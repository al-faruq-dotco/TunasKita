@php
    $menu = [
        ['label' => 'Dashboard',         'icon' => '🏠', 'route' => 'admin.dashboard',              'active' => 'admin.dashboard'],
        ['label' => 'Data Pengguna',     'icon' => '👥', 'route' => 'admin.users.index',            'active' => 'admin.users.*'],
        ['label' => 'Data Penjual',      'icon' => '🧑‍🌾', 'route' => 'admin.sellers.index',         'active' => 'admin.sellers.*'],
        ['label' => 'Kategori Produk',   'icon' => '🏷️', 'route' => 'admin.categories.index',       'active' => 'admin.categories.*'],
        ['label' => 'Data Produk',       'icon' => '📦', 'route' => 'admin.products.index',         'active' => 'admin.products.*'],
        ['label' => 'Pesanan',           'icon' => '🧾', 'route' => 'admin.orders.index',           'active' => 'admin.orders.*'],
        ['label' => 'Pembayaran',        'icon' => '💳', 'route' => 'admin.payments.index',         'active' => 'admin.payments.*'],
        ['label' => 'Zona Pengiriman',   'icon' => '🚚', 'route' => 'admin.delivery-zones.index',   'active' => 'admin.delivery-zones.*'],
        ['label' => 'Panduan Perawatan', 'icon' => '🌿', 'route' => 'admin.guides.index',           'active' => 'admin.guides.*'],
        ['label' => 'Kategori Panduan',  'icon' => '📚', 'route' => 'admin.guide-categories.index', 'active' => 'admin.guide-categories.*'],
        ['label' => 'Laporan',           'icon' => '📊', 'route' => 'admin.reports.index',          'active' => 'admin.reports.*'],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - TunasKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: Inter, sans-serif; background: #fafbf6; }
        .green { color: #244d35; }
        .btn { background: #315d40; color: #fff; border-radius: 999px; padding: .65rem 1.1rem; }
        .card { background: #fff; border: 1px solid #e8ecdf; border-radius: 18px; overflow: hidden; }
        .muted { color: #718071; }
    </style>
    @stack('head')
</head>
<body class="text-slate-800">

    {{-- Sidebar --}}
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col bg-[#17382a] text-green-50 transition-transform lg:translate-x-0">
        <div class="px-6 py-6">
            <div class="text-xl font-extrabold">🌱 TunasKita</div>
            <div class="text-xs text-green-200">Admin</div>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3">
            @foreach ($menu as $item)
                @php
                    $exists = \Illuminate\Support\Facades\Route::has($item['route']);
                    $active = $exists && request()->routeIs($item['active']);
                @endphp

                @if ($exists)
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition
                              {{ $active ? 'bg-[#2f6b4c] text-white' : 'text-green-100 hover:bg-[#214631]' }}">
                        <span>{{ $item['icon'] }}</span> {{ $item['label'] }}
                    </a>
                @else
                    <span class="flex cursor-not-allowed items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium text-green-100/40">
                        <span>{{ $item['icon'] }}</span> {{ $item['label'] }}
                    </span>
                @endif
            @endforeach
        </nav>
    </aside>

    <div class="lg:pl-64">
        {{-- Topbar --}}
        <header class="sticky top-0 z-40 flex items-center justify-between border-b border-green-100 bg-white px-5 py-3">
            <div class="flex items-center gap-3">
                <button type="button" class="rounded-lg border border-green-100 px-3 py-1.5 lg:hidden"
                        onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')">☰</button>
                <div>
                    <div class="font-bold green">@yield('heading', 'Dashboard Admin')</div>
                    <div class="text-xs muted">Selamat datang, {{ auth()->user()->name }} 👋</div>
                </div>
            </div>

            <div class="flex items-center gap-3 text-sm">
                <div class="hidden text-right sm:block">
                    <div class="font-semibold">{{ auth()->user()->name }}</div>
                    <div class="text-xs muted">Super Admin</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn text-sm">Logout</button>
                </form>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-5 py-7">
            @if (session('success'))
                <div class="mb-5 rounded-xl bg-green-100 p-3 text-green-800">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-5 rounded-xl bg-red-50 p-3 text-red-700">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-5 rounded-xl bg-red-50 p-3 text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>