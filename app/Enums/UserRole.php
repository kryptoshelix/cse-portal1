<?php

namespace App\Enums;

enum UserRole: string
{
    case Student = 'student';
    case Faculty = 'faculty';
    case DeptAdmin = 'dept_admin';
    case SuperAdmin = 'super_admin';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function registrable(): array
    {
        // Public registration may ONLY ever produce these roles.
        return [self::Student->value, self::Faculty->value];
    }

    public static function adminRoles(): array
    {
        return [self::DeptAdmin->value, self::SuperAdmin->value];
    }

    public function label(): string
    {
        return match ($this) {
            self::Student => 'Student',
            self::Faculty => 'Faculty',
            self::DeptAdmin => 'Department Administrator',
            self::SuperAdmin => 'Super Administrator',
        };
    }
}
