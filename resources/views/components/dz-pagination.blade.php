@props(['paginator' => null])

@if ($paginator && $paginator->hasPages())
    <nav
        role="navigation"
        aria-label="Navigasi halaman"
        class="flex flex-col items-center gap-4 border-t border-line px-6 py-4 md:flex-row md:justify-between"
    >
        <p class="text-sm text-ink-2">
            Menampilkan {{ $paginator->firstItem() ?? 0 }}-{{ $paginator->lastItem() ?? 0 }}
            dari {{ $paginator->total() }} data
        </p>

        <div class="flex items-center gap-2">
            @if ($paginator->onFirstPage())
                <span class="dz-btn dz-btn-ghost dz-btn-sm opacity-40" aria-disabled="true">Sebelumnya</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="dz-btn dz-btn-secondary dz-btn-sm" rel="prev">Sebelumnya</a>
            @endif

            @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                @if ($page === $paginator->currentPage())
                    <span class="dz-chip" aria-current="page" aria-pressed="true">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="dz-chip" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="dz-btn dz-btn-secondary dz-btn-sm" rel="next">Berikutnya</a>
            @else
                <span class="dz-btn dz-btn-ghost dz-btn-sm opacity-40" aria-disabled="true">Berikutnya</span>
            @endif
        </div>
    </nav>
@endif
