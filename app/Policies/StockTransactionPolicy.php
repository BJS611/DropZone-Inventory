<?php

namespace App\Policies;

use App\Models\StockTransaction;
use App\Models\User;

class StockTransactionPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, StockTransaction $transaction): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('stock-in') || $user->can('stock-out') || $user->can('stock-adjust');
    }
}
