<x-layouts.app :title="'Supplier — ' . config('app.name')">
    <x-page-header title="Supplier" :subtitle="'Master data supplier'">
        <x-slot:actions>
            @can('create', \App\Models\Supplier::class)
                <x-dz-button as="a" :href="route('suppliers.create')">+ Tambah Supplier</x-dz-button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <main class="space-y-6 p-6">
        <x-dz-alert tone="success" />
        <x-dz-alert tone="error" />

        <form method="GET" action="{{ route('suppliers.index') }}" class="dz-card p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-end">
                <div class="flex-1">
                    <label for="dz-sup-search" class="dz-label">Cari</label>
                    <input id="dz-sup-search" type="search" name="search" value="{{ request()->string('search')->toString() }}" placeholder="Nama, kontak, atau email" class="dz-input" />
                </div>
                <button type="submit" class="dz-btn dz-btn-primary">Filter</button>
            </div>
        </form>

        @if ($suppliers->isEmpty())
            <x-empty-state title="Belum ada supplier" description="Tambahkan supplier untuk melacak asal barang." />
        @else
            <div class="dz-card overflow-x-auto">
                <table class="dz-table min-w-[760px]">
                    <thead>
                        <tr><th>Supplier</th><th>Contact</th><th>Phone</th><th>Email</th><th>Item Count</th><th class="text-right">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($suppliers as $supplier)
                            <tr>
                                <td class="font-medium text-ink">{{ $supplier->name }}</td>
                                <td class="text-ink-2">{{ $supplier->contact_name }}</td>
                                <td class="dz-mono text-xs text-ink-2">{{ $supplier->phone }}</td>
                                <td class="text-ink-2">{{ $supplier->email }}</td>
                                <td class="dz-mono">{{ $supplier->items_count }}</td>
                                <td>
                                    <div class="flex items-center justify-end gap-2">
                                        @can('update', $supplier)
                                            <a href="{{ route('suppliers.edit', $supplier) }}" class="dz-btn dz-btn-secondary dz-btn-sm">Edit</a>
                                        @endcan
                                        @can('delete', $supplier)
                                            <x-dz-confirm
                                                action="{{ route('suppliers.destroy', $supplier) }}"
                                                label="Hapus"
                                                title="Hapus Supplier"
                                                :message="'Supplier \"' . $supplier->name . '\" akan dihapus permanen.'"
                                            />
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-dz-pagination :paginator="$suppliers" />
        @endif
    </main>
</x-layouts.app>
