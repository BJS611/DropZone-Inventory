<x-layouts.app :title="'Tambah Supplier — ' . config('app.name')">
    <x-page-header title="Tambah Supplier" />

    <main class="p-6">
        <x-dz-alert tone="error" />

        <form method="POST" action="{{ route('suppliers.store') }}" class="dz-card max-w-2xl space-y-6 p-6">
            @csrf
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <x-dz-input type="text" name="name" :label="'Nama Supplier'" placeholder="PT Maju Jaya" />
                </div>
                <div>
                    <x-dz-input type="text" name="contact_name" :label="'Nama Kontak'" placeholder="Budi" />
                </div>
                <div>
                    <x-dz-input type="tel" name="phone" :label="'Telepon'" placeholder="0812..." />
                </div>
                <div>
                    <x-dz-input type="email" name="email" :label="'Email'" placeholder=" supplier@example.com" />
                </div>
                <div class="md:col-span-2">
                    <x-dz-textarea name="address" :label="'Alamat'" placeholder="Alamat lengkap" />
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-line pt-6">
                <a href="{{ route('suppliers.index') }}" class="dz-btn dz-btn-ghost">Batal</a>
                <button type="submit" class="dz-btn dz-btn-primary" onclick="this.disabled = true; this.form.submit();">Simpan</button>
            </div>
        </form>
    </main>
</x-layouts.app>
