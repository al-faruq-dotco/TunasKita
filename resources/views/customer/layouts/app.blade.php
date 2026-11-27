<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'TunasKita')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Inter, sans-serif;
            background: #fafbf6
        }

        .green {
            color: #244d35
        }

        .btn {
            background: #315d40;
            color: white;
            border-radius: 999px;
            padding: .65rem 1.1rem
        }

        .card {
            background: white;
            border: 1px solid #e8ecdf;
            border-radius: 18px;
            overflow: hidden
        }

        .muted {
            color: #718071
        }
    </style>
</head>

<body class="min-h-screen flex flex-col bg-[#F7F9F5] text-gray-800">
    <header class="bg-white border-b border-green-100 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-5 py-4 flex items-center justify-between gap-4">
            <a href="{{ route('customer.home') }}" class="text-2xl font-extrabold green">🌱 TunasKita</a>

            <nav class="hidden md:flex items-center gap-7 text-sm font-semibold">
                <a href="{{ route('customer.home') }}">Beranda</a>
                <a href="{{ route('customer.products.index') }}">Katalog</a>
                <a href="{{ route('customer.orders.index') }}">Pesanan</a>
                <a href="{{ route('customer.home') }}#panduan">Artikel</a>
                <a href="{{ route('customer.home') }}#tentang">Tentang Kami</a>
            </nav>

            <div class="flex items-center gap-3 text-sm">
                <a href="{{ route('customer.cart.index') }}">🛒 Keranjang</a>
                <a href="{{ route('customer.wishlist.index') }}">♡ Favorit</a>
                <a class="font-semibold green" href="{{ route('customer.profile.edit') }}">{{ auth()->user()->name }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-600 font-semibold">Keluar</button>
                </form>
            </div>
        </div>
    </header>
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 py-8">
        @if (session('success'))
            <div class="mb-5 p-3 rounded-xl bg-green-100 text-green-800">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="mb-5 p-3 rounded-xl bg-red-100 text-red-700">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-5 p-3 rounded-xl bg-red-50 text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @yield('content')
    </main>
    <footer class="mt-14 bg-[#214631] text-green-50">
        <div class="max-w-7xl mx-auto px-5 py-10 grid md:grid-cols-4 gap-8">
            <div>
                <h3 class="font-bold text-xl">🌱 TunasKita</h3>
                <p class="text-sm mt-2 text-green-100">Temukan bibit anggur berkualitas untuk kebun rumah Anda.</p>
            </div>
            <div><b>Navigasi</b>
                <p class="mt-2 text-sm"><a href="{{ route('customer.products.index') }}">Katalog Bibit</a> · <a
                        href="{{ route('customer.orders.index') }}">Pesanan Saya</a></p>
            </div>
            <div id="tentang"><b>Tentang Kami</b>
                <p class="mt-2 text-sm text-green-100">Tumbuh bersama, mulai dari satu bibit.</p>
            </div>
            <div>
                <h3 class="font-bold text-white mb-2">Kontak Kami</h3>
                <ul class="space-y-1 text-sm text-green-100">
                    <li>📍 Jl. Permadi IV No.2, Delima, Binawidya, Kota Pekanbaru, Riau 28292</li>
                    <li>📞 +62 852-7821-1898</li>
                    <li>⏱️ Hours: Open · Closes 5.00 pm</li>
                    <li>✉️ tunaskita@gmail.com</li>
                    <li>📷 @tunaskita.id</li>
                </ul>
            </div>
        </div>
    </footer>
</body>

</html>
