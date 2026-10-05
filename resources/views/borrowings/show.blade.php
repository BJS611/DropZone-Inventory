<x-layouts.app :title="'Peminjaman — ' . config('app.name')">
    <x-page-header :title="'Peminjaman ' . $borrowing->borrower_name" :subtitle="$borrowing->id">
        <x-slot:actions>
            <a href="{{ route('borrowings.index') }}" class="dz-btn dz-btn-ghost">Kembali</a>
            @can('cancel', $borrowing)
                @if (! in_array($borrowing->status->value, ['RETURNED', 'CANCELLED']))
                    <x-dz-confirm
                        :action="route('borrowings.cancel', $borrowing)"
                        label="Batalkan"
                        confirm="CANCEL"
                        placeholder="CANCEL"
                        tone="danger"
                        title="Batalkan Peminjaman"
                        message="Stok akan dikembalikan ke inventaris dan peminjaman ditandai sebagai dibatalkan."
                    />
                @endif
            @endcan
        </x-slot:actions>
    </x-page-header>

    <main class="space-y-8 p-6">
        <x-dz-alert tone="success" />
        <x-dz-alert tone="error" />

        <section class="dz-card max-w-4xl space-y-6 p-6">
            <div class="flex items-center gap-3">
                <span class="dz-badge {{ $borrowing->status->badgeClass() }}">{{ $borrowing->status->label() }}</span>
                <span class="dz-mono text-xs text-ink-3">{{ $borrowing->id }}</span>
            </div>

            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="dz-overline">Nama Peminjam</dt>
                    <dd class="mt-1 text-ink">{{ $borrowing->borrower_name }}</dd>
                </div>
                <div>
                    <dt class="dz-overline">ID Peminjam</dt>
                    <dd class="dz-mono mt-1 text-ink-2">{{ $borrowing->borrower_identifier ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="dz-overline">Kontak</dt>
                    <dd class="mt-1 text-ink-2">{{ $borrowing->borrower_contact ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="dz-overline">Keperluan</dt>
                    <dd class="mt-1 text-ink-2">{{ $borrowing->purpose ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="dz-overline">Dipinjam Pada</dt>
                    <dd class="mt-1 text-ink-2">{{ $borrowing->borrowed_at?->format('d M Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="dz-overline">Tenggat Pengembalian</dt>
                    <dd class="mt-1 text-ink-2">{{ $borrowing->expected_return_at?->format('d M Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="dz-overline">Dibuat Oleh</dt>
                    <dd class="mt-1 text-ink-2">{{ $borrowing->createdBy?->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="dz-overline">Disetujui Oleh</dt>
                    <dd class="mt-1 text-ink-2">{{ $borrowing->approvedBy?->name ?? '-' }}</dd>
                </div>
            </dl>

            @isset($borrowing->note)
                <div>
                    <dt class="dz-overline">Catatan</dt>
                    <dd class="mt-1 text-sm text-ink-2">{{ $borrowing->note }}</dd>
                </div>
            @endif
        </section>

        <section>
            <h2 class="dz-heading mb-3 text-2xl">Daftar Barang</h2>
            <div class="dz-card overflow-x-auto">
                <table class="dz-table min-w-[760px]">
                    <thead>
                        <tr><th>Barang</th><th>Jumlah</th><th>Dikembalikan</th><th>Kondisi Awal</th><th>Kondisi Akhir</th><th class="text-right">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($borrowing->items as $borrowingItem)
                            <tr>
                                <td>
                                    <a href="{{ route('items.show', $borrowingItem->item) }}" class="font-medium text-ink hover:text-secondary">
                                        {{ $borrowingItem->item?->name }}
                                    </a>
                                </td>
                                <td class="dz-mono">{{ $borrowingItem->quantity }}</td>
                                <td class="dz-mono">{{ $borrowingItem->returned_quantity }}</td>
                                <td><span class="dz-badge {{ $borrowingItem->condition_before->badgeClass() }}">{{ $borrowingItem->condition_before->label() }}</span></td>
                                <td>
                                    @isset($borrowingItem->condition_after)
                                        <span class="dz-badge {{ $borrowingItem->condition_after->badgeClass() }}">{{ $borrowingItem->condition_after->label() }}</span>
                                    @else
                                        <span class="text-ink-3">-</span>
                                    @endif
                                </td>
                                <td>
                                    @can('return', $borrowing)
                                        @if ($borrowingItem->remainingQuantity() > 0)
                                            <form
                                                method="POST"
                                                :action="route('borrowings.items.return', [$borrowing, $borrowingItem])"
                                                class="flex items-center justify-end gap-2"
                                            >
                                                @csrf
                                                <input
                                                    type="number"
                                                    name="quantity"
                                                    min="1"
                                                    max="{{ $borrowingItem->remainingQuantity() }}"
                                                    value="{{ $borrowingItem->remainingQuantity() }}"
                                                    class="dz-input w-24"
                                                    aria-label="Jumlah pengembalian {{ $borrowingItem->item?->name }}"
                                                    required
                                                />
                                                <select name="condition_after" class="dz-input w-40" aria-label="Kondisi setelah pemakaian">
                                                    <option value="">Kondisi akhir</option>
                                                    @foreach (\App\Enums\ItemCondition::cases() as $condition)
                                                        <option value="{{ $condition->value }}">{{ $condition->label() }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="dz-btn dz-btn-primary dz-btn-sm shrink-0">Kembalikan</button>
                                            </form>
                                        @else
                                            <span class="dz-badge dz-badge-success">Selesai</span>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</x-layouts.app>
