<?php

namespace App\Http\Controllers;

use App\Models\StockTransaction;
use Illuminate\Http\Request;

class StockTransactionController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'string', 'in:IN,OUT,ADJUSTMENT,TRANSFER,RETURN'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $transactions = StockTransaction::query()
            ->with(['item.category', 'item.location', 'performedBy'])
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->whereHas('item', function ($item) use ($search): void {
                    $item->where('sku', 'like', '%'.$search.'%')
                        ->orWhere('name', 'like', '%'.$search.'%');
                });
            })
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

        return view('transactions.index', ['transactions' => $transactions]);
    }

    public function show(StockTransaction $transaction)
    {
        $transaction->load(['item.category', 'item.location', 'performedBy', 'fromLocation', 'toLocation']);

        return view('transactions.show', ['transaction' => $transaction]);
    }

    public function export(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'string', 'in:IN,OUT,ADJUSTMENT,TRANSFER,RETURN'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $transactions = StockTransaction::query()
            ->with(['item', 'performedBy'])
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->whereHas('item', function ($item) use ($search): void {
                    $item->where('sku', 'like', '%'.$search.'%')
                        ->orWhere('name', 'like', '%'.$search.'%');
                });
            })
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
            ->cursor();

        $filename = 'transactions-'.now()->format('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        return response()->stream(function () use ($transactions): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Tanggal', 'Tipe', 'Barang', 'Jumlah', 'Oleh', 'Catatan']);

            foreach ($transactions as $transaction) {
                fputcsv($handle, [
                    $transaction->created_at?->format('Y-m-d H:i'),
                    $transaction->type->value,
                    $transaction->item?->name,
                    $transaction->quantity,
                    $transaction->performedBy?->name,
                    $transaction->note,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
