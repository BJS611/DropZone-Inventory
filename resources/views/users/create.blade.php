<x-layouts.app :title="'Tambah User — ' . config('app.name')">
    <x-page-header title="Tambah User" />

    <main class="p-6">
        <x-dz-alert tone="error" />

        <form method="POST" :action="route('users.store')" class="dz-card max-w-2xl space-y-6 p-6">
            @csrf
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <x-dz-input type="text" name="name" :label="'Nama Lengkap'" />
                </div>
                <div>
                    <x-dz-input type="email" name="email" :label="'Email'" />
                </div>
                <div>
                    <x-dz-input type="password" name="password" :label="'Password'" />
                </div>
                <div>
                    <x-dz-input type="password" name="password_confirmation" :label="'Konfirmasi Password'" />
                </div>
                <div>
                    <x-dz-select name="role" :label="'Role'">
                        @foreach (\App\Enums\Role::cases() as $role)
                            <option value="{{ $role->value }}">{{ $role->label() }}</option>
                        @endforeach
                    </x-dz-select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-line pt-6">
                <a href="{{ route('users.index') }}" class="dz-btn dz-btn-ghost">Batal</a>
                <button type="submit" class="dz-btn dz-btn-primary" onclick="this.disabled = true; this.form.submit();">Simpan</button>
            </div>
        </form>
    </main>
</x-layouts.app>
