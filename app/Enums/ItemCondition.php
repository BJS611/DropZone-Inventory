<?php

namespace App\Enums;

enum ItemCondition: string
{
    case GOOD = 'GOOD';
    case MINOR_DAMAGE = 'MINOR_DAMAGE';
    case DAMAGED = 'DAMAGED';

    public function label(): string
    {
        return match ($this) {
            self::GOOD => 'Baik',
            self::MINOR_DAMAGE => 'Rusak Ringan',
            self::DAMAGED => 'Rusak Berat',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::GOOD => 'dz-badge-success',
            self::MINOR_DAMAGE => 'dz-badge-warning',
            self::DAMAGED => 'dz-badge-error',
        };
    }
}
