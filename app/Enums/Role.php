<?php

namespace App\Enums;

enum Role: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case User = 'user';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::User => 'Siswa',
        };
    }

    /**
     * Tailwind classes for the role badge.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::SuperAdmin => 'bg-rose-100 text-rose-700 ring-rose-200',
            self::Admin => 'bg-indigo-100 text-indigo-700 ring-indigo-200',
            self::User => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
        };
    }

    public function canManage(): bool
    {
        return $this === self::SuperAdmin || $this === self::Admin;
    }
}
