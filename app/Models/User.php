<?php

namespace App\Models;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * NOTE: `role`, `status`, `can_review_achievements` are intentionally NOT
     * fillable. They may only be changed through dedicated admin service code
     * (privilege-escalation protection / mass-assignment safety).
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => AccountStatus::class,
            'can_review_achievements' => 'boolean',
        ];
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function facultyProfile(): HasOne
    {
        return $this->hasOne(Faculty::class);
    }

    public function achievementsOwned()
    {
        return $this->hasMany(Achievement::class, 'owner_user_id');
    }

    public function achievementsCreated()
    {
        return $this->hasMany(Achievement::class, 'created_by');
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, [UserRole::DeptAdmin, UserRole::SuperAdmin], true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    public function isActive(): bool
    {
        return $this->status === AccountStatus::Active;
    }

    /** Whether this user may review (approve/reject) achievement submissions. */
    public function canReviewAchievements(): bool
    {
        return $this->isAdmin() || ($this->role === UserRole::Faculty && $this->can_review_achievements);
    }

    /** Dashboard home route for role-aware redirects. */
    public function dashboardRoute(): string
    {
        return match ($this->role) {
            UserRole::Student => 'student.dashboard',
            UserRole::Faculty => 'faculty.dashboard',
            UserRole::DeptAdmin, UserRole::SuperAdmin => 'admin.dashboard',
        };
    }
}
