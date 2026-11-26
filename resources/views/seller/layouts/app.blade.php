@php
    $nav = [
        ['label' => 'Ringkasan', 'icon' => '▦', 'route' => 'seller.dashboard', 'active' => 'seller.dashboard'],
        ['label' => 'Produk Saya', 'icon' => '▣', 'route' => 'seller.products.index', 'active' => 'seller.products.*'],
        ['label' => 'Pesanan Masuk', 'icon' => '▤', 'route' => 'seller.orders.index', 'active' => 'seller.orders.*'],
        ['label' => 'Profil Toko', 'icon' => '♙', 'route' => 'seller.profile', 'active' => 'seller.profile'],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Seller') · TunasKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:Inter,sans-serif;background:#f7f9f4}.green{color:#244d35}.muted{color:#718071}.card{background:#fff;border:1px solid #e8ecdf;border-radius:18px}.btn{display:inline-flex;align-items:center;justify-content:center;background:#315d40;color:#fff;border-radius:12px;padding:.7rem 1rem;font-weight:600}.btn:hover{background:#244d35}.field{width:100%;border:1px solid #dce6d8;border-radius:11px;padding:.7rem .85rem;outline:none}.field:focus{border-color:#315d40;box-shadow:0 0 0 3px #315d401a}</style>
    @stack('head')
</head>
<body class="text-slate-800">
<aside id="sellerSidebar" class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col bg-[#17382a] text-green-50 transition-transform lg:translate-x-0">
    <div class="px-6 py-6 border-b border-white/10"><a href="{{ route('seller.dashboard') }}" class="text-xl font-extrabold">🌱 TunasKita</a><p class="mt-1 text-xs text-green-200">Seller Workspace</p></div>
    <div class="px-5 py-5"><div class="rounded-2xl bg-white/10 p-3"><p class="text-xs text-green-200">TOKO ANDA</p><p class="mt-1 font-semibold">{{ auth()->user()->name }}</p></div></div>
    <nav class="flex-1 space-y-1 px-3">
        @foreach($nav as $item)
            <a href="{{ route($item['route']) }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs($item['active']) ? 'bg-[#2f6b4c] text-white' : 'text-green-100 hover:bg-[#214631]' }}"><span class="w-5 text-center text-lg">{{ $item['icon'] }}</span>{{ $item['label'] }}</a>
        @endforeach
    </nav>
    <div class="border-t border-white/10 p-4"><form method="POST" action="{{ route('logout') }}">@csrf<button class="w-full rounded-xl border border-white/20 px-4 py-2.5 text-sm font-semibold hover:bg-white/10">Keluar dari akun ↗</button></form></div>
</aside>
<div class="lg:pl-64 min-h-screen">
    <header class="sticky top-0 z-40 flex items-center justify-between border-b border-green-100 bg-white/95 px-5 py-4 backdrop-blur">
        <div class="flex items-center gap-3"><button class="rounded-lg border border-green-100 px-3 py-2 lg:hidden" onclick="document.getElementById('sellerSidebar').classList.toggle('-translate-x-full')">☰</button><div><p class="font-bold green">@yield('heading','Dashboard Penjual')</p><p class="text-xs muted">Kelola toko dan pesanan bibit anggur Anda.</p></div></div>
        <div class="hidden text-right sm:block"><p class="text-sm font-semibold">{{ auth()->user()->name }}</p><p class="text-xs muted">Penjual TunasKita</p></div>
    </header>
    <main class="mx-auto max-w-7xl px-5 py-7">
        @if(session('success'))<div class="mb-5 rounded-xl border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ session('error') }}</div>@endif
        @if($errors->any())<div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
        @yield('content')
    </main>
</div>
@stack('scripts')
</body></html>
