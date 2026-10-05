<?php

namespace App\Http\Controllers;

use App\Enums\BorrowingStatus;
use App\Models\Borrowing;
use App\Models\Item;
use App\Models\StockTransaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $lowStockLimit = 5;
        $recentLimit = 5;

        $totalItems = Item::count();
        $totalStock = (int) Item::sum('quantity');
        $lowStockCount = Item::lowStock()->count();
        $outOfStockCount = Item::outOfStock()->count();
        $borrowedCount = Borrowing::query()
            ->whereIn('status', [BorrowingStatus::BORROWED, BorrowingStatus::PARTIALLY_RETURNED, BorrowingStatus::OVERDUE])
            ->count();

        $recentTransactions = StockTransaction::query()
            ->with(['item', 'performedBy'])
            ->latest('created_at')
            ->limit($recentLimit)
            ->get();

        $recentBorrowings = Borrowing::query()
            ->latest('updated_at')
            ->limit($recentLimit)
            ->get();

        $lowStockItems = Item::lowStock()->limit($lowStockLimit)->get();
        $outOfStockItems = Item::outOfStock()->limit($lowStockLimit)->get();
        $recentItems = Item::query()->latest('created_at')->limit($recentLimit)->get();

        return view('dashboard.index', [
            'totalItems' => $totalItems,
            'totalStock' => $totalStock,
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
            'borrowedCount' => $borrowedCount,
            'recentTransactions' => $recentTransactions,
            'recentBorrowings' => $recentBorrowings,
            'lowStockItems' => $lowStockItems,
            'outOfStockItems' => $outOfStockItems,
            'recentItems' => $recentItems,
        ]);
    }
}
