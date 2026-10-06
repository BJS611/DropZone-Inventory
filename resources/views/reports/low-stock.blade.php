<x-layouts.app :title="'Laporan Low Stock — ' . config('app.name')">
    <x-page-header title="Low Stock Report" :subtitle="'Barang dengan stok di bawah atau sama dengan minimum'">
        <x-slot:actions>
            <a href="{{ route('reports.index') }}" class="dz-btn dz-btn-ghost">Kembali</a>
        </x-slot:actions>
    </x-page-header>

    <main class="space-y-6 p-6">
        <form method="GET" action="{{ route('reports.low-stock') }}" class="dz-card p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-end">
                <div class="md:w-56">
                    <label for="dz-ls-cat" class="dz-label">Kategori</label>
                    <select id="dz-ls-cat" name="category" class="dz-input">
                        <option value="">Semua</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(request()->string('category')->toString() === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="dz-btn dz-btn-primary">Filter</button>
            </div>
        </form>

        @if ($items->isEmpty())
            <x-empty-state title="Tidak ada barang low stock" description="Semua barang berada di atas batas minimum." />
        @else
            <div class="dz-card overflow-x-auto">
                <table class="dz-table min-w-[760px]">
                    <thead>
                        <tr><th>SKU</th><th>Nama</th><th>Lokasi</th><th>Stok</th><th>Minimum</th><th>Status</th><th class="text-right">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td class="dz-mono text-xs text-ink-2">{{ $item->sku }}</td>
                                <td class="font-medium text-ink">{{ $item->name }}</td>
                                <td class="text-ink-2">{{ $item->location?->name }}</td>
                                <td class="dz-mono">{{ $item->quantity }}</td>
                                <td class="dz-mono text-ink-2">{{ $item->minimum_stock }}</td>
                                <td><x-stock-badge :quantity="$item->quantity" :minimum="$item->minimum_stock" /></td>
                                <td class="text-right">
                                    @can('stockIn', $item)
                                        <a href="{{ route('items.show', $item) . '#dz-stock-in' }}" class="dz-btn dz-btn-primary dz-btn-sm">Stock In</a>
                                    @else
                                        <a href="{{ route('items.show', $item) }}" class="dz-btn dz-btn-ghost dz-btn-sm">Detail</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-dz-pagination :paginator="$items" />
        @endif
    </main>
</x-layouts.app>
