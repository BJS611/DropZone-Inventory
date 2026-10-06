<x-layouts.app :title="'Peminjaman — ' . config('app.name')">
    <x-page-header title="Borrowings" :subtitle="'Daftar peminjaman barang'">
        <x-slot:actions>
            <x-dz-button as="a" :href="route('borrowings.export')" variant="secondary">Export CSV</x-dz-button>
            @can('create', \App\Models\Borrowing::class)
                <x-dz-button as="a" :href="route('borrowings.create')">+ Buat Peminjaman</x-dz-button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <main class="space-y-6 p-6">
        <x-dz-alert tone="success" />
        <x-dz-alert tone="error" />

        <form method="GET" action="{{ route('borrowings.index') }}" class="dz-card p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-end">
                <div class="flex-1">
                    <label for="dz-brw-search" class="dz-label">Cari</label>
                    <input id="dz-brw-search" type="search" name="search" value="{{ request()->string('search')->toString() }}" placeholder="Nama atau ID peminjam" class="dz-input" />
                </div>
                <div class="md:w-52">
                    <label for="dz-brw-status" class="dz-label">Status</label>
                    <select id="dz-brw-status" name="status" class="dz-input">
                        <option value="">Semua</option>
                        @foreach (\App\Enums\BorrowingStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(request()->string('status')->toString() === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="dz-btn dz-btn-primary">Filter</button>
            </div>
        </form>

        @if ($borrowings->isEmpty())
            <x-empty-state title="Belum ada peminjaman" description="Buat peminjaman untuk meminjam barang dari inventaris." />
        @else
            <div class="dz-card overflow-x-auto">
                <table class="dz-table min-w-[760px]">
                    <thead>
                        <tr><th>Peminjam</th><th>Barang</th><th>Jumlah</th><th>Dipinjam</th><th>Tenggat</th><th>Status</th><th class="text-right">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($borrowings as $borrowing)
                            <tr>
                                <td class="font-medium text-ink">{{ $borrowing->borrower_name }}</td>
                                <td>
                                    @foreach ($borrowing->items as $item)
                                        <div class="text-ink-2">{{ $item->item?->name }} <span class="dz-mono text-xs text-ink-3">({{ $item->quantity }})</span></div>
                                    @endforeach
                                </td>
                                <td class="dz-mono">{{ $borrowing->totalQuantity() }}</td>
                                <td class="text-xs text-ink-3">{{ $borrowing->borrowed_at?->format('d M Y') }}</td>
                                <td class="text-xs text-ink-3">{{ $borrowing->expected_return_at?->format('d M Y') }}</td>
                                <td><span class="dz-badge {{ $borrowing->status->badgeClass() }}">{{ $borrowing->status->label() }}</span></td>
                                <td>
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('borrowings.show', $borrowing) }}" class="dz-btn dz-btn-ghost dz-btn-sm">Detail</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-dz-pagination :paginator="$borrowings" />
        @endif
    </main>
</x-layouts.app>
