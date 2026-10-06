<x-layouts.app :title="'Edit Lokasi — ' . config('app.name')">
    <x-page-header title="Edit Lokasi" :subtitle="$location->code" />

    <main class="p-6">
        <x-dz-alert tone="error" />

        <form method="POST" action="{{ route('locations.update', $location) }}" class="dz-card max-w-2xl space-y-6 p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <x-dz-input type="text" name="name" :label="'Nama Lokasi'" :value="old('name', $location->name)" />
                </div>
                <div>
                    <x-dz-input type="text" name="code" :label="'Kode'" :value="old('code', $location->code)" />
                </div>
                <div class="md:col-span-2">
                    <x-dz-textarea name="description" :label="'Deskripsi'">{{ old('description', $location->description) }}</x-dz-textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-line pt-6">
                <a href="{{ route('locations.index') }}" class="dz-btn dz-btn-ghost">Batal</a>
                <button type="submit" class="dz-btn dz-btn-primary" onclick="this.disabled = true; this.form.submit();">Simpan Perubahan</button>
            </div>
        </form>
    </main>
</x-layouts.app>
