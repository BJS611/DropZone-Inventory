<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function (): void {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function (): void {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Inventory
    Route::get('items', [ItemController::class, 'index'])->name('items.index');
    Route::get('items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('items', [ItemController::class, 'store'])->name('items.store');
    Route::get('items/{item}', [ItemController::class, 'show'])->name('items.show');
    Route::get('items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::put('items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
    Route::get('items-export', [ItemController::class, 'export'])->name('items.export');

    // Stock operations
    Route::post('items/{item}/stock-in', [ItemController::class, 'stockIn'])->name('items.stock-in');
    Route::post('items/{item}/stock-out', [ItemController::class, 'stockOut'])->name('items.stock-out');
    Route::post('items/{item}/adjust', [ItemController::class, 'adjust'])->name('items.adjust');
    Route::post('items/{item}/transfer', [ItemController::class, 'transfer'])->name('items.transfer');

    // Transactions
    Route::get('transactions', [StockTransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/{transaction}', [StockTransactionController::class, 'show'])->name('transactions.show');
    Route::get('transactions-export', [StockTransactionController::class, 'export'])->name('transactions.export');

    // Borrowings
    Route::get('borrowings', [BorrowingController::class, 'index'])->name('borrowings.index');
    Route::get('borrowings/create', [BorrowingController::class, 'create'])->name('borrowings.create');
    Route::post('borrowings', [BorrowingController::class, 'store'])->name('borrowings.store');
    Route::get('borrowings/{borrowing}', [BorrowingController::class, 'show'])->name('borrowings.show');
    Route::post('borrowings/{borrowing}/items/{borrowingItem}/return', [BorrowingController::class, 'returnItem'])
        ->name('borrowings.items.return');
    Route::post('borrowings/{borrowing}/cancel', [BorrowingController::class, 'cancel'])->name('borrowings.cancel');
    Route::get('borrowings-export', [BorrowingController::class, 'export'])->name('borrowings.export');

    // Categories
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Locations
    Route::get('locations', [LocationController::class, 'index'])->name('locations.index');
    Route::get('locations/create', [LocationController::class, 'create'])->name('locations.create');
    Route::post('locations', [LocationController::class, 'store'])->name('locations.store');
    Route::get('locations/{location}/edit', [LocationController::class, 'edit'])->name('locations.edit');
    Route::put('locations/{location}', [LocationController::class, 'update'])->name('locations.update');
    Route::delete('locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');

    // Suppliers
    Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('reports/low-stock', [ReportController::class, 'lowStock'])->name('reports.low-stock');
    Route::get('reports/transactions', [ReportController::class, 'transactions'])->name('reports.transactions');
    Route::get('reports/borrowings', [ReportController::class, 'borrowings'])->name('reports.borrowings');

    // Users (admin only)
    Route::middleware('can:viewAny,App\Models\User')->group(function (): void {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('users.toggle-status');
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])
            ->name('users.reset-password');
    });

    // Audit log (admin / staff)
    Route::middleware('can:viewAny,App\Models\AuditLog')->group(function (): void {
        Route::get('audit', [AuditLogController::class, 'index'])->name('audit.index');
        Route::get('audit/{auditLog}', [AuditLogController::class, 'show'])->name('audit.show');
    });
});

Route::get('/deploy/diag', function () {
    abort_if(request()->query('token') !== config('app.key'), 403);

    try {
        $tables = DB::select('SHOW TABLES');

        return response()->json([
            'status' => 'ok',
            'php' => PHP_VERSION,
            'db_ok' => true,
            'tables' => $tables,
            'cache_store' => config('cache.default'),
            'maintenance_driver' => config('app.maintenance.driver'),
            'session_driver' => config('session.driver'),
            'asset_url' => config('app.asset_url'),
        ]);
    } catch (Throwable $e) {
        return response()->json([
            'status' => 'error',
            'class' => $e::class,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
});

Route::get('/deploy/seed', function () {
    abort_if(request()->query('token') !== config('app.key'), 403);

    try {
        Artisan::call('db:seed', ['--force' => true]);

        return response()->json(['status' => 'ok', 'output' => Artisan::output()]);
    } catch (Throwable $e) {
        return response()->json(['status' => 'error', 'class' => $e::class, 'message' => $e->getMessage(), 'line' => $e->getLine()]);
    }
});
