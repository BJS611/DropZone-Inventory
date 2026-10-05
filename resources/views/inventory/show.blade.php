<x-layouts.app :title="$item->name . ' — ' . config('app.name')">
    <x-page-header :title="$item->name" :subtitle="$item->sku">
        <x-slot:actions>
            <a href="{{ route('items.index') }}" class="dz-btn dz-btn-ghost">Kembali</a>
            @can('stockIn', $item)
                <a href="#dz-stock-in" class="dz-btn dz-btn-secondary">Stock In</a>
            @endcan
            @can('update', $item)
                <a href="{{ route('items.edit', $item) }}" class="dz-btn dz-btn-primary">Edit</a>

                @can('delete', $item)
                    <form method="POST" action="{{ route('items.destroy', $item) }}" class="inline" onsubmit="return confirm('Hapus barang ini? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dz-btn dz-btn-danger">Hapus</button>
                    </form>
                @endcan
            @endcan
        </x-slot:actions>
    </x-page-header>

    <main class="space-y-8 p-6">
        <x-dz-alert tone="success" />
        <x-dz-alert tone="error" />

        <section class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="dz-card space-y-4 p-6 lg:col-span-2">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="dz-badge {{ $item->status->badgeClass() }}">{{ $item->status->label() }}</span>
                    <span class="dz-badge {{ $item->condition->badgeClass() }}">{{ $item->condition->label() }}</span>
                    <x-stock-badge :quantity="$item->quantity" :minimum="$item->minimum_stock" />
                </div>

                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="dz-overline">SKU</dt>
                        <dd class="dz-mono mt-1 text-ink">{{ $item->sku }}</dd>
                    </div>
                    <div>
                        <dt class="dz-overline">Stok Saat Ini</dt>
                        <dd class="dz-mono mt-1 text-2xl text-ink">{{ $item->quantity }} {{ $item->unit->value }}</dd>
                    </div>
                    <div>
                        <dt class="dz-overline">Stok Minimum</dt>
                        <dd class="dz-mono mt-1 text-ink-2">{{ $item->minimum_stock }} {{ $item->unit->value }}</dd>
                    </div>
                    <div>
                        <dt class="dz-overline">Kategori</dt>
                        <dd class="mt-1 text-ink-2">{{ $item->category?->name }} ({{ $item->category?->code }})</dd>
                    </div>
                    <div>
                        <dt class="dz-overline">Lokasi</dt>
                        <dd class="mt-1 text-ink-2">{{ $item->location?->name }} ({{ $item->location?->code }})</dd>
                    </div>
                    <div>
                        <dt class="dz-overline">Supplier</dt>
                        <dd class="mt-1 text-ink-2">{{ $item->supplier?->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="dz-overline">Dibuat</dt>
                        <dd class="mt-1 text-ink-2">{{ $item->created_at?->format('d M Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="dz-overline">Diperbarui</dt>
                        <dd class="mt-1 text-ink-2">{{ $item->updated_at?->format('d M Y H:i') }}</dd>
                    </div>
                </dl>

                @isset($item->description)
                    <div>
                        <dt class="dz-overline">Deskripsi</dt>
                        <dd class="mt-1 text-sm text-ink-2">{{ $item->description }}</dd>
                    </div>
                @endif
            </div>

            <div class="dz-card space-y-4 p-6">
                <h2 class="dz-heading text-2xl">Operasi Stok</h2>

                @can('stockIn', $item)
                    <form id="dz-stock-in" method="POST" :action="route('items.stock-in', $item)" class="space-y-3 border-b border-line pb-4">
                        @csrf
                        <label for="dz-in-qty" class="dz-label">Stok Masuk</label>
                        <div class="flex gap-2">
                            <input id="dz-in-qty" type="number" name="quantity" min="1" value="1" required class="dz-input" />
                            <button type="submit" class="dz-btn dz-btn-primary shrink-0">Masuk</button>
                        </div>
                        @error('quantity') <p class="dz-error" role="alert">{{ $message }}</p> @enderror
                    </form>
                @endcan

                @can('stockOut', $item)
                    <form method="POST" :action="route('items.stock-out', $item)" class="space-y-3 border-b border-line pb-4">
                        @csrf
                        <label for="dz-out-qty" class="dz-label">Stok Keluar</label>
                        <div class="flex gap-2">
                            <input id="dz-out-qty" type="number" name="quantity" min="1" value="1" required class="dz-input" />
                            <button type="submit" class="dz-btn dz-btn-danger shrink-0">Keluar</button>
                        </div>
                        @error('quantity') <p class="dz-error" role="alert">{{ $message }}</p> @enderror
                    </form>
                @endcan

                @can('adjust', $item)
                    <form method="POST" :action="route('items.adjust', $item)" class="space-y-3 border-b border-line pb-4">
                        @csrf
                        <label for="dz-adj" class="dz-label">Penyesuaian Stok</label>
                        <div class="flex gap-2">
                            <input id="dz-adj" type="number" name="new_stock" min="0" value="{{ $item->quantity }}" required class="dz-input" />
                            <button type="submit" class="dz-btn dz-btn-secondary shrink-0">Sesuaikan</button>
                        </div>
                        @error('new_stock') <p class="dz-error" role="alert">{{ $message }}</p> @enderror
                    </form>
                @endcan

                @can('transfer', $item)
                    <form method="POST" :action="route('items.transfer', $item)" class="space-y-3">
                        @csrf
                        <label for="dz-trf-to" class="dz-label">Transfer Lokasi</label>
                        <select id="dz-trf-to" name="to_location_id" class="dz-input" required>
                            <option value="">Pilih tujuan</option>
                            @foreach (\App\Models\Location::orderBy('name')->get() as $location)
                                @if ($location->id !== $item->location_id)
                                    <option value="{{ $location->id }}">{{ $location->name }} ({{ $location->code }})</option>
                                @endif
                            @endforeach
                        </select>
                        <input type="hidden" name="from_location_id" value="{{ $item->location_id }}" />
                        <input type="hidden" name="quantity" value="{{ max(1, $item->quantity) }}" />
                        <button type="submit" class="dz-btn dz-btn-secondary w-full">Transfer</button>
                        @error('to_location_id') <p class="dz-error" role="alert">{{ $message }}</p> @enderror
                    </form>
                @endcan
            </div>
        </section>

        <section>
            <h2 class="dz-heading mb-3 text-2xl">Riwayat Transaksi Stok</h2>
            <div class="dz-card overflow-x-auto">
                <table class="dz-table min-w-[640px]">
                    <thead>
                        <tr>
                            <th>Tanggal</th><th>Tipe</th><th>Jumlah</th><th>Oleh</th><th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($item->stockTransactions as $transaction)
                            <tr>
                                <td class="text-xs text-ink-3">{{ $transaction->created_at?->format('d M Y H:i') }}</td>
                                <td><span class="dz-badge {{ $transaction->type->badgeClass() }}">{{ $transaction->type->label() }}</span></td>
                                <td class="dz-mono">{{ $transaction->quantity }}</td>
                                <td class="text-ink-2">{{ $transaction->performedBy?->name ?? '-' }}</td>
                                <td class="text-ink-2">{{ $transaction->note }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-8 text-center text-sm text-ink-3">Belum ada transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section>
            <h2 class="dz-heading mb-3 text-2xl">Riwayat Peminjaman</h2>
            <div class="dz-card overflow-x-auto">
                <table class="dz-table min-w-[640px]">
                    <thead>
                        <tr>
                            <th>Peminjam</th><th>Jumlah</th><th>Dikembalikan</th><th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($item->borrowingItems as $borrowingItem)
                            <tr>
                                <td class="text-ink-2">{{ $borrowingItem->borrowing->borrower_name }}</td>
                                <td class="dz-mono">{{ $borrowingItem->quantity }}</td>
                                <td class="dz-mono">{{ $borrowingItem->returned_quantity }}</td>
                                <td><span class="dz-badge {{ $borrowingItem->borrowing->status->badgeClass() }}">{{ $borrowingItem->borrowing->status->label() }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-8 text-center text-sm text-ink-3">Belum pernah dipinjam.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</x-layouts.app>
