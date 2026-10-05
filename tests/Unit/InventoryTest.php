<?php

use App\Enums\BorrowingStatus;
use App\Enums\ItemCondition;
use App\Enums\ItemStatus;
use App\Enums\Role;
use App\Enums\TransactionType;
use App\Enums\Unit;
use App\Models\Borrowing;
use App\Models\BorrowingItem;
use App\Models\Item;

describe('stock status', function (): void {
    it('classifies out of stock', function (): void {
        $item = Item::factory()->create(['quantity' => 0, 'minimum_stock' => 5]);
        expect($item->stockStatus())->toBe('OUT_OF_STOCK')
            ->and($item->isOutOfStock())->toBeTrue();
    });

    it('classifies low stock', function (): void {
        $item = Item::factory()->create(['quantity' => 3, 'minimum_stock' => 5]);
        expect($item->stockStatus())->toBe('LOW')
            ->and($item->isLowStock())->toBeTrue();
    });

    it('classifies low stock at exact boundary', function (): void {
        $item = Item::factory()->create(['quantity' => 5, 'minimum_stock' => 5]);
        expect($item->stockStatus())->toBe('LOW');
    });

    it('classifies normal stock', function (): void {
        $item = Item::factory()->create(['quantity' => 20, 'minimum_stock' => 5]);
        expect($item->stockStatus())->toBe('NORMAL')
            ->and($item->isLowStock())->toBeFalse()
            ->and($item->isOutOfStock())->toBeFalse();
    });
});

describe('item scopes', function (): void {
    it('filters low stock items', function (): void {
        Item::factory()->create(['quantity' => 0, 'minimum_stock' => 5]);
        Item::factory()->create(['quantity' => 3, 'minimum_stock' => 5]);
        Item::factory()->create(['quantity' => 20, 'minimum_stock' => 5]);

        expect(Item::lowStock()->count())->toBe(1)
            ->and(Item::outOfStock()->count())->toBe(1);
    });
});

describe('borrowing states', function (): void {
    it('tracks remaining quantity', function (): void {
        $item = Item::factory()->create(['quantity' => 10]);
        $borrowing = Borrowing::factory()->create();
        $bi = BorrowingItem::factory()->create([
            'borrowing_id' => $borrowing->id,
            'item_id' => $item->id,
            'quantity' => 4,
            'returned_quantity' => 1,
        ]);

        expect($bi->remainingQuantity())->toBe(3)
            ->and($bi->isFullyReturned())->toBeFalse();
    });

    it('detects fully returned', function (): void {
        $item = Item::factory()->create();
        $borrowing = Borrowing::factory()->create();
        $bi = BorrowingItem::factory()->create([
            'borrowing_id' => $borrowing->id,
            'item_id' => $item->id,
            'quantity' => 4,
            'returned_quantity' => 4,
        ]);

        expect($bi->remainingQuantity())->toBe(0)
            ->and($bi->isFullyReturned())->toBeTrue();
    });

    it('computes total quantity', function (): void {
        $item = Item::factory()->create();
        $borrowing = Borrowing::factory()->create();
        BorrowingItem::factory()->create([
            'borrowing_id' => $borrowing->id, 'item_id' => $item->id, 'quantity' => 3,
        ]);
        BorrowingItem::factory()->create([
            'borrowing_id' => $borrowing->id, 'item_id' => $item->id, 'quantity' => 5,
        ]);

        expect($borrowing->fresh()->totalQuantity())->toBe(8);
    });
});

describe('role permissions', function (): void {
    it('grants admin everything', function (): void {
        expect(Role::ADMIN->can('manage-users'))->toBeTrue()
            ->and(Role::ADMIN->can('stock-in'))->toBeTrue()
            ->and(Role::ADMIN->can('delete'))->toBeTrue();
    });

    it('denies staff user management', function (): void {
        expect(Role::STAFF->can('manage-users'))->toBeFalse()
            ->and(Role::STAFF->can('stock-in'))->toBeTrue()
            ->and(Role::STAFF->can('delete-limited'))->toBeTrue();
    });

    it('makes viewer read-only', function (): void {
        expect(Role::VIEWER->can('view-any'))->toBeTrue()
            ->and(Role::VIEWER->can('create'))->toBeFalse()
            ->and(Role::VIEWER->can('stock-in'))->toBeFalse();
    });
});

describe('enums', function (): void {
    it('has required transaction types', function (): void {
        $values = array_map(fn ($t) => $t->value, TransactionType::cases());

        expect(TransactionType::cases())
            ->toHaveCount(5)
            ->and($values)
            ->toContain('IN', 'OUT', 'ADJUSTMENT', 'TRANSFER', 'RETURN');
    });

    it('has required borrowing statuses', function (): void {
        expect(BorrowingStatus::cases())->toHaveCount(6);
    });

    it('has required item values', function (): void {
        expect(ItemCondition::cases())->toHaveCount(3)
            ->and(ItemStatus::cases())->toHaveCount(4)
            ->and(Unit::cases())->toHaveCount(5);
    });
});
