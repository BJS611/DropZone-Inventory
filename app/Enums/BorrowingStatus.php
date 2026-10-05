<?php

namespace App\Enums;

enum BorrowingStatus: string
{
    case PENDING = 'PENDING';
    case BORROWED = 'BORROWED';
    case PARTIALLY_RETURNED = 'PARTIALLY_RETURNED';
    case RETURNED = 'RETURNED';
    case OVERDUE = 'OVERDUE';
    case CANCELLED = 'CANCELLED';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu',
            self::BORROWED => 'Dipinjam',
            self::PARTIALLY_RETURNED => 'Dikembalikan Sebagian',
            self::RETURNED => 'Dikembalikan',
            self::OVERDUE => 'Terlambat',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'dz-badge-warning',
            self::BORROWED => 'dz-badge-info',
            self::PARTIALLY_RETURNED => 'dz-badge-warning',
            self::RETURNED => 'dz-badge-success',
            self::OVERDUE => 'dz-badge-error',
            self::CANCELLED => 'dz-badge-neutral',
        };
    }
}
