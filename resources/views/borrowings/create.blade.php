<x-layouts.app :title="'Buat Peminjaman — ' . config('app.name')">
    <x-page-header title="Buat Peminjaman" :subtitle="'Pilih barang dan jumlah yang dipinjam'" />

    <main class="p-6">
        <x-dz-alert tone="error" />

        <form method="POST" :action="route('borrowings.store')" class="dz-card max-w-4xl space-y-8 p-6">
            @csrf

            <section>
                <h2 class="dz-heading mb-4 text-2xl">Data Peminjam</h2>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <x-dz-input type="text" name="borrower_name" :label="'Nama Peminjam'" placeholder="Nama lengkap" />
                    </div>
                    <div>
                        <x-dz-input type="text" name="borrower_identifier" :label="'ID Peminjam'" placeholder="NIM / NIP / KTP" />
                    </div>
                    <div>
                        <x-dz-input type="text" name="borrower_contact" :label="'Kontak'" placeholder="No. HP atau email" />
                    </div>
                    <div>
                        <x-dz-input type="date" name="expected_return_at" :label="'Tenggat Pengembalian'" />
                    </div>
                    <div class="md:col-span-2">
                        <x-dz-textarea name="purpose" :label="'Keperluan'" placeholder="Untuk keperluan apa" />
                    </div>
                </div>
            </section>

            <section>
                <h2 class="dz-heading mb-4 text-2xl">Barang Dipinjam</h2>
                <div class="space-y-3">
                    @foreach ($items as $item)
                        <div class="flex items-center gap-4 border border-line bg-surface-3 p-4">
                            <input
                                type="checkbox"
                                name="items[{{ $item->id }}][item_id]"
                                value="{{ $item->id }}"
                                id="dz-brw-{{ $item->id }}"
                                class="h-5 w-5 shrink-0 accent-primary"
                            />
                            <label for="dz-brw-{{ $item->id }}" class="flex-1 cursor-pointer">
                                <span class="block font-medium text-ink">{{ $item->name }}</span>
                                <span class="dz-mono mt-0.5 block text-xs text-ink-3">{{ $item->sku }} — stok tersisa {{ $item->quantity }}</span>
                            </label>
                            <input
                                type="number"
                                name="items[{{ $item->id }}][quantity]"
                                min="1"
                                value="1"
                                class="dz-input w-24"
                                aria-label="Jumlah pinjaman {{ $item->name }}"
                            />
                            <select
                                name="items[{{ $item->id }}][condition_before]"
                                class="dz-input w-40"
                                aria-label="Kondisi sebelum {{ $item->name }}"
                            >
                                @foreach ($conditions as $condition)
                                    <option value="{{ $condition->value }}">{{ $condition->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>
                @error('items') <p class="dz-error" role="alert">{{ $message }}</p> @enderror
            </section>

            <div class="flex items-center justify-end gap-3 border-t border-line pt-6">
                <a href="{{ route('borrowings.index') }}" class="dz-btn dz-btn-ghost">Batal</a>
                <button type="submit" class="dz-btn dz-btn-primary" onclick="this.disabled = true; this.form.submit();">
                    Buat Peminjaman
                </button>
            </div>
        </form>
    </main>
</x-layouts.app>
