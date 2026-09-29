<?php

namespace App\Enums;

enum AchievementStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Rejected = 'rejected';
    case Approved = 'approved';
    case Published = 'published';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /** Allowed transitions per docs/WORKFLOWS.md. */
    public function allowedTargets(): array
    {
        return match ($this) {
            self::Draft => [self::Pending],
            self::Pending => [self::Approved, self::Rejected],
            self::Rejected => [self::Pending],
            self::Approved => [self::Published],
            self::Published => [self::Approved],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTargets(), true);
    }
}
