<x-layouts.app :title="'Laporan Transaksi — ' . config('app.name')">
    <x-page-header title="Transaction Report" :subtitle="'Riwayat transaksi stok'">
        <x-slot:actions>
            <a href="{{ route('reports.index') }}" class="dz-btn dz-btn-ghost">Kembali</a>
            <x-dz-button as="a" :href="route('transactions.export', request()->query())" variant="secondary">Export CSV</x-dz-button>
        </x-slot:actions>
    </x-page-header>

    <main class="space-y-6 p-6">
        <form method="GET" :action="route('reports.transactions')" class="dz-card p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-end">
                <div class="md:w-48">
                    <label for="dz-rt-type" class="dz-label">Tipe</label>
                    <select id="dz-rt-type" name="type" class="dz-input">
                        <option value="">Semua</option>
                        @foreach (\App\Enums\TransactionType::cases() as $type)
                            <option value="{{ $type->value }}" @selected(request()->string('type')->toString() === $type->value)>{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:w-44">
                    <label for="dz-rt-from" class="dz-label">Dari</label>
                    <input id="dz-rt-from" type="date" name="from" value="{{ request()->string('from')->toString() }}" class="dz-input" />
                </div>
                <div class="md:w-44">
                    <label for="dz-rt-to" class="dz-label">Sampai</label>
                    <input id="dz-rt-to" type="date" name="to" value="{{ request()->string('to')->toString() }}" class="dz-input" />
                </div>
                <button type="submit" class="dz-btn dz-btn-primary">Filter</button>
            </div>
        </form>

        @if ($transactions->isEmpty())
            <x-empty-state title="Tidak ada transaksi" description="Belum ada transaksi yang cocok dengan filter." />
        @else
            <div class="dz-card overflow-x-auto">
                <table class="dz-table min-w-[760px]">
                    <thead>
                        <tr><th>Tanggal</th><th>Tipe</th><th>Barang</th><th>Jumlah</th><th>Oleh</th><th>Catatan</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td class="text-xs text-ink-3">{{ $transaction->created_at?->format('d M Y H:i') }}</td>
                                <td><span class="dz-badge {{ $transaction->type->badgeClass() }}">{{ $transaction->type->label() }}</span></td>
                                <td class="font-medium text-ink">{{ $transaction->item?->name }}</td>
                                <td class="dz-mono">{{ $transaction->quantity }}</td>
                                <td class="text-ink-2">{{ $transaction->performedBy?->name ?? '-' }}</td>
                                <td class="text-ink-2">{{ $transaction->note }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-dz-pagination :paginator="$transactions" />
        @endif
    </main>
</x-layouts.app>
