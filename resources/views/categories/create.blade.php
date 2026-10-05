<x-layouts.app :title="'Tambah Kategori — ' . config('app.name')">
    <x-page-header title="Tambah Kategori" />

    <main class="p-6">
        <x-dz-alert tone="error" />

        <form method="POST" :action="route('categories.store')" class="dz-card max-w-2xl space-y-6 p-6">
            @csrf
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <x-dz-input type="text" name="name" :label="'Nama Kategori'" placeholder="Elektronik" />
                </div>
                <div>
                    <x-dz-input type="text" name="code" :label="'Kode'" placeholder="ELK" :helper="'Unik, disimpan huruf besar'" />
                </div>
                <div class="md:col-span-2">
                    <x-dz-textarea name="description" :label="'Deskripsi'" placeholder="Deskripsi kategori" />
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-line pt-6">
                <a href="{{ route('categories.index') }}" class="dz-btn dz-btn-ghost">Batal</a>
                <button type="submit" class="dz-btn dz-btn-primary" onclick="this.disabled = true; this.form.submit();">Simpan</button>
            </div>
        </form>
    </main>
</x-layouts.app>
