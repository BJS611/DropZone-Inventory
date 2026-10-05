<?php

namespace App\Enums;

enum ItemStatus: string
{
    case ACTIVE = 'ACTIVE';
    case INACTIVE = 'INACTIVE';
    case LOST = 'LOST';
    case DISPOSED = 'DISPOSED';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Aktif',
            self::INACTIVE => 'Nonaktif',
            self::LOST => 'Hilang',
            self::DISPOSED => 'Dibuang',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::ACTIVE => 'dz-badge-success',
            self::INACTIVE => 'dz-badge-neutral',
            self::LOST => 'dz-badge-warning',
            self::DISPOSED => 'dz-badge-error',
        };
    }
}
