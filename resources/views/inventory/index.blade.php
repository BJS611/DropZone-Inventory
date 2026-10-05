<x-layouts.app :title="'Inventory — ' . config('app.name')">
    <x-page-header title="Inventory" :subtitle="'Daftar barang inventaris'">
        <x-slot:actions>
            <x-dz-button as="a" :href="route('items.export')" variant="secondary">Export CSV</x-dz-button>
            @can('create', \App\Models\Item::class)
                <x-dz-button as="a" :href="route('items.create')">+ Tambah Barang</x-dz-button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <main class="space-y-6 p-6">
        <x-dz-alert tone="success" />
        <x-dz-alert tone="error" />

        {{-- Filter bar --}}
        <form method="GET" :action="route('items.index')" class="dz-card p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-end">
                <div class="flex-1">
                    <label for="dz-search" class="dz-label">Cari</label>
                    <input
                        id="dz-search"
                        type="search"
                        name="search"
                        value="{{ request()->string('search')->toString() }}"
                        placeholder="SKU atau nama barang"
                        class="dz-input"
                    />
                </div>
                <div class="md:w-48">
                    <label for="dz-f-category" class="dz-label">Kategori</label>
                    <select id="dz-f-category" name="category" class="dz-input">
                        <option value="">Semua</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(request()->string('category')->toString() === $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:w-48">
                    <label for="dz-f-location" class="dz-label">Lokasi</label>
                    <select id="dz-f-location" name="location" class="dz-input">
                        <option value="">Semua</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}" @selected(request()->string('location')->toString() === $location->id)>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:w-44">
                    <label for="dz-f-stock" class="dz-label">Status Stok</label>
                    <select id="dz-f-stock" name="stock_status" class="dz-input">
                        <option value="">Semua</option>
                        <option value="NORMAL" @selected(request()->string('stock_status')->toString() === 'NORMAL')>Normal</option>
                        <option value="LOW" @selected(request()->string('stock_status')->toString() === 'LOW')>Low Stock</option>
                        <option value="OUT_OF_STOCK" @selected(request()->string('stock_status')->toString() === 'OUT_OF_STOCK')>Out Of Stock</option>
                    </select>
                </div>
                <div class="md:w-40">
                    <label for="dz-f-perpage" class="dz-label">Per Halaman</label>
                    <select id="dz-f-perpage" name="per_page" class="dz-input">
                        @foreach ([10, 20, 50, 100] as $value)
                            <option value="{{ $value }}" @selected((int) request()->input('per_page', 20) === $value)>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="dz-btn dz-btn-primary">Filter</button>
                    <a href="{{ route('items.index') }}" class="dz-btn dz-btn-ghost">Reset</a>
                </div>
            </div>
        </form>

        {{-- Table --}}
        @if ($items->isEmpty())
            <x-empty-state
                title="Belum ada barang"
                description="Tambahkan barang pertama untuk mulai mengelola inventaris."
            >
                <x-slot:actions>
                    @can('create', \App\Models\Item::class)
                        <x-dz-button as="a" :href="route('items.create')">+ Tambah Barang</x-dz-button>
                    @endcan
                </x-slot>
            </x-empty-state>
        @else
            <div class="dz-card overflow-x-auto">
                <table class="dz-table min-w-[900px]">
                    <thead>
                        <tr>
                            <x-dz-sortable label="SKU" column="sku" :current="$sort" :direction="request()->string('direction')->toString()" />
                            <x-dz-sortable label="Nama Barang" column="name" :current="$sort" :direction="request()->string('direction')->toString()" />
                            <th>Kategori</th>
                            <th>Lokasi</th>
                            <x-dz-sortable label="Stok" column="quantity" :current="$sort" :direction="request()->string('direction')->toString()" />
                            <th>Minimum</th>
                            <th>Kondisi</th>
                            <th>Status</th>
                            <x-dz-sortable label="Updated" column="updated_at" :current="$sort" :direction="request()->string('direction')->toString()" />
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td class="dz-mono text-xs text-ink-2">{{ $item->sku }}</td>
                                <td>
                                    <a href="{{ route('items.show', $item) }}" class="font-medium text-ink hover:text-secondary">
                                        {{ $item->name }}
                                    </a>
                                </td>
                                <td class="text-ink-2">{{ $item->category?->name }}</td>
                                <td class="text-ink-2">{{ $item->location?->name }}</td>
                                <td class="dz-mono text-ink">{{ $item->quantity }}</td>
                                <td class="dz-mono text-ink-2">{{ $item->minimum_stock }}</td>
                                <td><span class="dz-badge {{ $item->condition->badgeClass() }}">{{ $item->condition->label() }}</span></td>
                                <td><span class="dz-badge {{ $item->status->badgeClass() }}">{{ $item->status->label() }}</span></td>
                                <td class="text-xs text-ink-3">{{ $item->updated_at?->format('d M Y') }}</td>
                                <td>
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('items.show', $item) }}" class="dz-btn dz-btn-ghost dz-btn-sm">Detail</a>
                                        @can('update', $item)
                                            <a href="{{ route('items.edit', $item) }}" class="dz-btn dz-btn-secondary dz-btn-sm">Edit</a>
                                        @endcan
                                    </div>
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
