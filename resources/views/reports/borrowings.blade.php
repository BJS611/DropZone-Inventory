<x-layouts.app :title="'Laporan Peminjaman — ' . config('app.name')">
    <x-page-header title="Borrowing Report" :subtitle="'Riwayat peminjaman dan pengembalian'">
        <x-slot:actions>
            <a href="{{ route('reports.index') }}" class="dz-btn dz-btn-ghost">Kembali</a>
            <x-dz-button as="a" :href="route('borrowings.export', request()->query())" variant="secondary">Export CSV</x-dz-button>
        </x-slot:actions>
    </x-page-header>

    <main class="space-y-6 p-6">
        <form method="GET" action="{{ route('reports.borrowings') }}" class="dz-card p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-end">
                <div class="md:w-52">
                    <label for="dz-rb-status" class="dz-label">Status</label>
                    <select id="dz-rb-status" name="status" class="dz-input">
                        <option value="">Semua</option>
                        @foreach (\App\Enums\BorrowingStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(request()->string('status')->toString() === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:w-44">
                    <label for="dz-rb-from" class="dz-label">Dari</label>
                    <input id="dz-rb-from" type="date" name="from" value="{{ request()->string('from')->toString() }}" class="dz-input" />
                </div>
                <div class="md:w-44">
                    <label for="dz-rb-to" class="dz-label">Sampai</label>
                    <input id="dz-rb-to" type="date" name="to" value="{{ request()->string('to')->toString() }}" class="dz-input" />
                </div>
                <button type="submit" class="dz-btn dz-btn-primary">Filter</button>
            </div>
        </form>

        @if ($borrowings->isEmpty())
            <x-empty-state title="Tidak ada peminjaman" description="Belum ada peminjaman yang cocok dengan filter." />
        @else
            <div class="dz-card overflow-x-auto">
                <table class="dz-table min-w-[900px]">
                    <thead>
                        <tr><th>Peminjam</th><th>Barang</th><th>Jumlah</th><th>Dipinjam</th><th>Tenggat</th><th>Dikembalikan</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($borrowings as $borrowing)
                            @foreach ($borrowing->items as $item)
                                <tr>
                                    <td class="font-medium text-ink">{{ $borrowing->borrower_name }}</td>
                                    <td class="text-ink-2">{{ $item->item?->name }}</td>
                                    <td class="dz-mono">{{ $item->quantity }}</td>
                                    <td class="text-xs text-ink-3">{{ $borrowing->borrowed_at?->format('d M Y') }}</td>
                                    <td class="text-xs text-ink-3">{{ $borrowing->expected_return_at?->format('d M Y') }}</td>
                                    <td class="text-xs text-ink-3">{{ $borrowing->returned_at?->format('d M Y') }}</td>
                                    <td><span class="dz-badge {{ $borrowing->status->badgeClass() }}">{{ $borrowing->status->label() }}</span></td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-dz-pagination :paginator="$borrowings" />
        @endif
    </main>
</x-layouts.app>
