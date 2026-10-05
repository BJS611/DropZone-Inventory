<x-layouts.app :title="'Transaksi — ' . config('app.name')">
    <x-page-header title="Transactions" :subtitle="'Riwayat transaksi stok'">
        <x-slot:actions>
            <x-dz-button as="a" :href="route('transactions.export')" variant="secondary">Export CSV</x-dz-button>
        </x-slot:actions>
    </x-page-header>

    <main class="space-y-6 p-6">
        <x-dz-alert tone="success" />
        <x-dz-alert tone="error" />

        <form method="GET" :action="route('transactions.index')" class="dz-card p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-end">
                <div class="flex-1">
                    <label for="dz-tx-search" class="dz-label">Cari</label>
                    <input id="dz-tx-search" type="search" name="search" value="{{ request()->string('search')->toString() }}" placeholder="SKU atau nama barang" class="dz-input" />
                </div>
                <div class="md:w-48">
                    <label for="dz-tx-type" class="dz-label">Tipe</label>
                    <select id="dz-tx-type" name="type" class="dz-input">
                        <option value="">Semua</option>
                        @foreach (\App\Enums\TransactionType::cases() as $type)
                            <option value="{{ $type->value }}" @selected(request()->string('type')->toString() === $type->value)>{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="dz-btn dz-btn-primary">Filter</button>
            </div>
        </form>

        @if ($transactions->isEmpty())
            <x-empty-state title="Belum ada transaksi" description="Transaksi muncul setelah ada operasi stok." />
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
                                <td>
                                    <a href="{{ route('items.show', $transaction->item) }}" class="font-medium text-ink hover:text-secondary">
                                        {{ $transaction->item?->name }}
                                    </a>
                                    <p class="dz-mono mt-0.5 text-xs text-ink-3">{{ $transaction->item?->sku }}</p>
                                </td>
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
