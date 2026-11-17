@php $field = 'w-full rounded-xl border border-green-100 px-4 py-2.5 outline-none focus:border-[#315d40]'; @endphp

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-semibold">Nama</label>
        <input type="text" name="name" value="{{ old('name', $account->name ?? '') }}" required maxlength="150" class="{{ $field }}">
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold">Email</label>
        <input type="email" name="email" value="{{ old('email', $account->email ?? '') }}" required maxlength="150" class="{{ $field }}">
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold">Telepon (opsional)</label>
        <input type="text" name="phone" value="{{ old('phone', $account->phone ?? '') }}" maxlength="25" class="{{ $field }}">
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold">
            Password {{ $account ? '(kosongkan jika tidak diubah)' : '' }}
        </label>
        <input type="password" name="password" {{ $account ? '' : 'required' }} minlength="6" autocomplete="new-password" class="{{ $field }}">
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-semibold">Alamat (opsional)</label>
        <textarea name="address" rows="3" class="{{ $field }}">{{ old('address', $account->address ?? '') }}</textarea>
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button class="btn text-sm">Simpan</button>
    <a href="{{ route($routeBase . '.index') }}" class="text-sm muted hover:underline">Batal</a>
</div>