<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#24472f">
    <title>Daftar — TunasKita</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f4f6ef] text-[#25352a] antialiased">
    <main class="mx-auto grid min-h-screen max-w-7xl items-center gap-8 px-4 py-6 sm:px-8 lg:grid-cols-[1.05fr_.95fr] lg:gap-12 lg:py-10">
        @include('auth._brand-panel', [
            'heading' => 'Mulai tumbuhkan kebun impian.',
            'description' => 'Buat akun Customer untuk menjelajahi bibit anggur, menyimpan favorit, dan memantau pesanan.',
        ])
        <section class="mx-auto w-full max-w-lg rounded-[2rem] border border-[#e5e9dd] bg-white p-6 shadow-[0_24px_80px_-48px_rgba(36,71,47,.45)] sm:p-10">
            <a href="{{ url('/') }}" class="mb-6 inline-flex items-center gap-2 font-extrabold text-[#24472f] lg:hidden"><span class="text-2xl">🌱</span> TunasKita</a>
            <p class="text-sm font-bold uppercase tracking-[.18em] text-[#758775]">Bergabung dengan kami</p>
            <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-[#24472f]">Buat akun Customer</h1>
            <p class="mt-2 text-sm leading-6 text-[#6f7e71]">Isi informasi berikut untuk mulai berbelanja.</p>

            @if ($errors->any())
                <div role="alert" class="mt-5 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                    @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <form action="{{ route('register.process') }}" method="POST" class="mt-6 grid gap-4 sm:grid-cols-2">
                @csrf
                <div class="sm:col-span-2">
                    <label for="name" class="mb-2 block text-sm font-semibold">Nama lengkap</label>
                    <input id="name" name="name" value="{{ old('name') }}" autocomplete="name" required class="w-full rounded-xl border border-[#dce4d8] bg-[#fcfdfb] px-4 py-3 outline-none focus:border-[#477957] focus:ring-4 focus:ring-[#477957]/10">
                </div>
                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required class="w-full rounded-xl border border-[#dce4d8] bg-[#fcfdfb] px-4 py-3 outline-none focus:border-[#477957] focus:ring-4 focus:ring-[#477957]/10">
                </div>
                <div>
                    <label for="phone" class="mb-2 block text-sm font-semibold">Nomor telepon</label>
                    <input id="phone" name="phone" value="{{ old('phone') }}" autocomplete="tel" class="w-full rounded-xl border border-[#dce4d8] bg-[#fcfdfb] px-4 py-3 outline-none focus:border-[#477957] focus:ring-4 focus:ring-[#477957]/10">
                </div>
                <div class="sm:col-span-2">
                    <label for="address" class="mb-2 block text-sm font-semibold">Alamat</label>
                    <textarea id="address" name="address" autocomplete="street-address" rows="2" class="w-full rounded-xl border border-[#dce4d8] bg-[#fcfdfb] px-4 py-3 outline-none focus:border-[#477957] focus:ring-4 focus:ring-[#477957]/10">{{ old('address') }}</textarea>
                </div>
                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold">Password</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" autocomplete="new-password" required class="w-full rounded-xl border border-[#dce4d8] bg-[#fcfdfb] px-4 py-3 pr-16 outline-none focus:border-[#477957] focus:ring-4 focus:ring-[#477957]/10">
                        <button type="button" data-password-toggle="password" class="absolute inset-y-0 right-2 my-auto h-9 rounded-lg px-2 text-xs font-bold text-[#477957] hover:bg-green-50">Lihat</button>
                    </div>
                </div>
                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-semibold">Konfirmasi password</label>
                    <div class="relative">
                        <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required class="w-full rounded-xl border border-[#dce4d8] bg-[#fcfdfb] px-4 py-3 pr-16 outline-none focus:border-[#477957] focus:ring-4 focus:ring-[#477957]/10">
                        <button type="button" data-password-toggle="password_confirmation" class="absolute inset-y-0 right-2 my-auto h-9 rounded-lg px-2 text-xs font-bold text-[#477957] hover:bg-green-50">Lihat</button>
                    </div>
                </div>
                <button class="mt-2 w-full rounded-full bg-[#315b3c] px-5 py-3.5 font-bold text-white shadow-sm transition hover:bg-[#24472f] focus:outline-none focus:ring-4 focus:ring-[#477957]/20 sm:col-span-2">Buat akun</button>
            </form>
            <p class="mt-6 text-center text-sm text-[#6f7e71]">Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-[#315b3c] hover:underline">Masuk</a></p>
        </section>
    </main>
</body>
</html>
