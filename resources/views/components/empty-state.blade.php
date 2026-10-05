@props(['title' => 'Tidak ada data', 'description' => null])

<div class="dz-card px-8 py-16 text-center">
    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center border border-line bg-surface-2 text-ink-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" d="M3 7l9-4 9 4M3 7v10l9 4 9-4V7M3 7l9 4m0 0l9-4m-9 4v10" />
        </svg>
    </div>
    <h3 class="dz-heading text-2xl text-ink">{{ $title }}</h3>
    @isset($description)
        <p class="mx-auto mt-2 max-w-md text-sm text-ink-2">{{ $description }}</p>
    @endif
    @if (isset($actions) && $actions->isNotEmpty())
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            {{ $actions }}
        </div>
    @endif
</div>
