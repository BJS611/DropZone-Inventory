<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'ADMIN';
    case STAFF = 'STAFF';
    case VIEWER = 'VIEWER';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::STAFF => 'Staff',
            self::VIEWER => 'Viewer',
        };
    }

    /**
     * Permissions granted to each role.
     *
     * @return array<int, string>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::ADMIN => [
                'view-any',
                'create', 'update', 'delete',
                'stock-in', 'stock-out', 'stock-adjust', 'stock-transfer',
                'borrow', 'return',
                'manage-categories', 'manage-locations', 'manage-suppliers',
                'manage-users', 'view-audit', 'view-reports', 'export',
            ],
            self::STAFF => [
                'view-any',
                'create', 'update', 'delete-limited',
                'stock-in', 'stock-out', 'stock-adjust', 'stock-transfer',
                'borrow', 'return',
                'manage-categories', 'manage-locations', 'manage-suppliers',
                'view-audit', 'view-reports', 'export',
            ],
            self::VIEWER => [
                'view-any', 'view-reports', 'export',
            ],
        };
    }

    public function can(string $ability): bool
    {
        return in_array($ability, $this->permissions(), true);
    }
}
