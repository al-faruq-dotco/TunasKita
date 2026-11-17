@php $field = 'w-full rounded-xl border border-green-100 px-4 py-2.5 outline-none focus:border-[#315d40]'; @endphp

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-semibold">Provinsi</label>
        <input type="text" name="province" value="{{ old('province', $zone->province ?? '') }}" required maxlength="100" class="{{ $field }}">
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold">Kota / Kabupaten</label>
        <input type="text" name="city" value="{{ old('city', $zone->city ?? '') }}" required maxlength="100" class="{{ $field }}">
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold">Ongkir (Rp)</label>
        <input type="number" name="shipping_cost" value="{{ old('shipping_cost', isset($zone) ? (int) $zone->shipping_cost : 0) }}"
               required min="0" step="any" class="{{ $field }}">
    </div>

    <div class="flex items-end pb-2">
        <input type="hidden" name="is_active" value="0">
        <label class="flex items-center gap-2 text-sm font-semibold">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $zone->is_active ?? true))>
            Zona aktif
        </label>
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button class="btn text-sm">Simpan</button>
    <a href="{{ route('admin.delivery-zones.index') }}" class="text-sm muted hover:underline">Batal</a>
</div>