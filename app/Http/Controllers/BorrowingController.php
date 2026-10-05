<?php

namespace App\Http\Controllers;

use App\Enums\ItemCondition;
use App\Http\Requests\ReturnItemRequest;
use App\Http\Requests\StoreBorrowingRequest;
use App\Models\Borrowing;
use App\Models\Item;
use App\Services\BorrowingService;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    public function __construct(private BorrowingService $borrowings) {}

    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'in:PENDING,BORROWED,PARTIALLY_RETURNED,RETURNED,OVERDUE,CANCELLED'],
        ]);

        $borrowings = Borrowing::query()
            ->with(['items.item', 'createdBy'])
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where('borrower_name', 'like', '%'.$search.'%')
                    ->orWhere('borrower_identifier', 'like', '%'.$search.'%');
            })
            ->when($request->filled('status'), function ($query) use ($request): void {
                $query->where('status', $request->string('status')->toString());
            })
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('borrowings.index', ['borrowings' => $borrowings]);
    }

    public function create()
    {
        return view('borrowings.create', [
            'items' => Item::where('quantity', '>', 0)->orderBy('name')->get(),
            'conditions' => ItemCondition::cases(),
        ]);
    }

    public function store(StoreBorrowingRequest $request)
    {
        $borrowing = $this->borrowings->create($request->validatedData(), $request->user());

        return redirect()
            ->route('borrowings.show', $borrowing)
            ->with('success', 'Peminjaman berhasil dibuat.');
    }

    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['items.item.category', 'items.item.location', 'createdBy', 'approvedBy']);

        return view('borrowings.show', ['borrowing' => $borrowing]);
    }

    public function returnItem(ReturnItemRequest $request, Borrowing $borrowing, string $borrowingItem)
    {
        $borrowingItem = $borrowing->items()->findOrFail($borrowingItem);

        $this->borrowings->returnItem($borrowingItem, $request->validatedData(), $request->user());

        return redirect()
            ->route('borrowings.show', $borrowing)
            ->with('success', 'Pengembalian berhasil dicatat.');
    }

    public function cancel(Request $request, Borrowing $borrowing)
    {
        $this->authorize('cancel', $borrowing);

        $this->borrowings->cancel($borrowing, $request->user());

        return redirect()
            ->route('borrowings.show', $borrowing)
            ->with('success', 'Peminjaman berhasil dibatalkan.');
    }

    public function export(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'in:PENDING,BORROWED,PARTIALLY_RETURNED,RETURNED,OVERDUE,CANCELLED'],
        ]);

        $borrowings = Borrowing::query()
            ->with(['items.item'])
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where('borrower_name', 'like', '%'.$search.'%')
                    ->orWhere('borrower_identifier', 'like', '%'.$search.'%');
            })
            ->when($request->filled('status'), function ($query) use ($request): void {
                $query->where('status', $request->string('status')->toString());
            })
            ->latest('updated_at')
            ->cursor();

        $filename = 'borrowings-'.now()->format('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        return response()->stream(function () use ($borrowings): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Peminjam', 'Barang', 'Jumlah', 'Dipinjam', 'Tenggat', 'Dikembalikan', 'Status']);

            foreach ($borrowings as $borrowing) {
                foreach ($borrowing->items as $item) {
                    fputcsv($handle, [
                        $borrowing->borrower_name,
                        $item->item?->name,
                        $item->quantity,
                        $borrowing->borrowed_at?->format('Y-m-d'),
                        $borrowing->expected_return_at?->format('Y-m-d'),
                        $borrowing->returned_at?->format('Y-m-d'),
                        $borrowing->status->value,
                    ]);
                }
            }

            fclose($handle);
        }, 200, $headers);
    }
}
