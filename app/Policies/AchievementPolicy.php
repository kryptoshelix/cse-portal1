<?php

namespace App\Policies;

use App\Enums\AchievementStatus;
use App\Models\Achievement;
use App\Models\User;

class AchievementPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // scoped lists handled per role in controllers
    }

    /** Owner (or creator of an on-behalf submission) and admins can view. */
    public function view(User $user, Achievement $achievement): bool
    {
        return $user->id === $achievement->owner_user_id
            || $user->id === $achievement->created_by
            || $user->isAdmin()
            || ($user->canReviewAchievements() && $achievement->status !== AchievementStatus::Draft);
    }

    /** Create is allowed for every authenticated active user (portal routes gate role). */
    public function create(User $user): bool
    {
        return true;
    }

    /** Edit content only while draft or rejected, and only by owner/creator or admins. */
    public function update(User $user, Achievement $achievement): bool
    {
        if (! in_array($achievement->status, [AchievementStatus::Draft, AchievementStatus::Rejected], true)) {
            return false;
        }

        return $user->id === $achievement->owner_user_id
            || $user->id === $achievement->created_by
            || $user->isAdmin();
    }

    /** Submit for review: owner/creator while draft or rejected. */
    public function submit(User $user, Achievement $achievement): bool
    {
        return $this->update($user, $achievement);
    }

    public function delete(User $user, Achievement $achievement): bool
    {
        // Owners may delete their own drafts; admins may soft-delete anything.
        if ($user->isAdmin()) {
            return true;
        }

        return ($user->id === $achievement->owner_user_id || $user->id === $achievement->created_by)
            && $achievement->status === AchievementStatus::Draft;
    }

    public function review(User $user, Achievement $achievement): bool
    {
        return $user->canReviewAchievements() && $achievement->status === AchievementStatus::Pending;
    }

    public function publish(User $user, Achievement $achievement): bool
    {
        return $user->isAdmin() && $achievement->status === AchievementStatus::Approved;
    }
}
