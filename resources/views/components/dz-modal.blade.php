@props(['name' => 'dz-modal', 'title' => null])

<div
    x-data="{ open: false }"
    x-on:keydown.escape.window="open = false"
    {{ $attributes->merge(['class' => '']) }}
>
    <button
        type="button"
        @click="open = true"
        {{ $triggerAttributes ?? '' }}
    >
        {{ $trigger }}
    </button>

    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
        :aria-label="{{ \Illuminate\Support\Js::from($title ?? 'Konfirmasi') }}"
    >
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/80"
            @click="open = false"
            aria-hidden="true"
        ></div>

        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative z-10 w-full max-w-lg overflow-hidden rounded-xl border border-line bg-surface dz-shadow-overlay"
        >
            @isset($title)
                <div class="border-b border-line px-6 py-4">
                    <h2 class="dz-heading text-2xl text-ink">{{ $title }}</h2>
                </div>
            @endif
            <div class="px-6 py-5">
                {{ $slot }}
            </div>
            @if (isset($footer) && $footer->isNotEmpty())
                <div class="flex justify-end gap-3 border-t border-line px-6 py-4">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
