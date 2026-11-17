@php
    $field = 'w-full rounded-xl border border-green-100 px-4 py-2.5 outline-none focus:border-[#315d40]';
    $currentSeller = old('seller_id', $product->seller_id ?? '');
    $orphanSeller = $product && $product->seller_id && ! $sellers->contains('id', $product->seller_id);
@endphp

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-semibold">Nama produk</label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required maxlength="150" class="{{ $field }}">
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold">Kategori</label>
        <select name="category_id" required class="{{ $field }}">
            <option value="">-- Pilih kategori --</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id ?? '') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold">Penjual</label>
        <select name="seller_id" required class="{{ $field }}">
            <option value="">-- Pilih penjual --</option>
            @foreach ($sellers as $s)
                <option value="{{ $s->id }}" @selected($currentSeller == $s->id)>{{ $s->name }}</option>
            @endforeach
        </select>
        @if ($sellers->isEmpty())
            <p class="mt-1 text-xs text-red-600">Belum ada penjual. Jalankan SellerSeeder atau tambahkan penjual dulu.</p>
        @elseif ($orphanSeller)
            <p class="mt-1 text-xs text-amber-700">Penjual lama (#{{ $product->seller_id }}) tidak ditemukan. Pilih penjual yang valid.</p>
        @endif
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold">Harga (Rp)</label>
        <input type="number" name="price" value="{{ old('price', isset($product) ? (int) $product->price : '') }}" required min="0" step="any" class="{{ $field }}">
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold">Stok</label>
        <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required min="0" class="{{ $field }}">
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold">Umur tanaman</label>
        <input type="text" name="plant_age" value="{{ old('plant_age', $product->plant_age ?? '') }}" maxlength="80" placeholder="Contoh: 3 Bulan (Grafting)" class="{{ $field }}">
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold">Ukuran tanaman</label>
        <input type="text" name="plant_size" value="{{ old('plant_size', $product->plant_size ?? '') }}" maxlength="80" placeholder="Contoh: 40 - 50 cm" class="{{ $field }}">
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold">Ketersediaan</label>
        <select name="availability" required class="{{ $field }}">
            @foreach (['available' => 'Tersedia', 'out_of_stock' => 'Habis', 'preorder' => 'Pre-order'] as $value => $label)
                <option value="{{ $value }}" @selected(old('availability', $product->availability ?? 'available') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold">Status</label>
        <select name="status" required class="{{ $field }}">
            @foreach (['active' => 'Aktif', 'inactive' => 'Nonaktif'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $product->status ?? 'active') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-semibold">Deskripsi</label>
        <textarea name="description" rows="5" class="{{ $field }}">{{ old('description', $product->description ?? '') }}</textarea>
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-semibold">
            {{ $product ? 'Tambah gambar baru' : 'Gambar produk' }} (maks. 5 file, masing-masing 2 MB)
        </label>
        <input type="file" name="images[]" multiple accept="image/png,image/jpeg,image/webp"
               class="w-full rounded-xl border border-dashed border-green-200 bg-green-50/40 p-3 text-sm">
        @unless ($product)
            <p class="mt-1 text-xs muted">Gambar pertama otomatis menjadi gambar utama.</p>
        @endunless
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button class="btn text-sm">Simpan</button>
    <a href="{{ route('admin.products.index') }}" class="text-sm muted hover:underline">Batal</a>
</div>