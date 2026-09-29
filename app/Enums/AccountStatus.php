<?php

namespace App\Enums;

enum AccountStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Rejected = 'rejected';
    case Deactivated = 'deactivated';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
