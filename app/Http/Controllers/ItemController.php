<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Http\Requests\StockAdjustmentRequest;
use App\Http\Requests\StockInRequest;
use App\Http\Requests\StockOutRequest;
use App\Http\Requests\StockTransferRequest;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\Supplier;
use App\Services\AuditService;
use App\Services\StockService;
use App\Support\ItemQuery;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ItemController extends Controller
{
    public function __construct(
        private AuditService $audit,
        private StockService $stocks,
        private ItemQuery $itemQuery,
    ) {}

    /**
     * Daftar barang dengan pencarian, filter, sorting, dan pagination.
     */
    public function index(Request $request)
    {
        $request->validate($this->itemQuery->validationRules());

        $items = $this->itemQuery
            ->build($request)
            ->paginate($this->itemQuery->perPage($request))
            ->withQueryString();

        return view('inventory.index', [
            'items' => $items,
            'categories' => Category::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'sort' => $this->itemQuery->build($request)->getQuery()->orders[0]['column'] ?? 'updated_at',
        ]);
    }

    public function create()
    {
        $this->authorize('create', Item::class);

        return view('inventory.create', [
            'categories' => Category::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
        ]);
    }

    public function store(StoreItemRequest $request)
    {
        $data = $request->validatedData();

        $item = \DB::transaction(function () use ($data, $request): Item {
            $item = Item::create($data);

            if ($data['quantity'] > 0) {
                $this->stocks->record($item, TransactionType::IN, [
                    'quantity' => $data['quantity'],
                    'to_location_id' => $item->location_id,
                    'note' => 'Stok awal',
                    'performed_by' => $request->user()?->id,
                ]);
            }

            $this->audit->log(
                'item_create',
                'Item',
                $item->id,
                ['sku' => $item->sku, 'name' => $item->name, 'quantity' => $data['quantity']],
                $request->user(),
            );

            return $item;
        });

        return redirect()
            ->route('items.show', $item)
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function show(Item $item)
    {
        $item->load([
            'category',
            'location',
            'supplier',
            'stockTransactions' => fn ($query) => $query->latest('created_at')->limit(20),
            'stockTransactions.item',
            'stockTransactions.performedBy',
            'borrowingItems' => fn ($query) => $query->latest()->limit(20),
            'borrowingItems.borrowing',
        ]);

        return view('inventory.show', ['item' => $item]);
    }

    public function edit(Item $item)
    {
        return view('inventory.edit', [
            'item' => $item,
            'categories' => Category::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        $data = $request->validatedData();

        \DB::transaction(function () use ($item, $data, $request): void {
            $item->update($data);

            $this->audit->log(
                'item_update',
                'Item',
                $item->id,
                ['name' => $item->name],
                $request->user(),
            );
        });

        return redirect()
            ->route('items.show', $item)
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Request $request, Item $item)
    {
        $this->authorize('delete', $item);

        \DB::transaction(function () use ($item, $request): void {
            $this->audit->log(
                'item_delete',
                'Item',
                $item->id,
                ['sku' => $item->sku, 'name' => $item->name],
                $request->user(),
            );

            $item->delete();
        });

        return redirect()
            ->route('items.index')
            ->with('success', 'Barang berhasil dihapus.');
    }

    public function stockIn(StockInRequest $request, Item $item)
    {
        $transaction = $this->stocks->stockIn($item, $request->validated(), $request->user());

        return redirect()
            ->route('items.show', $item)
            ->with('success', sprintf('Stok masuk %d %s berhasil dicatat.', $transaction->quantity, $item->unit->value));
    }

    public function stockOut(StockOutRequest $request, Item $item)
    {
        $transaction = $this->stocks->stockOut($item, $request->validated(), $request->user());

        return redirect()
            ->route('items.show', $item)
            ->with('success', sprintf('Stok keluar %d %s berhasil dicatat.', $transaction->quantity, $item->unit->value));
    }

    public function adjust(StockAdjustmentRequest $request, Item $item)
    {
        $transaction = $this->stocks->adjust($item, $request->validated(), $request->user());

        return redirect()
            ->route('items.show', $item)
            ->with('success', sprintf('Stok disesuaikan menjadi %d.', $item->fresh()->quantity));
    }

    public function transfer(StockTransferRequest $request, Item $item)
    {
        $transaction = $this->stocks->transfer($item, $request->validated(), $request->user());

        return redirect()
            ->route('items.show', $item)
            ->with('success', 'Transfer lokasi berhasil dicatat.');
    }

    /**
     * Ekspor CSV barang mengikuti filter aktif.
     */
    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Item::class);

        $request->validate($this->itemQuery->validationRules());

        $items = $this->itemQuery->build($request)->with(['category', 'location'])->cursor();

        $filename = 'inventory-'.now()->format('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        return response()->stream(function () use ($items): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['SKU', 'Nama', 'Kategori', 'Lokasi', 'Stok', 'Minimum', 'Kondisi', 'Status']);

            foreach ($items as $item) {
                fputcsv($handle, [
                    $item->sku,
                    $item->name,
                    $item->category?->name,
                    $item->location?->name,
                    $item->quantity,
                    $item->minimum_stock,
                    $item->condition->value,
                    $item->status->value,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
