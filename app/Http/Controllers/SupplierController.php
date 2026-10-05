<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Services\AuditService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct(private AuditService $audit) {}

    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $suppliers = Supplier::query()
            ->withCount('items')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('contact_name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('suppliers.index', ['suppliers' => $suppliers]);
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(StoreSupplierRequest $request)
    {
        $supplier = \DB::transaction(function () use ($request): Supplier {
            $supplier = Supplier::create($request->validated());

            $this->audit->log(
                'supplier_create',
                'Supplier',
                $supplier->id,
                ['name' => $supplier->name],
                $request->user(),
            );

            return $supplier;
        });

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', ['supplier' => $supplier]);
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        \DB::transaction(function () use ($supplier, $request): void {
            $supplier->update($request->validated());

            $this->audit->log(
                'supplier_update',
                'Supplier',
                $supplier->id,
                ['name' => $supplier->name],
                $request->user(),
            );
        });

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Request $request, Supplier $supplier)
    {
        $this->authorize('delete', $supplier);

        \DB::transaction(function () use ($supplier, $request): void {
            $this->audit->log(
                'supplier_delete',
                'Supplier',
                $supplier->id,
                ['name' => $supplier->name],
                $request->user(),
            );

            $supplier->delete();
        });

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
