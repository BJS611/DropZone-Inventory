<?php

namespace App\Http\Controllers;

use App\Enums\BorrowingStatus;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\StockTransaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index', [
            'totalItems' => Item::count(),
            'lowStock' => Item::lowStock()->count(),
            'outOfStock' => Item::outOfStock()->count(),
            'transactions' => StockTransaction::count(),
            'activeBorrowings' => Borrowing::query()
                ->whereIn('status', [BorrowingStatus::BORROWED, BorrowingStatus::PARTIALLY_RETURNED, BorrowingStatus::OVERDUE])
                ->count(),
        ]);
    }

    public function inventory(Request $request)
    {
        $request->validate([
            'category' => ['nullable', 'string', 'uuid'],
            'location' => ['nullable', 'string', 'uuid'],
            'stock_status' => ['nullable', 'string', 'in:NORMAL,LOW,OUT_OF_STOCK'],
        ]);

        $items = $this->inventoryQuery($request)->paginate(20)->withQueryString();

        return view('reports.inventory', [
            'items' => $items,
            'categories' => Category::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
        ]);
    }

    public function lowStock(Request $request)
    {
        $request->validate([
            'category' => ['nullable', 'string', 'uuid'],
            'location' => ['nullable', 'string', 'uuid'],
        ]);

        $items = $this->inventoryQuery($request)
            ->where(function ($query): void {
                $query->where('quantity', 0)
                    ->orWhereColumn('quantity', '<=', 'minimum_stock');
            })
            ->paginate(20)
            ->withQueryString();

        return view('reports.low-stock', [
            'items' => $items,
            'categories' => Category::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
        ]);
    }

    public function transactions(Request $request)
    {
        $request->validate([
            'type' => ['nullable', 'string', 'in:IN,OUT,ADJUSTMENT,TRANSFER,RETURN'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $transactions = StockTransaction::query()
            ->with(['item', 'performedBy'])
            ->when($request->filled('type'), function ($query) use ($request): void {
                $query->where('type', $request->string('type')->toString());
            })
            ->when($request->filled('from'), function ($query) use ($request): void {
                $query->whereDate('created_at', '>=', $request->string('from')->toString());
            })
            ->when($request->filled('to'), function ($query) use ($request): void {
                $query->whereDate('created_at', '<=', $request->string('to')->toString());
            })
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('reports.transactions', ['transactions' => $transactions]);
    }

    public function borrowings(Request $request)
    {
        $request->validate([
            'status' => ['nullable', 'string', 'in:PENDING,BORROWED,PARTIALLY_RETURNED,RETURNED,OVERDUE,CANCELLED'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $borrowings = Borrowing::query()
            ->with(['items.item'])
            ->when($request->filled('status'), function ($query) use ($request): void {
                $query->where('status', $request->string('status')->toString());
            })
            ->when($request->filled('from'), function ($query) use ($request): void {
                $query->whereDate('borrowed_at', '>=', $request->string('from')->toString());
            })
            ->when($request->filled('to'), function ($query) use ($request): void {
                $query->whereDate('borrowed_at', '<=', $request->string('to')->toString());
            })
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('reports.borrowings', ['borrowings' => $borrowings]);
    }

    protected function inventoryQuery(Request $request)
    {
        return Item::query()
            ->with(['category', 'location'])
            ->when($request->filled('category'), function ($query) use ($request): void {
                $query->where('category_id', $request->string('category')->toString());
            })
            ->when($request->filled('location'), function ($query) use ($request): void {
                $query->where('location_id', $request->string('location')->toString());
            })
            ->when($request->filled('stock_status'), function ($query) use ($request): void {
                $status = $request->string('stock_status')->toString();
                $query->when($status === 'NORMAL', fn ($inner) => $inner->whereColumn('quantity', '>', 'minimum_stock'))
                    ->when($status === 'LOW', fn ($inner) => $inner->where('quantity', '>', 0)->whereColumn('quantity', '<=', 'minimum_stock'))
                    ->when($status === 'OUT_OF_STOCK', fn ($inner) => $inner->where('quantity', 0));
            })
            ->orderBy('name');
    }
}
