<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#24472f">
    <title>Masuk — TunasKita</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#f4f6ef] text-[#25352a] antialiased">

    <main class="mx-auto grid min-h-screen max-w-7xl items-center gap-8 px-4 py-6 sm:px-8 lg:grid-cols-[1.05fr_.95fr] lg:gap-12 lg:py-10">

        @include('auth._brand-panel', [
            'heading' => 'Selamat datang kembali.',
            'description' => 'Masuk untuk menemukan bibit yang tepat dan melanjutkan perjalanan berkebun Anda.',
        ])

        <section class="mx-auto w-full max-w-lg rounded-[2rem] border border-[#e5e9dd] bg-white p-6 shadow-[0_24px_80px_-48px_rgba(36,71,47,.45)] sm:p-10">

            <a href="{{ url('/') }}" class="mb-8 inline-flex items-center gap-2 font-extrabold text-[#24472f] lg:hidden">
                <span class="text-2xl">🌱</span> TunasKita
            </a>

            <p class="text-sm font-bold uppercase tracking-[.18em] text-[#758775]">
                Akun TunasKita
            </p>

            <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-[#24472f]">
                Masuk ke akun
            </h1>

            <p class="mt-2 text-sm leading-6 text-[#6f7e71]">
                Gunakan email dan password akun Anda.
            </p>

            {{-- Notifikasi sukses --}}
            @if (session('success'))
                <div role="status" class="mt-6 rounded-xl border border-green-200 bg-green-50 p-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Notifikasi error --}}
            @if ($errors->any())
                <div role="alert" class="mt-6 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Form Login --}}
            <form action="{{ route('login.process') }}" method="POST" class="mt-7 space-y-5">

                @csrf

                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold">
                        Email
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        autocomplete="username"
                        required
                        autofocus
                        class="w-full rounded-xl border border-[#dce4d8] bg-[#fcfdfb] px-4 py-3 outline-none transition focus:border-[#477957] focus:ring-4 focus:ring-[#477957]/10"
                    >
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between">
                        <label for="password" class="text-sm font-semibold">
                            Password
                        </label>
                    </div>

                    <div class="relative">

                        <input
                            id="password"
                            type="password"
                            name="password"
                            autocomplete="current-password"
                            required
                            class="w-full rounded-xl border border-[#dce4d8] bg-[#fcfdfb] px-4 py-3 pr-20 outline-none transition focus:border-[#477957] focus:ring-4 focus:ring-[#477957]/10"
                        >

                        <button
                            type="button"
                            data-password-toggle="password"
                            class="absolute inset-y-0 right-3 my-auto h-9 rounded-lg px-2 text-xs font-bold text-[#477957] hover:bg-green-50"
                        >
                            Tampilkan
                        </button>

                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full rounded-full bg-[#315b3c] px-5 py-3.5 font-bold text-white shadow-sm transition hover:bg-[#24472f] focus:outline-none focus:ring-4 focus:ring-[#477957]/20"
                >
                    Masuk
                </button>

            </form>

            <p class="mt-7 text-center text-sm text-[#6f7e71]">
                Belum punya akun?

                <a
                    href="{{ route('register') }}"
                    class="font-bold text-[#315b3c] hover:underline"
                >
                    Daftar sebagai Customer
                </a>
            </p>

        </section>

    </main>

</body>
</html>