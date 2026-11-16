<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Dashboard Admin - TunasKita</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="p-8">
    <h1 class="text-2xl font-bold">Dashboard Admin</h1>
    <ul class="mt-4 space-y-1">
        <li>Pengguna: {{ $totalUsers }}</li>
        <li>Penjual: {{ $totalSellers }}</li>
        <li>Produk: {{ $totalProducts }}</li>
        <li>Pesanan: {{ $totalOrders }}</li>
    </ul>
    <form method="POST" action="{{ route('logout') }}" class="mt-6">
        @csrf
        <button class="rounded bg-gray-800 px-4 py-2 text-white">Logout</button>
    </form>
</body>
</html>