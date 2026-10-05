<x-layouts.app :title="'Edit Supplier — ' . config('app.name')">
    <x-page-header title="Edit Supplier" :subtitle="$supplier->name" />

    <main class="p-6">
        <x-dz-alert tone="error" />

        <form method="POST" :action="route('suppliers.update', $supplier)" class="dz-card max-w-2xl space-y-6 p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <x-dz-input type="text" name="name" :label="'Nama Supplier'" :value="old('name', $supplier->name)" />
                </div>
                <div>
                    <x-dz-input type="text" name="contact_name" :label="'Nama Kontak'" :value="old('contact_name', $supplier->contact_name)" />
                </div>
                <div>
                    <x-dz-input type="tel" name="phone" :label="'Telepon'" :value="old('phone', $supplier->phone)" />
                </div>
                <div>
                    <x-dz-input type="email" name="email" :label="'Email'" :value="old('email', $supplier->email)" />
                </div>
                <div class="md:col-span-2">
                    <x-dz-textarea name="address" :label="'Alamat'">{{ old('address', $supplier->address) }}</x-dz-textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-line pt-6">
                <a href="{{ route('suppliers.index') }}" class="dz-btn dz-btn-ghost">Batal</a>
                <button type="submit" class="dz-btn dz-btn-primary" onclick="this.disabled = true; this.form.submit();">Simpan Perubahan</button>
            </div>
        </form>
    </main>
</x-layouts.app>
