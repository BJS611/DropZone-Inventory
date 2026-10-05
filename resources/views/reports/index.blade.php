<x-layouts.app :title="'Reports — ' . config('app.name')">
    <x-page-header title="Reports" :subtitle="'Laporan operasional inventaris'" />

    <main class="space-y-6 p-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <a href="{{ route('reports.inventory') }}" class="dz-card dz-card-hover p-6 transition-colors">
                <p class="dz-overline">Laporan</p>
                <h2 class="dz-heading mt-2 text-2xl text-ink">Inventory Report</h2>
                <p class="mt-2 text-sm text-ink-2">Seluruh barang dengan kategori, lokasi, dan status stok.</p>
                <p class="dz-mono mt-4 text-info">Lihat laporan →</p>
            </a>
            <a href="{{ route('reports.low-stock') }}" class="dz-card dz-card-hover p-6 transition-colors">
                <p class="dz-overline">Laporan</p>
                <h2 class="dz-heading mt-2 text-2xl text-ink">Low Stock Report</h2>
                <p class="mt-2 text-sm text-ink-2">Barang dengan stok di bawah atau sama dengan minimum.</p>
                <p class="dz-mono mt-4 text-warning">{{ $lowStock }} barang →</p>
            </a>
            <a href="{{ route('reports.transactions') }}" class="dz-card dz-card-hover p-6 transition-colors">
                <p class="dz-overline">Laporan</p>
                <h2 class="dz-heading mt-2 text-2xl text-ink">Transaction Report</h2>
                <p class="mt-2 text-sm text-ink-2">Riwayat seluruh transaksi stok.</p>
                <p class="dz-mono mt-4 text-info">{{ $transactions }} transaksi →</p>
            </a>
            <a href="{{ route('reports.borrowings') }}" class="dz-card dz-card-hover p-6 transition-colors">
                <p class="dz-overline">Laporan</p>
                <h2 class="dz-heading mt-2 text-2xl text-ink">Borrowing Report</h2>
                <p class="mt-2 text-sm text-ink-2">Riwayat peminjaman dan status pengembalian.</p>
                <p class="dz-mono mt-4 text-info">{{ $activeBorrowings }} aktif →</p>
            </a>
        </div>
    </main>
</x-layouts.app>
