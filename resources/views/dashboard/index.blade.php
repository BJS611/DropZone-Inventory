<x-layouts.app :title="'Dashboard — ' . config('app.name')">
    <x-page-header title="Dashboard" :subtitle="'Ringkasan inventaris saat ini'">
        <x-slot:actions>
            @can('create', \App\Models\Item::class)
                <x-dz-button as="a" :href="route('items.create')">+ Tambah Barang</x-dz-button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <main class="space-y-8 p-6">
        <x-dz-alert tone="success" />
        <x-dz-alert tone="error" />

        <section aria-label="KPI utama">
            <h2 class="dz-overline mb-3">Ringkasan</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <div class="dz-card p-6">
                    <p class="dz-overline">Total Items</p>
                    <p class="dz-heading mt-2 text-5xl text-ink">{{ $totalItems }}</p>
                </div>
                <div class="dz-card p-6">
                    <p class="dz-overline">Total Stock</p>
                    <p class="dz-heading mt-2 text-5xl text-info">{{ $totalStock }}</p>
                </div>
                <div class="dz-card p-6">
                    <p class="dz-overline">Low Stock</p>
                    <p class="dz-heading mt-2 text-5xl text-warning">{{ $lowStockCount }}</p>
                </div>
                <div class="dz-card dz-glow-orange p-6">
                    <p class="dz-overline">Out Of Stock</p>
                    <p class="dz-heading mt-2 text-5xl text-error">{{ $outOfStockCount }}</p>
                </div>
                <div class="dz-card p-6">
                    <p class="dz-overline">Currently Borrowed</p>
                    <p class="dz-heading mt-2 text-5xl text-info">{{ $borrowedCount }}</p>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            <section aria-label="Transaksi terakhir">
                <h2 class="dz-heading mb-3 text-2xl">Transaksi Terakhir</h2>
                <div class="dz-card">
                    @forelse ($recentTransactions as $transaction)
                        <div class="flex items-center justify-between gap-4 border-b border-line px-5 py-3 last:border-b-0">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-ink">{{ $transaction->item?->name }}</p>
                                <p class="dz-mono mt-0.5 text-xs text-ink-3">{{ $transaction->item?->sku }}</p>
                            </div>
                            <div class="flex shrink-0 items-center gap-3">
                                <span class="dz-badge {{ $transaction->type->badgeClass() }}">{{ $transaction->type->label() }}</span>
                                <span class="dz-mono text-sm text-ink">{{ $transaction->quantity }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="px-5 py-8 text-sm text-ink-3">Belum ada transaksi.</p>
                    @endforelse
                </div>
            </section>

            <section aria-label="Peminjaman terakhir">
                <h2 class="dz-heading mb-3 text-2xl">Peminjaman Terakhir</h2>
                <div class="dz-card">
                    @forelse ($recentBorrowings as $borrowing)
                        <div class="flex items-center justify-between gap-4 border-b border-line px-5 py-3 last:border-b-0">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-ink">{{ $borrowing->borrower_name }}</p>
                                <p class="mt-0.5 text-xs text-ink-3">{{ $borrowing->borrowed_at?->format('d M Y') }}</p>
                            </div>
                            <span class="dz-badge {{ $borrowing->status->badgeClass() }}">{{ $borrowing->status->label() }}</span>
                        </div>
                    @empty
                        <p class="px-5 py-8 text-sm text-ink-3">Belum ada peminjaman.</p>
                    @endforelse
                </div>
            </section>

            <section aria-label="Barang dengan stok rendah">
                <h2 class="dz-heading mb-3 text-2xl">Low Stock</h2>
                <div class="dz-card">
                    @forelse ($lowStockItems as $item)
                        <div class="flex items-center justify-between gap-4 border-b border-line px-5 py-3 last:border-b-0">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-ink">{{ $item->name }}</p>
                                <p class="dz-mono mt-0.5 text-xs text-ink-3">{{ $item->sku }}</p>
                            </div>
                            <span class="dz-badge dz-badge-warning">{{ $item->quantity }} / {{ $item->minimum_stock }}</span>
                        </div>
                    @empty
                        <p class="px-5 py-8 text-sm text-ink-3">Semua barang di atas batas minimum.</p>
                    @endforelse
                </div>
            </section>

            <section aria-label="Barang habis">
                <h2 class="dz-heading mb-3 text-2xl">Out Of Stock</h2>
                <div class="dz-card">
                    @forelse ($outOfStockItems as $item)
                        <div class="flex items-center justify-between gap-4 border-b border-line px-5 py-3 last:border-b-0">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-ink">{{ $item->name }}</p>
                                <p class="dz-mono mt-0.5 text-xs text-ink-3">{{ $item->sku }}</p>
                            </div>
                            <span class="dz-badge dz-badge-error">Out Of Stock</span>
                        </div>
                    @empty
                        <p class="px-5 py-8 text-sm text-ink-3">Tidak ada barang yang habis.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <section aria-label="Barang terbaru">
            <h2 class="dz-heading mb-3 text-2xl">Barang Terbaru</h2>
            <div class="dz-card">
                @forelse ($recentItems as $item)
                    <a
                        href="{{ route('items.show', $item) }}"
                        class="flex items-center justify-between gap-4 border-b border-line px-5 py-3 transition-colors hover:bg-surface-2 last:border-b-0"
                    >
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-ink">{{ $item->name }}</p>
                            <p class="dz-mono mt-0.5 text-xs text-ink-3">{{ $item->sku }}</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-3">
                            <span class="text-xs text-ink-3">{{ $item->created_at?->format('d M Y') }}</span>
                            <x-stock-badge :quantity="$item->quantity" :minimum="$item->minimum_stock" />
                        </div>
                    </a>
                @empty
                    <p class="px-5 py-8 text-sm text-ink-3">Belum ada barang.</p>
                @endforelse
            </div>
        </section>
    </main>
</x-layouts.app>
