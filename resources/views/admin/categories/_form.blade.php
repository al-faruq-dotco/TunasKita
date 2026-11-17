<div class="space-y-4">
    <div>
        <label class="mb-1 block text-sm font-semibold">Nama kategori</label>
        <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required maxlength="100"
               class="w-full rounded-xl border border-green-100 px-4 py-2.5 outline-none focus:border-[#315d40]">
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold">Deskripsi (opsional)</label>
        <textarea name="description" rows="4"
                  class="w-full rounded-xl border border-green-100 px-4 py-2.5 outline-none focus:border-[#315d40]">{{ old('description', $category->description ?? '') }}</textarea>
    </div>

    <div class="flex items-center gap-3 pt-2">
        <button class="btn text-sm">Simpan</button>
        <a href="{{ route('admin.categories.index') }}" class="text-sm muted hover:underline">Batal</a>
    </div>
</div>