<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    /** Only admins manage accounts. */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function approve(User $actor): bool
    {
        return $actor->isAdmin();
    }

    /** Changing roles or the review flag is super-admin only. */
    public function manageRoles(User $actor): bool
    {
        return $actor->isSuperAdmin();
    }

    public function deactivate(User $actor, User $target): bool
    {
        if (! $actor->isAdmin()) {
            return false;
        }

        // Nobody can deactivate themselves through the list UI.
        if ($actor->id === $target->id) {
            return false;
        }

        // Department admins cannot touch super admins.
        if ($target->role === UserRole::SuperAdmin && ! $actor->isSuperAdmin()) {
            return false;
        }

        return true;
    }
}
