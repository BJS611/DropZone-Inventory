<x-layouts.app :title="'Laporan Inventory — ' . config('app.name')">
    <x-page-header title="Inventory Report" :subtitle="'Seluruh barang inventaris'">
        <x-slot:actions>
            <a href="{{ route('reports.index') }}" class="dz-btn dz-btn-ghost">Kembali</a>
            <x-dz-button as="a" :href="route('items.export', request()->query())" variant="secondary">Export CSV</x-dz-button>
        </x-slot:actions>
    </x-page-header>

    <main class="space-y-6 p-6">
        <form method="GET" action="{{ route('reports.inventory') }}" class="dz-card p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-end">
                <div class="md:w-56">
                    <label for="dz-rp-cat" class="dz-label">Kategori</label>
                    <select id="dz-rp-cat" name="category" class="dz-input">
                        <option value="">Semua</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(request()->string('category')->toString() === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:w-56">
                    <label for="dz-rp-loc" class="dz-label">Lokasi</label>
                    <select id="dz-rp-loc" name="location" class="dz-input">
                        <option value="">Semua</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}" @selected(request()->string('location')->toString() === $location->id)>{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="dz-btn dz-btn-primary">Filter</button>
            </div>
        </form>

        @if ($items->isEmpty())
            <x-empty-state title="Tidak ada data" description="Belum ada barang yang cocok dengan filter." />
        @else
            <div class="dz-card overflow-x-auto">
                <table class="dz-table min-w-[900px]">
                    <thead>
                        <tr><th>SKU</th><th>Nama</th><th>Kategori</th><th>Lokasi</th><th>Stok</th><th>Minimum</th><th>Kondisi</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td class="dz-mono text-xs text-ink-2">{{ $item->sku }}</td>
                                <td class="font-medium text-ink">{{ $item->name }}</td>
                                <td class="text-ink-2">{{ $item->category?->name }}</td>
                                <td class="text-ink-2">{{ $item->location?->name }}</td>
                                <td class="dz-mono">{{ $item->quantity }}</td>
                                <td class="dz-mono text-ink-2">{{ $item->minimum_stock }}</td>
                                <td><span class="dz-badge {{ $item->condition->badgeClass() }}">{{ $item->condition->label() }}</span></td>
                                <td><x-stock-badge :quantity="$item->quantity" :minimum="$item->minimum_stock" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-dz-pagination :paginator="$items" />
        @endif
    </main>
</x-layouts.app>
