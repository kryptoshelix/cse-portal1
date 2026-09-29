<?php

namespace Tests;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\Achievement;
use App\Models\AchievementCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function makeUser(UserRole $role, array $attributes = []): User
    {
        $user = User::forceCreate(array_merge([
            'name' => fake()->name(),
            'email' => str_replace('_', '-', $role->value).fake()->unique()->numberBetween(1000, 9999).'@test.local',
            'password' => 'Sup3rSecret!Pass',
            'role' => $role,
            'status' => AccountStatus::Active,
        ], $attributes));

        if ($role === UserRole::Student) {
            DB::table('students')->insert([
                'user_id' => $user->id,
                'roll_number' => 'CSE'.fake()->unique()->numberBetween(10000, 99999),
                'program' => 'B.Tech',
                'batch' => '2024-28',
                'public_display_consent' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $user->unsetRelation('student')->refresh();
        } elseif ($role === UserRole::Faculty) {
            DB::table('faculty')->insert([
                'user_id' => $user->id,
                'designation' => 'Assistant Professor',
                'specialization' => 'Computer Science',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $user->unsetRelation('facultyProfile')->refresh();
        }

        return $user;
    }

    protected function category(): AchievementCategory
    {
        return AchievementCategory::firstOrCreate(
            ['slug' => 'hackathons'],
            ['name' => 'Hackathons', 'description' => 'Sample category']
        );
    }

    /**
     * Create an achievement directly in the given workflow state (bypasses
     * transitions on purpose so tests can set up scenarios quickly).
     */
    protected function makeAchievement(User $owner, string $status = 'draft', array $extra = []): Achievement
    {
        $a = new Achievement(array_merge([
            'title' => 'Test Achievement '.Str::random(6),
            'description' => 'A test description long enough to pass validation.',
            'achievement_category_id' => $this->category()->id,
            'achievement_date' => now()->subDays(5)->toDateString(),
            'issuing_organization' => 'Sample Org',
            'level' => 'National',
            'public_display_consent' => true,
        ], $extra));

        // Ownership/status are privileged: set via forceFill, mirroring the
        // server-side assignment controllers perform after authorization.
        $a->forceFill([
            'owner_user_id' => $owner->id,
            'created_by' => $owner->id,
            'status' => $status,
        ])->save();

        return $a->fresh();
    }

    /**
     * Test helper: persist a model row with privileged columns (is_published,
     * published_at) that are intentionally NOT in $fillable. Mirrors the
     * forceFill usage inside controllers after authorization.
     */
    protected function force(\Illuminate\Database\Eloquent\Model $model, array $attributes): \Illuminate\Database\Eloquent\Model
    {
        $model->forceFill($attributes)->save();

        return $model;
    }
}
