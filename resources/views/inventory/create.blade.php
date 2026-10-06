<x-layouts.app :title="'Tambah Barang — ' . config('app.name')">
    <x-page-header title="Tambah Barang" :subtitle="'Buat barang inventaris baru'" />

    <main class="p-6">
        <x-dz-alert tone="error" />

        <form method="POST" action="{{ route('items.store') }}" class="dz-card max-w-4xl space-y-8 p-6">
            @csrf

            <section>
                <h2 class="dz-heading mb-4 text-2xl">Informasi Dasar</h2>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <x-dz-input type="text" name="sku" :label="'SKU'" placeholder="DZ-0001" :helper="'Unik, disimpan huruf besar'" />
                    </div>
                    <div>
                        <x-dz-input type="text" name="name" :label="'Nama Barang'" placeholder="Nama barang" />
                    </div>
                    <div class="md:col-span-2">
                        <x-dz-textarea name="description" :label="'Deskripsi'" placeholder="Deskripsi singkat" />
                    </div>
                </div>
            </section>

            <section>
                <h2 class="dz-heading mb-4 text-2xl">Klasifikasi</h2>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                    <div>
                        <x-dz-select name="category_id" :label="'Kategori'">
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->code }})</option>
                            @endforeach
                        </x-dz-select>
                    </div>
                    <div>
                        <x-dz-select name="location_id" :label="'Lokasi'">
                            <option value="">Pilih lokasi</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }} ({{ $location->code }})</option>
                            @endforeach
                        </x-dz-select>
                    </div>
                    <div>
                        <x-dz-select name="supplier_id" :label="'Supplier'">
                            <option value="">Tanpa supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </x-dz-select>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="dz-heading mb-4 text-2xl">Stok</h2>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <x-dz-input
                            type="number"
                            name="quantity"
                            :label="'Stok Awal'"
                            :helper="'Stok awal akan dicatat sebagai transaksi IN'"
                            value="0"
                        />
                    </div>
                    <div>
                        <x-dz-input type="number" name="minimum_stock" :label="'Stok Minimum'" value="0" />
                    </div>
                    <div>
                        <x-dz-select name="unit" :label="'Satuan'">
                            @foreach (\App\Enums\Unit::cases() as $unit)
                                <option value="{{ $unit->value }}">{{ $unit->value }}</option>
                            @endforeach
                        </x-dz-select>
                    </div>
                    <div>
                        <x-dz-select name="condition" :label="'Kondisi'">
                            @foreach (\App\Enums\ItemCondition::cases() as $condition)
                                <option value="{{ $condition->value }}">{{ $condition->label() }}</option>
                            @endforeach
                        </x-dz-select>
                    </div>
                    <div>
                        <x-dz-select name="status" :label="'Status'">
                            @foreach (\App\Enums\ItemStatus::cases() as $status)
                                <option value="{{ $status->value }}">{{ $status->label() }}</option>
                            @endforeach
                        </x-dz-select>
                    </div>
                    <div>
                        <x-dz-input type="url" name="image_url" :label="'Image URL'" placeholder="https://..." :helper="'URL gambar eksternal (opsional)'" />
                    </div>
                </div>
            </section>

            <div class="flex items-center justify-end gap-3 border-t border-line pt-6">
                <a href="{{ route('items.index') }}" class="dz-btn dz-btn-ghost">Batal</a>
                <button type="submit" class="dz-btn dz-btn-primary" onclick="this.disabled = true; this.form.submit();">
                    Simpan Barang
                </button>
            </div>
        </form>
    </main>
</x-layouts.app>
