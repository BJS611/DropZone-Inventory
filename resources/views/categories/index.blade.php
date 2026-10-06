<x-layouts.app :title="'Kategori — ' . config('app.name')">
    <x-page-header title="Kategori" :subtitle="'Master data kategori'">
        <x-slot:actions>
            @can('create', \App\Models\Category::class)
                <x-dz-button as="a" :href="route('categories.create')">+ Tambah Kategori</x-dz-button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <main class="space-y-6 p-6">
        <x-dz-alert tone="success" />
        <x-dz-alert tone="error" />

        <form method="GET" action="{{ route('categories.index') }}" class="dz-card p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-end">
                <div class="flex-1">
                    <label for="dz-cat-search" class="dz-label">Cari</label>
                    <input id="dz-cat-search" type="search" name="search" value="{{ request()->string('search')->toString() }}" placeholder="Nama atau kode" class="dz-input" />
                </div>
                <button type="submit" class="dz-btn dz-btn-primary">Filter</button>
            </div>
        </form>

        @if ($categories->isEmpty())
            <x-empty-state title="Belum ada kategori" description="Tambahkan kategori untuk mengelompokkan barang." />
        @else
            <div class="dz-card overflow-x-auto">
                <table class="dz-table min-w-[640px]">
                    <thead>
                        <tr><th>Nama</th><th>Kode</th><th>Jumlah Barang</th><th>Dibuat</th><th class="text-right">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td class="font-medium text-ink">{{ $category->name }}</td>
                                <td class="dz-mono text-ink-2">{{ $category->code }}</td>
                                <td class="dz-mono">{{ $category->items_count }}</td>
                                <td class="text-xs text-ink-3">{{ $category->created_at?->format('d M Y') }}</td>
                                <td>
                                    <div class="flex items-center justify-end gap-2">
                                        @can('update', $category)
                                            <a href="{{ route('categories.edit', $category) }}" class="dz-btn dz-btn-secondary dz-btn-sm">Edit</a>
                                        @endcan
                                        @can('delete', $category)
                                            <x-dz-confirm
                                                action="{{ route('categories.destroy', $category) }}"
                                                label="Hapus"
                                                title="Hapus Kategori"
                                                :message="'Kategori \"' . $category->name . '\" akan dihapus permanen.'"
                                            />
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-dz-pagination :paginator="$categories" />
        @endif
    </main>
</x-layouts.app>
