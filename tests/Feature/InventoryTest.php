<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

describe('authentication', function (): void {
    it('shows the login page', function (): void {
        $this->get('/login')->assertOk()->assertViewIs('auth.login');
    });

    it('logs in an active user', function (): void {
        $user = User::factory()->admin()->create(['password' => Hash::make('secret123')]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ])->assertRedirect('/dashboard');

        expect(auth()->check())->toBeTrue();
    });

    it('rejects invalid credentials', function (): void {
        $user = User::factory()->admin()->create();

        $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertRedirect('/login');

        expect(auth()->check())->toBeFalse();
    });

    it('requires email and password', function (): void {
        $this->post('/login', [])->assertSessionHasErrors(['email', 'password']);
    });

    it('logs out', function (): void {
        $this->actingAs(User::factory()->admin()->create())
            ->post('/logout')
            ->assertRedirect('/');

        expect(auth()->check())->toBeFalse();
    });

    it('redirects guests to login', function (): void {
        $this->get('/dashboard')->assertRedirect('/login');
    });
});

describe('inactive user blocking', function (): void {
    it('prevents login for inactive users', function (): void {
        $user = User::factory()->inactive()->create(['password' => Hash::make('secret123')]);

        $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ])->assertRedirect('/login');

        expect(auth()->check())->toBeFalse();
    });
});

describe('dashboard', function (): void {
    it('shows dashboard stats', function (): void {
        Item::factory()->count(3)->create();
        Item::factory()->lowStock()->create();
        Item::factory()->outOfStock()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->get('/dashboard')
            ->assertOk()
            ->assertViewHas(['totalItems', 'totalStock', 'lowStockCount', 'outOfStockCount']);
    });
});

describe('inventory crud', function (): void {
    it('lists items paginated', function (): void {
        Item::factory()->count(25)->create();

        $this->actingAs(User::factory()->admin()->create())
            ->get('/items')
            ->assertOk()
            ->assertViewHas('items');

        expect(Item::count())->toBe(25);
    });

    it('creates an item with initial stock transaction', function (): void {
        $category = Category::factory()->create();
        $location = Location::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->post('/items', [
                'sku' => 'DZ-TEST-001',
                'name' => 'Test Item',
                'description' => 'desc',
                'category_id' => $category->id,
                'location_id' => $location->id,
                'supplier_id' => null,
                'quantity' => 10,
                'minimum_stock' => 2,
                'unit' => 'PCS',
                'condition' => 'GOOD',
                'status' => 'ACTIVE',
            ])->assertRedirect();

        $item = Item::firstWhere('sku', 'DZ-TEST-001');
        expect($item)->not->toBeNull()
            ->and($item->quantity)->toBe(10);

        expect($item->stockTransactions()->where('type', 'IN')->count())->toBe(1);
    });

    it('validates required fields', function (): void {
        $this->actingAs(User::factory()->admin()->create())
            ->post('/items', [])
            ->assertSessionHasErrors(['sku', 'name', 'category_id', 'location_id', 'unit', 'condition', 'status']);
    });

    it('enforces unique sku', function (): void {
        Item::factory()->create(['sku' => 'DZ-DUP-001']);

        $this->actingAs(User::factory()->admin()->create())
            ->post('/items', [
                'sku' => 'DZ-DUP-001',
                'name' => 'Dup',
                'category_id' => Category::factory()->create()->id,
                'location_id' => Location::factory()->create()->id,
                'quantity' => 1,
                'minimum_stock' => 1,
                'unit' => 'PCS',
                'condition' => 'GOOD',
                'status' => 'ACTIVE',
            ])->assertSessionHasErrors('sku');
    });

    it('updates an item without changing quantity', function (): void {
        $item = Item::factory()->create(['quantity' => 7]);

        $this->actingAs(User::factory()->admin()->create())
            ->put("/items/{$item->id}", [
                'name' => 'Renamed',
                'description' => $item->description,
                'category_id' => $item->category_id,
                'location_id' => $item->location_id,
                'supplier_id' => $item->supplier_id,
                'minimum_stock' => $item->minimum_stock,
                'unit' => $item->unit->value,
                'condition' => $item->condition->value,
                'status' => $item->status->value,
                'image_url' => $item->image_url,
            ])->assertRedirect();

        expect($item->fresh()->name)->toBe('Renamed')
            ->and($item->fresh()->quantity)->toBe(7);
    });

    it('deletes an item', function (): void {
        $item = Item::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->delete("/items/{$item->id}")
            ->assertRedirect();

        expect($item->fresh())->toBeNull();
    });

    it('searches by sku and name', function (): void {
        Item::factory()->create(['sku' => 'DZ-FIND-001', 'name' => 'Keyboard']);
        Item::factory()->create(['sku' => 'DZ-OTHER-002', 'name' => 'Monitor']);

        $response = $this->actingAs(User::factory()->admin()->create())
            ->get('/items?search=Keyboard')
            ->assertOk();

        expect($response->viewData('items')->total())->toBe(1);
    });
});

describe('stock operations', function (): void {
    it('increases stock on stock in', function (): void {
        $item = Item::factory()->create(['quantity' => 5]);

        $this->actingAs(User::factory()->staff()->create())
            ->post("/items/{$item->id}/stock-in", [
                'quantity' => 5,
                'note' => 'procurement',
            ])->assertRedirect();

        expect($item->fresh()->quantity)->toBe(10);
    });

    it('decreases stock on stock out', function (): void {
        $item = Item::factory()->create(['quantity' => 10]);

        $this->actingAs(User::factory()->staff()->create())
            ->post("/items/{$item->id}/stock-out", [
                'quantity' => 4,
                'note' => 'usage',
            ])->assertRedirect();

        expect($item->fresh()->quantity)->toBe(6);
    });

    it('rejects stock out that would go negative', function (): void {
        $item = Item::factory()->create(['quantity' => 2]);

        $this->actingAs(User::factory()->staff()->create())
            ->post("/items/{$item->id}/stock-out", [
                'quantity' => 99,
                'note' => 'too much',
            ])->assertSessionHasErrors('quantity');

        expect($item->fresh()->quantity)->toBe(2);
    });

    it('adjusts stock to a new value', function (): void {
        $item = Item::factory()->create(['quantity' => 10]);

        $this->actingAs(User::factory()->staff()->create())
            ->post("/items/{$item->id}/adjust", [
                'new_stock' => 15,
                'note' => 'opname',
            ])->assertRedirect();

        expect($item->fresh()->quantity)->toBe(15);
    });

    it('transfers stock between locations', function (): void {
        $from = Location::factory()->create();
        $to = Location::factory()->create();
        $item = Item::factory()->create(['quantity' => 10, 'location_id' => $from->id]);

        $this->actingAs(User::factory()->staff()->create())
            ->post("/items/{$item->id}/transfer", [
                'quantity' => 4,
                'from_location_id' => $from->id,
                'to_location_id' => $to->id,
                'note' => 'move',
            ])->assertRedirect();

        expect($item->fresh()->location_id)->toBe($to->id);
    });
});

describe('category crud', function (): void {
    it('lists categories', function (): void {
        Category::factory()->count(3)->create();

        $this->actingAs(User::factory()->admin()->create())
            ->get('/categories')
            ->assertOk()
            ->assertViewHas('categories');
    });

    it('creates a category', function (): void {
        $this->actingAs(User::factory()->admin()->create())
            ->post('/categories', [
                'name' => 'Test Category',
                'code' => 'TST',
                'description' => 'desc',
            ])->assertRedirect();

        expect(Category::where('code', 'TST')->exists())->toBeTrue();
    });

    it('enforces unique code', function (): void {
        Category::factory()->create(['code' => 'DUP']);

        $this->actingAs(User::factory()->admin()->create())
            ->post('/categories', ['name' => 'X', 'code' => 'DUP'])
            ->assertSessionHasErrors('code');
    });

    it('updates a category', function (): void {
        $category = Category::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->put("/categories/{$category->id}", [
                'name' => 'Renamed',
                'code' => $category->code,
                'description' => $category->description,
            ])->assertRedirect();

        expect($category->fresh()->name)->toBe('Renamed');
    });

    it('deletes a category', function (): void {
        $category = Category::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->delete("/categories/{$category->id}")
            ->assertRedirect();

        expect($category->fresh())->toBeNull();
    });
});

describe('location crud', function (): void {
    it('creates a location', function (): void {
        $this->actingAs(User::factory()->admin()->create())
            ->post('/locations', [
                'name' => 'Lab Test',
                'code' => 'LAB-T',
                'description' => 'desc',
            ])->assertRedirect();

        expect(Location::where('code', 'LAB-T')->exists())->toBeTrue();
    });

    it('deletes a location', function (): void {
        $location = Location::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->delete("/locations/{$location->id}")
            ->assertRedirect();

        expect($location->fresh())->toBeNull();
    });
});

describe('supplier crud', function (): void {
    it('creates a supplier', function (): void {
        $this->actingAs(User::factory()->admin()->create())
            ->post('/suppliers', [
                'name' => 'PT Test',
                'contact_name' => 'Budi',
                'phone' => '0812',
                'email' => 'budi@test.com',
                'address' => 'Jl. Test',
            ])->assertRedirect();

        expect(Supplier::where('name', 'PT Test')->exists())->toBeTrue();
    });

    it('lists suppliers', function (): void {
        Supplier::factory()->count(3)->create();

        $this->actingAs(User::factory()->admin()->create())
            ->get('/suppliers')
            ->assertOk()
            ->assertViewHas('suppliers');
    });
});

describe('permissions', function (): void {
    it('blocks viewer from creating items', function (): void {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/items/create')
            ->assertForbidden();

        $this->actingAs(User::factory()->viewer()->create())
            ->post('/items', [])
            ->assertForbidden();
    });

    it('blocks viewer from stock operations', function (): void {
        $item = Item::factory()->create();

        $this->actingAs(User::factory()->viewer()->create())
            ->post("/items/{$item->id}/stock-in", ['quantity' => 1])
            ->assertForbidden();
    });

    it('blocks staff from user management', function (): void {
        $this->actingAs(User::factory()->staff()->create())
            ->get('/users')
            ->assertForbidden();
    });

    it('blocks viewer from user management', function (): void {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/users')
            ->assertForbidden();
    });

    it('allows viewer to read inventory', function (): void {
        Item::factory()->create();

        $this->actingAs(User::factory()->viewer()->create())
            ->get('/items')
            ->assertOk();
    });
});

describe('reports and export', function (): void {
    it('shows the reports index', function (): void {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/reports')
            ->assertOk();
    });

    it('exports items as csv', function (): void {
        Item::factory()->count(3)->create();

        $response = $this->actingAs(User::factory()->admin()->create())
            ->get('/items-export');

        $response->assertOk();
        expect($response->headers->get('content-type'))->toContain('text/csv');
    });

    it('exports transactions as csv', function (): void {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/transactions-export')
            ->assertOk();
    });
});
