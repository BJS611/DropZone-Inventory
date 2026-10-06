<x-layouts.app :title="'Lokasi — ' . config('app.name')">
    <x-page-header title="Lokasi" :subtitle="'Master data lokasi'">
        <x-slot:actions>
            @can('create', \App\Models\Location::class)
                <x-dz-button as="a" :href="route('locations.create')">+ Tambah Lokasi</x-dz-button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <main class="space-y-6 p-6">
        <x-dz-alert tone="success" />
        <x-dz-alert tone="error" />

        <form method="GET" action="{{ route('locations.index') }}" class="dz-card p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-end">
                <div class="flex-1">
                    <label for="dz-loc-search" class="dz-label">Cari</label>
                    <input id="dz-loc-search" type="search" name="search" value="{{ request()->string('search')->toString() }}" placeholder="Nama atau kode" class="dz-input" />
                </div>
                <button type="submit" class="dz-btn dz-btn-primary">Filter</button>
            </div>
        </form>

        @if ($locations->isEmpty())
            <x-empty-state title="Belum ada lokasi" description="Tambahkan lokasi untuk mengetahui posisi barang." />
        @else
            <div class="dz-card overflow-x-auto">
                <table class="dz-table min-w-[640px]">
                    <thead>
                        <tr><th>Nama</th><th>Kode</th><th>Jumlah Barang</th><th class="text-right">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($locations as $location)
                            <tr>
                                <td class="font-medium text-ink">{{ $location->name }}</td>
                                <td class="dz-mono text-ink-2">{{ $location->code }}</td>
                                <td class="dz-mono">{{ $location->items_count }}</td>
                                <td>
                                    <div class="flex items-center justify-end gap-2">
                                        @can('update', $location)
                                            <a href="{{ route('locations.edit', $location) }}" class="dz-btn dz-btn-secondary dz-btn-sm">Edit</a>
                                        @endcan
                                        @can('delete', $location)
                                            <x-dz-confirm
                                                action="{{ route('locations.destroy', $location) }}"
                                                label="Hapus"
                                                title="Hapus Lokasi"
                                                :message="'Lokasi \"' . $location->name . '\" akan dihapus permanen.'"
                                            />
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-dz-pagination :paginator="$locations" />
        @endif
    </main>
</x-layouts.app>
