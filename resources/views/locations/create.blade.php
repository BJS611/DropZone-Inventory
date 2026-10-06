<x-layouts.app :title="'Tambah Lokasi — ' . config('app.name')">
    <x-page-header title="Tambah Lokasi" />

    <main class="p-6">
        <x-dz-alert tone="error" />

        <form method="POST" action="{{ route('locations.store') }}" class="dz-card max-w-2xl space-y-6 p-6">
            @csrf
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <x-dz-input type="text" name="name" :label="'Nama Lokasi'" placeholder="Lab Jaringan" />
                </div>
                <div>
                    <x-dz-input type="text" name="code" :label="'Kode'" placeholder="LAB-JAR" :helper="'Unik, disimpan huruf besar'" />
                </div>
                <div class="md:col-span-2">
                    <x-dz-textarea name="description" :label="'Deskripsi'" placeholder="Deskripsi lokasi" />
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-line pt-6">
                <a href="{{ route('locations.index') }}" class="dz-btn dz-btn-ghost">Batal</a>
                <button type="submit" class="dz-btn dz-btn-primary" onclick="this.disabled = true; this.form.submit();">Simpan</button>
            </div>
        </form>
    </main>
</x-layouts.app>
