<?php

use App\Enums\BorrowingStatus;
use App\Enums\UserStatus;
use App\Models\AuditLog;
use App\Models\Borrowing;
use App\Models\BorrowingItem;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use App\Services\BorrowingService;
use Illuminate\Support\Facades\Hash;

describe('borrowing', function (): void {
    it('creates a borrowing and decreases stock', function (): void {
        $item = Item::factory()->create(['quantity' => 20]);

        $this->actingAs(User::factory()->staff()->create())
            ->post('/borrowings', [
                'borrower_name' => 'Mahasiswa Test',
                'borrower_identifier' => 'TST001',
                'borrower_contact' => '0812',
                'purpose' => 'Praktikum',
                'expected_return_at' => now()->addDays(3)->format('Y-m-d'),
                'items' => [
                    [
                        'item_id' => $item->id,
                        'quantity' => 5,
                        'condition_before' => 'GOOD',
                    ],
                ],
            ])->assertRedirect();

        $borrowing = Borrowing::first();
        expect($borrowing)->not->toBeNull()
            ->and($borrowing->status)->toBe(BorrowingStatus::BORROWED)
            ->and($item->fresh()->quantity)->toBe(15)
            ->and($borrowing->items()->count())->toBe(1);
    });

    it('rejects borrowing more than available stock', function (): void {
        $item = Item::factory()->create(['quantity' => 2]);

        $this->actingAs(User::factory()->staff()->create())
            ->post('/borrowings', [
                'borrower_name' => 'Test',
                'expected_return_at' => now()->addDay()->format('Y-m-d'),
                'items' => [
                    ['item_id' => $item->id, 'quantity' => 99, 'condition_before' => 'GOOD'],
                ],
            ])->assertSessionHasErrors();

        expect($item->fresh()->quantity)->toBe(2)
            ->and(Borrowing::count())->toBe(0);
    });

    it('requires at least one item', function (): void {
        $this->actingAs(User::factory()->staff()->create())
            ->post('/borrowings', [
                'borrower_name' => 'Test',
                'expected_return_at' => now()->addDay()->format('Y-m-d'),
                'items' => [],
            ])->assertSessionHasErrors();
    });

    it('shows borrowing detail', function (): void {
        $item = Item::factory()->create();
        $borrowing = Borrowing::factory()->create();
        BorrowingItem::factory()->create([
            'borrowing_id' => $borrowing->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs(User::factory()->admin()->create())
            ->get("/borrowings/{$borrowing->id}")
            ->assertOk()
            ->assertViewHas('borrowing');
    });

    it('lists borrowings', function (): void {
        $item = Item::factory()->create();
        $borrowing = Borrowing::factory()->create();
        BorrowingItem::factory()->create([
            'borrowing_id' => $borrowing->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs(User::factory()->admin()->create())
            ->get('/borrowings')
            ->assertOk()
            ->assertViewHas('borrowings');
    });
});

describe('returns', function (): void {
    it('handles full return', function (): void {
        $item = Item::factory()->create(['quantity' => 20]);
        $borrowing = Borrowing::factory()->create();
        $bi = BorrowingItem::factory()->create([
            'borrowing_id' => $borrowing->id,
            'item_id' => $item->id,
            'quantity' => 5,
            'returned_quantity' => 0,
        ]);

        $this->actingAs(User::factory()->staff()->create())
            ->post("/borrowings/{$borrowing->id}/items/{$bi->id}/return", [
                'quantity' => 5,
                'condition_after' => 'GOOD',
            ])->assertRedirect();

        expect($bi->fresh()->returned_quantity)->toBe(5)
            ->and($item->fresh()->quantity)->toBe(25)
            ->and($borrowing->fresh()->status)->toBe(BorrowingStatus::RETURNED);
    });

    it('handles partial return', function (): void {
        $item = Item::factory()->create(['quantity' => 20]);
        $borrowing = Borrowing::factory()->create();
        $bi = BorrowingItem::factory()->create([
            'borrowing_id' => $borrowing->id,
            'item_id' => $item->id,
            'quantity' => 5,
            'returned_quantity' => 0,
        ]);

        $this->actingAs(User::factory()->staff()->create())
            ->post("/borrowings/{$borrowing->id}/items/{$bi->id}/return", [
                'quantity' => 2,
                'condition_after' => 'MINOR_DAMAGE',
            ])->assertRedirect();

        expect($bi->fresh()->returned_quantity)->toBe(2)
            ->and($item->fresh()->quantity)->toBe(22)
            ->and($borrowing->fresh()->status)->toBe(BorrowingStatus::PARTIALLY_RETURNED);
    });

    it('rejects returning more than borrowed', function (): void {
        $item = Item::factory()->create(['quantity' => 20]);
        $borrowing = Borrowing::factory()->create();
        $bi = BorrowingItem::factory()->create([
            'borrowing_id' => $borrowing->id,
            'item_id' => $item->id,
            'quantity' => 5,
            'returned_quantity' => 0,
        ]);

        $this->actingAs(User::factory()->staff()->create())
            ->post("/borrowings/{$borrowing->id}/items/{$bi->id}/return", [
                'quantity' => 99,
                'condition_after' => 'GOOD',
            ])->assertSessionHasErrors();

        expect($bi->fresh()->returned_quantity)->toBe(0)
            ->and($item->fresh()->quantity)->toBe(20);
    });

    it('rejects return with invalid condition', function (): void {
        $item = Item::factory()->create();
        $borrowing = Borrowing::factory()->create();
        $bi = BorrowingItem::factory()->create([
            'borrowing_id' => $borrowing->id,
            'item_id' => $item->id,
            'quantity' => 5,
        ]);

        $this->actingAs(User::factory()->staff()->create())
            ->post("/borrowings/{$borrowing->id}/items/{$bi->id}/return", [
                'quantity' => 1,
                'condition_after' => 'INVALID',
            ])->assertSessionHasErrors('condition_after');
    });
});

describe('overdue', function (): void {
    it('marks past-due borrowings as overdue', function (): void {
        $item = Item::factory()->create();
        $borrowing = Borrowing::factory()->create([
            'expected_return_at' => now()->subDays(5),
            'status' => BorrowingStatus::BORROWED,
        ]);
        BorrowingItem::factory()->create([
            'borrowing_id' => $borrowing->id,
            'item_id' => $item->id,
            'quantity' => 2,
            'returned_quantity' => 0,
        ]);

        app(BorrowingService::class)->refreshOverdue();

        expect($borrowing->fresh()->status)->toBe(BorrowingStatus::OVERDUE);
    });
});

describe('user management', function (): void {
    it('admin can list users', function (): void {
        User::factory()->count(3)->create();

        $this->actingAs(User::factory()->admin()->create())
            ->get('/users')
            ->assertOk()
            ->assertViewHas('users');
    });

    it('admin can create a user', function (): void {
        $this->actingAs(User::factory()->admin()->create())
            ->post('/users', [
                'name' => 'New User',
                'email' => 'newuser@test.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'STAFF',
            ])->assertRedirect();

        $user = User::firstWhere('email', 'newuser@test.com');
        expect($user)->not->toBeNull()
            ->and($user->role->value)->toBe('STAFF')
            ->and(Hash::check('password123', $user->password))->toBeTrue();
    });

    it('validates user creation', function (): void {
        $this->actingAs(User::factory()->admin()->create())
            ->post('/users', [])
            ->assertSessionHasErrors(['name', 'email', 'password', 'role']);
    });

    it('admin can update a user', function (): void {
        $user = User::factory()->staff()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->put("/users/{$user->id}", [
                'name' => 'Renamed User',
                'email' => $user->email,
                'role' => 'ADMIN',
            ])->assertRedirect();

        expect($user->fresh()->name)->toBe('Renamed User')
            ->and($user->fresh()->role->value)->toBe('ADMIN');
    });

    it('admin can deactivate a user', function (): void {
        $user = User::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->post("/users/{$user->id}/toggle-status")
            ->assertRedirect();

        expect($user->fresh()->status->value)->toBe('INACTIVE');
    });

    it('prevents deactivating the last admin', function (): void {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post("/users/{$admin->id}/toggle-status")
            ->assertForbidden();

        expect($admin->fresh()->status->value)->toBe('ACTIVE');
    });

    it('prevents deactivating the last remaining active admin', function (): void {
        $admin = User::factory()->admin()->create();
        $other = User::factory()->admin()->create();

        $other->update(['status' => UserStatus::INACTIVE]);

        $this->actingAs($admin)
            ->post("/users/{$other->id}/toggle-status")
            ->assertRedirect();

        expect($other->fresh()->status->value)->toBe('ACTIVE');
    });

    it('admin can reset a password', function (): void {
        $user = User::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->post("/users/{$user->id}/reset-password", [
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ])->assertRedirect();

        expect(Hash::check('newpassword123', $user->fresh()->password))->toBeTrue();
    });
});

describe('audit log', function (): void {
    it('records stock operations', function (): void {
        $item = Item::factory()->create(['quantity' => 5]);
        $user = User::factory()->staff()->create();

        $this->actingAs($user)
            ->post("/items/{$item->id}/stock-in", ['quantity' => 3, 'note' => 'test'])
            ->assertRedirect();

        expect(AuditLog::where('action', 'stock_in')->exists())->toBeTrue();
    });

    it('records item creation', function (): void {
        $category = Category::factory()->create();
        $location = Location::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->post('/items', [
                'sku' => 'DZ-AUDIT-001',
                'name' => 'Audit Item',
                'category_id' => $category->id,
                'location_id' => $location->id,
                'quantity' => 1,
                'minimum_stock' => 1,
                'unit' => 'PCS',
                'condition' => 'GOOD',
                'status' => 'ACTIVE',
            ])->assertRedirect();

        expect(AuditLog::where('action', 'item_create')->exists())->toBeTrue();
    });

    it('records borrowing creation', function (): void {
        $item = Item::factory()->create(['quantity' => 10]);

        $this->actingAs(User::factory()->staff()->create())
            ->post('/borrowings', [
                'borrower_name' => 'Audit Test',
                'expected_return_at' => now()->addDay()->format('Y-m-d'),
                'items' => [
                    ['item_id' => $item->id, 'quantity' => 1, 'condition_before' => 'GOOD'],
                ],
            ])->assertRedirect();

        expect(AuditLog::where('action', 'borrowing_create')->exists())->toBeTrue();
    });

    it('lists audit logs for admin', function (): void {
        AuditLog::factory()->count(3)->create();

        $this->actingAs(User::factory()->admin()->create())
            ->get('/audit')
            ->assertOk()
            ->assertViewHas('logs');
    });
});
