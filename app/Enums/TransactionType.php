<?php

namespace App\Enums;

enum TransactionType: string
{
    case IN = 'IN';
    case OUT = 'OUT';
    case ADJUSTMENT = 'ADJUSTMENT';
    case TRANSFER = 'TRANSFER';
    case RETURN = 'RETURN';

    public function label(): string
    {
        return match ($this) {
            self::IN => 'Masuk',
            self::OUT => 'Keluar',
            self::ADJUSTMENT => 'Penyesuaian',
            self::TRANSFER => 'Transfer',
            self::RETURN => 'Pengembalian',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::IN => 'dz-badge-success',
            self::OUT => 'dz-badge-error',
            self::ADJUSTMENT => 'dz-badge-warning',
            self::TRANSFER => 'dz-badge-info',
            self::RETURN => 'dz-badge-info',
        };
    }
}
