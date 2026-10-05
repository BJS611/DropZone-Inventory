<x-layouts.app :title="'Edit Barang — ' . config('app.name')">
    <x-page-header title="Edit Barang" :subtitle="$item->sku" />

    <main class="p-6">
        <x-dz-alert tone="error" />

        <form method="POST" :action="route('items.update', $item)" class="dz-card max-w-4xl space-y-8 p-6">
            @csrf
            @method('PUT')

            <section>
                <h2 class="dz-heading mb-4 text-2xl">Informasi Dasar</h2>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <x-dz-input type="text" name="name" :label="'Nama Barang'" :value="old('name', $item->name)" />
                    </div>
                    <div class="md:col-span-2">
                        <x-dz-textarea name="description" :label="'Deskripsi'">{{ old('description', $item->description) }}</x-dz-textarea>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="dz-heading mb-4 text-2xl">Klasifikasi</h2>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                    <div>
                        <x-dz-select name="category_id" :label="'Kategori'">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $item->category_id) === $category->id)>
                                    {{ $category->name }} ({{ $category->code }})
                                </option>
                            @endforeach
                        </x-dz-select>
                    </div>
                    <div>
                        <x-dz-select name="location_id" :label="'Lokasi'">
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}" @selected(old('location_id', $item->location_id) === $location->id)>
                                    {{ $location->name }} ({{ $location->code }})
                                </option>
                            @endforeach
                        </x-dz-select>
                    </div>
                    <div>
                        <x-dz-select name="supplier_id" :label="'Supplier'">
                            <option value="">Tanpa supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected(old('supplier_id', $item->supplier_id) === $supplier->id)>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </x-dz-select>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="dz-heading mb-4 text-2xl">Stok</h2>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <x-dz-input type="number" name="minimum_stock" :label="'Stok Minimum'" :value="old('minimum_stock', $item->minimum_stock)" />
                    </div>
                    <div>
                        <x-dz-select name="unit" :label="'Satuan'">
                            @foreach (\App\Enums\Unit::cases() as $unit)
                                <option value="{{ $unit->value }}" @selected(old('unit', $item->unit->value) === $unit->value)>{{ $unit->value }}</option>
                            @endforeach
                        </x-dz-select>
                    </div>
                    <div>
                        <x-dz-select name="condition" :label="'Kondisi'">
                            @foreach (\App\Enums\ItemCondition::cases() as $condition)
                                <option value="{{ $condition->value }}" @selected(old('condition', $item->condition->value) === $condition->value)>
                                    {{ $condition->label() }}
                                </option>
                            @endforeach
                        </x-dz-select>
                    </div>
                    <div>
                        <x-dz-select name="status" :label="'Status'">
                            @foreach (\App\Enums\ItemStatus::cases() as $status)
                                <option value="{{ $status->value }}" @selected(old('status', $item->status->value) === $status->value)>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </x-dz-select>
                    </div>
                    <div class="md:col-span-2">
                        <x-dz-input type="url" name="image_url" :label="'Image URL'" :value="old('image_url', $item->image_url)" />
                    </div>
                </div>

                <p class="dz-helper">
                    Stok tidak dapat diubah dari form ini. Gunakan transaksi stok (In / Out / Adjustment / Transfer).
                </p>
            </section>

            <div class="flex items-center justify-end gap-3 border-t border-line pt-6">
                <a href="{{ route('items.show', $item) }}" class="dz-btn dz-btn-ghost">Batal</a>
                <button type="submit" class="dz-btn dz-btn-primary" onclick="this.disabled = true; this.form.submit();">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </main>
</x-layouts.app>
