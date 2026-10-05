@props([
    'action' => null,
    'label' => 'Hapus',
    'tone' => 'danger',
    'confirm' => 'DELETE',
    'placeholder' => 'DELETE',
    'title' => 'Konfirmasi',
    'message' => 'Aksi ini tidak dapat dibatalkan.',
])

<div
    x-data="{ open: false, confirmation: '' }"
    x-on:keydown.escape.window="open = false"
>
    <button
        type="button"
        @click="open = true; confirmation = ''"
        class="{{ $tone === 'danger' ? 'dz-btn dz-btn-danger dz-btn-sm' : 'dz-btn dz-btn-secondary dz-btn-sm' }}"
    >
        {{ $label }}
    </button>

    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
        aria-label="{{ $title }}"
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
            <div class="border-b border-line px-6 py-4">
                <h2 class="dz-heading text-2xl text-ink">{{ $title }}</h2>
            </div>

            <form method="POST" action="{{ $action }}" @submit.prevent="if (confirmation === '{{ $confirm }}') $el.submit()">
                @csrf
                <div class="px-6 py-5">
                    <p class="text-sm text-ink-2">{{ $message }}</p>
                    <p class="mt-3 text-sm text-ink-2">
                        Ketik <span class="dz-mono text-warning">{{ $placeholder }}</span> untuk melanjutkan.
                    </p>

                    <div class="mt-4">
                        <label for="dz-confirm-{{ substr(\Illuminate\Support\Str::uuid()->toString(), 0, 8) }}" class="dz-label">Konfirmasi</label>
                        <input
                            type="text"
                            name="confirmation"
                            x-model="confirmation"
                            autocomplete="off"
                            class="dz-input"
                            aria-describedby="dz-confirm-help"
                        />
                        <p id="dz-confirm-help" class="sr-only">Ketik {{ $placeholder }} untuk mengaktifkan tombol konfirmasi.</p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-line px-6 py-4">
                    <button type="button" @click="open = false" class="dz-btn dz-btn-ghost dz-btn-sm">Batal</button>
                    <button
                        type="submit"
                        :disabled="confirmation !== '{{ $confirm }}'"
                        class="dz-btn dz-btn-danger dz-btn-sm"
                    >
                        {{ $label }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
