<x-layouts.app :title="'Transaksi — ' . config('app.name')">
    <x-page-header title="Detail Transaksi" :subtitle="$transaction->id" />

    <main class="p-6">
        <div class="dz-card max-w-3xl space-y-6 p-6">
            <div class="flex items-center gap-3">
                <span class="dz-badge {{ $transaction->type->badgeClass() }}">{{ $transaction->type->label() }}</span>
                <span class="dz-mono text-ink-2">{{ $transaction->id }}</span>
            </div>

            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="dz-overline">Barang</dt>
                    <dd class="mt-1 text-ink-2">{{ $transaction->item?->name }}</dd>
                </div>
                <div>
                    <dt class="dz-overline">SKU</dt>
                    <dd class="dz-mono mt-1 text-ink-2">{{ $transaction->item?->sku }}</dd>
                </div>
                <div>
                    <dt class="dz-overline">Jumlah</dt>
                    <dd class="dz-mono mt-1 text-ink">{{ $transaction->quantity }}</dd>
                </div>
                <div>
                    <dt class="dz-overline">Oleh</dt>
                    <dd class="mt-1 text-ink-2">{{ $transaction->performedBy?->name ?? '-' }}</dd>
                </div>
                @if ($transaction->from_location_id)
                    <div>
                        <dt class="dz-overline">Lokasi Asal</dt>
                        <dd class="mt-1 text-ink-2">{{ $transaction->fromLocation?->name }}</dd>
                    </div>
                @endif
                @if ($transaction->to_location_id)
                    <div>
                        <dt class="dz-overline">Lokasi Tujuan</dt>
                        <dd class="mt-1 text-ink-2">{{ $transaction->toLocation?->name }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="dz-overline">Tanggal</dt>
                    <dd class="mt-1 text-ink-2">{{ $transaction->created_at?->format('d M Y H:i') }}</dd>
                </div>
            </dl>

            @isset($transaction->note)
                <div>
                    <dt class="dz-overline">Catatan</dt>
                    <dd class="mt-1 text-sm text-ink-2">{{ $transaction->note }}</dd>
                </div>
            @endif

            <div class="flex justify-end gap-3 border-t border-line pt-6">
                <a href="{{ route('transactions.index') }}" class="dz-btn dz-btn-ghost">Kembali</a>
                <a href="{{ route('items.show', $transaction->item) }}" class="dz-btn dz-btn-secondary">Lihat Barang</a>
            </div>
        </div>
    </main>
</x-layouts.app>
