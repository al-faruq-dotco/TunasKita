@php $field = 'w-full rounded-xl border border-green-100 px-4 py-2.5 outline-none focus:border-[#315d40]'; @endphp

<div class="space-y-4">
    <div>
        <label class="mb-1 block text-sm font-semibold">Judul</label>
        <input type="text" name="title" value="{{ old('title', $guide->title ?? '') }}" required maxlength="150" class="{{ $field }}">
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold">Kategori</label>
        <select name="guide_category_id" class="{{ $field }}">
            <option value="">-- Tanpa kategori --</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" @selected(old('guide_category_id', $guide->guide_category_id ?? '') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold">Isi panduan</label>
        <textarea name="content" rows="14" required class="{{ $field }}">{{ old('content', $guide->content ?? '') }}</textarea>
        <p class="mt-1 text-xs muted">Teks biasa. Baris kosong memisahkan paragraf.</p>
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold">Gambar (opsional, maks. 2 MB)</label>

        @if (! empty($guide?->image))
            <div class="mb-2 flex items-center gap-4">
                <img src="{{ asset('storage/' . $guide->image) }}" class="h-24 rounded-lg object-cover" alt="">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="remove_image" value="1"> Hapus gambar ini
                </label>
            </div>
        @endif

        <input type="file" name="image" accept="image/png,image/jpeg,image/webp"
               class="w-full rounded-xl border border-dashed border-green-200 bg-green-50/40 p-3 text-sm">
        @if (! empty($guide?->image))
            <p class="mt-1 text-xs muted">Mengunggah gambar baru akan mengganti yang lama.</p>
        @endif
    </div>

    <div class="flex items-center gap-3 pt-2">
        <button class="btn text-sm">Simpan</button>
        <a href="{{ route('admin.guides.index') }}" class="text-sm muted hover:underline">Batal</a>
    </div>
</div>