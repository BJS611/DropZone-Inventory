@php
    $nav = [
        ['dashboard', 'Dashboard', 'dashboard'],
        ['items.index', 'Inventory', 'items'],
        ['transactions.index', 'Transactions', 'transactions'],
        ['borrowings.index', 'Borrowings', 'borrowings'],
        ['categories.index', 'Categories', 'categories'],
        ['locations.index', 'Locations', 'locations'],
        ['suppliers.index', 'Suppliers', 'suppliers'],
        ['reports.index', 'Reports', 'reports'],
    ];
    if (auth()->user()?->isAdmin()) {
        $nav[] = ['users.index', 'Users', 'users'];
        $nav[] = ['audit.index', 'Audit Log', 'audit'];
    }
@endphp

<div
    x-data="{ open: false }"
    x-on:keydown.escape.window="open = false"
    class="fixed inset-0 z-40 lg:z-auto"
>
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/70 lg:hidden"
        aria-hidden="true"
        @click="open = false"
    ></div>

    <aside
        x-show="open || window.innerWidth >= 1024"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="dz-sidebar fixed inset-y-0 left-0 z-50 w-64 flex flex-col border-r border-line bg-surface"
        aria-label="Navigasi utama"
    >
        <div class="flex h-16 items-center gap-3 border-b border-line px-6">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <span class="text-2xl">📦</span>
                <span class="dz-heading text-2xl tracking-wider text-ink">DROPZONE</span>
            </a>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto p-3">
            @foreach ($nav as [$route, $label, $active])
                <a
                    href="{{ route($route) }}"
                    class="dz-nav-link {{ request()->routeIs($active . '*') ? 'dz-active' : '' }}"
                    @click="window.innerWidth < 1024 && (open = false)"
                >
                    <span class="text-base">{{ $label }}</span>
                </a>
            @endforeach
        </nav>

        <div class="border-t border-line p-3">
            <div class="px-3 py-2">
                <p class="dz-overline">Login sebagai</p>
                <p class="mt-1 truncate text-sm font-medium text-ink">{{ auth()->user()?->name }}</p>
                <p class="dz-badge-neutral mt-1.5">{{ auth()->user()?->role?->label() }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="dz-btn dz-btn-ghost dz-btn-sm w-full">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <button
        type="button"
        @click="open = !open"
        class="fixed left-4 top-4 z-50 inline-flex h-10 w-10 items-center justify-center rounded-lg border border-line bg-surface text-ink lg:hidden"
        :aria-expanded="open"
        aria-label="Buka menu navigasi"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>
</div>
