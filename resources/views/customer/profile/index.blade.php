@extends('customer.layouts.app')
@section('title', 'Profil Saya')
@section('content')
    <h1 class="text-3xl font-bold green mb-6">Profil Saya</h1>

    <div class="grid md:grid-cols-2 gap-6">
        <form class="card p-6" method="POST" action="{{ route('customer.profile.update') }}">
            @csrf
            @method('PUT')
            <h2 class="font-bold text-xl mb-4">Informasi Akun</h2>

            <label class="block text-sm mb-1" for="name">Nama</label>
            <input id="name" name="name" value="{{ old('name', $user->name) }}" required class="border rounded-xl p-3 w-full mb-4">

            <label class="block text-sm mb-1" for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required class="border rounded-xl p-3 w-full mb-4">

            <label class="block text-sm mb-1" for="phone">Telepon</label>
            <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="border rounded-xl p-3 w-full mb-4">

            <label class="block text-sm mb-1" for="address">Alamat</label>
            <textarea id="address" name="address" class="border rounded-xl p-3 w-full mb-5">{{ old('address', $user->address) }}</textarea>

            <button class="btn">Simpan Profil</button>
        </form>

        <form class="card p-6" method="POST" action="{{ route('customer.profile.password') }}">
            @csrf
            @method('PUT')
            <h2 class="font-bold text-xl mb-4">Ubah Password</h2>

            <label class="block text-sm mb-1" for="current_password">Password saat ini</label>
            <input id="current_password" type="password" name="current_password" required class="border rounded-xl p-3 w-full mb-4">

            <label class="block text-sm mb-1" for="password">Password baru</label>
            <input id="password" type="password" name="password" required minlength="8" class="border rounded-xl p-3 w-full mb-4">

            <label class="block text-sm mb-1" for="password_confirmation">Konfirmasi password baru</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="border rounded-xl p-3 w-full mb-5">

            <button class="btn">Ubah Password</button>
        </form>
    </div>
@endsection
