<x-layouts.app :title="'Edit User — ' . config('app.name')">
    <x-page-header title="Edit User" :subtitle="$user->email" />

    <main class="space-y-6 p-6">
        <x-dz-alert tone="error" />

        <form method="POST" :action="route('users.update', $user)" class="dz-card max-w-2xl space-y-6 p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <x-dz-input type="text" name="name" :label="'Nama Lengkap'" :value="old('name', $user->name)" />
                </div>
                <div>
                    <x-dz-input type="email" name="email" :label="'Email'" :value="old('email', $user->email)" />
                </div>
                @if (auth()->user()?->isAdmin())
                    <div>
                        <x-dz-select name="role" :label="'Role'">
                            @foreach (\App\Enums\Role::cases() as $role)
                                <option value="{{ $role->value }}" @selected(old('role', $user->role->value) === $role->value)>{{ $role->label() }}</option>
                            @endforeach
                        </x-dz-select>
                    </div>
                @endif
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-line pt-6">
                <a href="{{ route('users.index') }}" class="dz-btn dz-btn-ghost">Batal</a>
                <button type="submit" class="dz-btn dz-btn-primary" onclick="this.disabled = true; this.form.submit();">Simpan Perubahan</button>
            </div>
        </form>

        @can('resetPassword', $user)
            <form method="POST" :action="route('users.reset-password', $user)" class="dz-card max-w-2xl space-y-6 p-6">
                @csrf
                <h2 class="dz-heading text-2xl">Reset Password</h2>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <x-dz-input type="password" name="password" :label="'Password Baru'" />
                    </div>
                    <div>
                        <x-dz-input type="password" name="password_confirmation" :label="'Konfirmasi Password'" />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-line pt-6">
                    <button type="submit" class="dz-btn dz-btn-danger" onclick="this.disabled = true; this.form.submit();">Reset Password</button>
                </div>
            </form>
        @endcan
    </main>
</x-layouts.app>
