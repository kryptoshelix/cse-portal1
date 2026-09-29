<?php

namespace App\Services;

use App\Enums\AchievementStatus;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * The ONLY place allowed to change achievement status. Every transition is
 * validated against AchievementStatus::allowedTargets() and audit-logged.
 */
class AchievementWorkflowService
{
    public function __construct(private AuditLogger $audit)
    {
    }

    public function submit(Achievement $achievement, User $actor): Achievement
    {
        return DB::transaction(function () use ($achievement, $actor) {
            $this->assertTransition($achievement, AchievementStatus::Pending);
            // Only the owner (or an admin acting on it via review screens which use other methods) submits.
            if ($achievement->owner_user_id !== $actor->id && ! $actor->isAdmin()) {
                abort(403);
            }

            $achievement->forceFill([
                'status' => AchievementStatus::Pending,
                'submitted_at' => now(),
            ])->save();

            // Clear stale rejection feedback once back in queue.
            $achievement->forceFill(['rejection_feedback' => null])->save();

            $this->audit->log('achievement.submitted', "Submission #{$achievement->id} sent for review", $achievement);

            return $achievement;
        });
    }

    public function approve(Achievement $achievement, User $reviewer): Achievement
    {
        return DB::transaction(function () use ($achievement, $reviewer) {
            $this->assertReviewer($reviewer, $achievement);
            $this->assertTransition($achievement, AchievementStatus::Approved);

            // Prevent self-review of one's own submission unless super admin.
            if ($achievement->owner_user_id === $reviewer->id && ! $reviewer->isSuperAdmin()) {
                throw ValidationException::withMessages([
                    'achievement' => 'You cannot review your own submission.',
                ]);
            }

            $achievement->forceFill([
                'status' => AchievementStatus::Approved,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'approved_at' => now(),
                'rejection_feedback' => null,
            ])->save();

            $this->audit->log('achievement.approved', "Submission #{$achievement->id} approved", $achievement);

            return $achievement;
        });
    }

    public function reject(Achievement $achievement, User $reviewer, string $feedback): Achievement
    {
        return DB::transaction(function () use ($achievement, $reviewer, $feedback) {
            $this->assertReviewer($reviewer, $achievement);
            $this->assertTransition($achievement, AchievementStatus::Rejected);

            if (mb_strlen(trim($feedback)) < 10) {
                throw ValidationException::withMessages([
                    'rejection_feedback' => 'Rejection requires meaningful reviewer feedback (at least 10 characters).',
                ]);
            }

            if ($achievement->owner_user_id === $reviewer->id && ! $reviewer->isSuperAdmin()) {
                throw ValidationException::withMessages([
                    'achievement' => 'You cannot review your own submission.',
                ]);
            }

            $achievement->forceFill([
                'status' => AchievementStatus::Rejected,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'rejection_feedback' => trim($feedback),
            ])->save();

            $this->audit->log('achievement.rejected', "Submission #{$achievement->id} rejected", $achievement);

            return $achievement;
        });
    }

    public function publish(Achievement $achievement, User $actor): Achievement
    {
        return DB::transaction(function () use ($achievement, $actor) {
            $this->assertAdmin($actor);
            $this->assertTransition($achievement, AchievementStatus::Published);

            $achievement->forceFill([
                'status' => AchievementStatus::Published,
                'published_at' => now(),
            ])->save();

            $this->audit->log('achievement.published', "Achievement #{$achievement->id} published publicly", $achievement);

            return $achievement;
        });
    }

    public function unpublish(Achievement $achievement, User $actor): Achievement
    {
        return DB::transaction(function () use ($achievement, $actor) {
            $this->assertAdmin($actor);
            $this->assertTransition($achievement, AchievementStatus::Approved);

            $achievement->forceFill([
                'status' => AchievementStatus::Approved,
                'published_at' => null,
            ])->save();

            $this->audit->log('achievement.unpublished', "Achievement #{$achievement->id} unpublished", $achievement);

            return $achievement;
        });
    }

    private function assertTransition(Achievement $achievement, AchievementStatus $target): void
    {
        $current = $achievement->status;
        if (! $current->canTransitionTo($target)) {
            $this->audit->log('achievement.invalid_transition',
                "Attempted illegal transition {$current->value} -> {$target->value}", $achievement);
            abort(403, 'That status change is not allowed at this point in the workflow.');
        }
    }

    private function assertReviewer(User $user, Achievement $achievement): void
    {
        if (! $user->canReviewAchievements()) {
            $this->audit->log('achievement.review_denied', "Unauthorized review attempt on #{$achievement->id}", $achievement);
            abort(403);
        }
    }

    private function assertAdmin(User $user): void
    {
        if (! $user->isAdmin()) {
            abort(403);
        }
    }
}
