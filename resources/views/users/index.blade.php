<x-layouts.app :title="'Users — ' . config('app.name')">
    <x-page-header title="User Management" :subtitle="'Kelola pengguna dan peran'">
        <x-slot:actions>
            <x-dz-button as="a" :href="route('users.create')">+ Tambah User</x-dz-button>
        </x-slot:actions>
    </x-page-header>

    <main class="space-y-6 p-6">
        <x-dz-alert tone="success" />
        <x-dz-alert tone="error" />

        <form method="GET" action="{{ route('users.index') }}" class="dz-card p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-end">
                <div class="flex-1">
                    <label for="dz-user-search" class="dz-label">Cari</label>
                    <input id="dz-user-search" type="search" name="search" value="{{ request()->string('search')->toString() }}" placeholder="Nama atau email" class="dz-input" />
                </div>
                <button type="submit" class="dz-btn dz-btn-primary">Filter</button>
            </div>
        </form>

        @if ($users->isEmpty())
            <x-empty-state title="Belum ada user" description="Tambahkan pengguna baru." />
        @else
            <div class="dz-card overflow-x-auto">
                <table class="dz-table min-w-[760px]">
                    <thead>
                        <tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Dibuat</th><th class="text-right">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td class="font-medium text-ink">{{ $user->name }}</td>
                                <td class="text-ink-2">{{ $user->email }}</td>
                                <td><span class="dz-badge {{ $user->role === \App\Enums\Role::ADMIN ? 'dz-badge-info' : 'dz-badge-neutral' }}">{{ $user->role->label() }}</span></td>
                                <td><span class="dz-badge {{ $user->status === \App\Enums\UserStatus::ACTIVE ? 'dz-badge-success' : 'dz-badge-error' }}">{{ $user->status->value }}</span></td>
                                <td class="text-xs text-ink-3">{{ $user->created_at?->format('d M Y') }}</td>
                                <td>
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('users.edit', $user) }}" class="dz-btn dz-btn-secondary dz-btn-sm">Edit</a>
                                        @can('deactivate', $user)
                                            <form method="POST" action="{{ route('users.toggle-status', $user) }}" class="inline">
                                                @csrf
                                                @if ($user->status === \App\Enums\UserStatus::ACTIVE)
                                                    <button type="submit" class="dz-btn dz-btn-danger dz-btn-sm" onclick="return confirm('Nonaktifkan user ini?')">Nonaktifkan</button>
                                                @else
                                                    <button type="submit" class="dz-btn dz-btn-primary dz-btn-sm">Aktifkan</button>
                                                @endif
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-dz-pagination :paginator="$users" />
        @endif
    </main>
</x-layouts.app>
