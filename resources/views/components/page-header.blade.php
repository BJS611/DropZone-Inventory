@props(['title' => null, 'subtitle' => null])

<header class="border-b border-line bg-surface">
    <div class="flex flex-col gap-4 px-6 py-6 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="dz-heading text-3xl text-ink md:text-4xl">{{ $title }}</h1>
            @isset($subtitle)
                <p class="mt-1 text-sm text-ink-2">{{ $subtitle }}</p>
            @endif
        </div>
        @if (isset($actions) && $actions->isNotEmpty())
            <div class="flex flex-wrap items-center gap-3">
                {{ $actions }}
            </div>
        @endif
    </div>
</header>
