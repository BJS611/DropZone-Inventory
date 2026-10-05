<?php

namespace App\Policies;

use App\Enums\ItemStatus;
use App\Models\Item;
use App\Models\User;

class ItemPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Item $item): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('create');
    }

    public function update(User $user, Item $item): bool
    {
        return $user->can('update');
    }

    public function delete(User $user, Item $item): bool
    {
        // STAFF hanya boleh menghapus barang yang rusak berat / dibuang.
        if ($user->isAdmin()) {
            return true;
        }

        return $user->can('delete-limited')
            && in_array($item->status, [ItemStatus::LOST, ItemStatus::DISPOSED], true);
    }

    public function stockIn(User $user, Item $item): bool
    {
        return $user->can('stock-in');
    }

    public function stockOut(User $user, Item $item): bool
    {
        return $user->can('stock-out');
    }

    public function adjust(User $user, Item $item): bool
    {
        return $user->can('stock-adjust');
    }

    public function transfer(User $user, Item $item): bool
    {
        return $user->can('stock-transfer');
    }
}
