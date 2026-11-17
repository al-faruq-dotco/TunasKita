@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('heading', 'Dashboard Admin')

@php
    $statusLabel = [
        'pending'    => ['Menunggu',  'bg-amber-50 text-amber-700'],
        'confirmed'  => ['Dikonfirmasi', 'bg-blue-50 text-blue-700'],
        'processing' => ['Diproses',  'bg-blue-50 text-blue-700'],
        'shipped'    => ['Dikirim',   'bg-indigo-50 text-indigo-700'],
        'completed'  => ['Selesai',   'bg-green-100 text-green-800'],
        'cancelled'  => ['Dibatalkan', 'bg-red-50 text-red-700'],
    ];
    $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
@endphp

@section('content')
    {{-- Stat cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($stats as $stat)
            <div class="card p-5">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 text-2xl">{{ $stat['icon'] }}</div>
                    <div>
                        <div class="text-2xl font-extrabold green">{{ number_format($stat['total'], 0, ',', '.') }}</div>
                        <div class="text-sm text-slate-600">{{ $stat['label'] }}</div>
                    </div>
                </div>
                <div class="mt-3 text-xs text-green-700">+{{ $stat['new'] }} bulan ini</div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
        {{-- Sales chart --}}
        <div class="card p-5 lg:col-span-2">
            <div class="font-bold green">Grafik Penjualan</div>
            <div class="text-xs muted">Total pesanan (tidak termasuk yang dibatalkan), 6 bulan terakhir</div>
            <div class="relative mt-4 h-64">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        {{-- Recent orders --}}
        <div class="card p-5">
            <div class="mb-3 font-bold green">Transaksi Terbaru</div>
            @forelse ($recentOrders as $order)
                @php [$label, $class] = $statusLabel[$order->status] ?? [$order->status, 'bg-slate-100 text-slate-700']; @endphp
                <div class="flex items-center justify-between border-b border-green-50 py-3 last:border-0">
                    <div>
                        <div class="text-sm font-semibold">{{ $order->order_code }}</div>
                        <div class="text-xs muted">{{ $order->customer_name ?? '-' }} · {{ $rp($order->total_price) }}</div>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $class }}">{{ $label }}</span>
                </div>
            @empty
                <p class="py-6 text-center text-sm muted">Belum ada transaksi.</p>
            @endforelse
        </div>
    </div>

    {{-- Best sellers --}}
    <div class="card mt-6 p-5">
        <div class="mb-3 font-bold green">Produk Terlaris</div>
        @forelse ($bestSellers as $product)
            <div class="flex items-center justify-between border-b border-green-50 py-3 last:border-0">
                <div>
                    <div class="text-sm font-semibold">{{ $product->name }}</div>
                    <div class="text-xs muted">{{ $rp($product->price) }}</div>
                </div>
                <div class="text-sm font-semibold green">{{ $product->sold }} terjual</div>
            </div>
        @empty
            <p class="py-6 text-center text-sm muted">Belum ada produk yang terjual.</p>
        @endforelse
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        const sales = @json($salesChart);
        new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: sales.map(s => s.label),
                datasets: [{
                    data: sales.map(s => s.total),
                    borderColor: '#315d40',
                    backgroundColor: 'rgba(49, 93, 64, 0.12)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3,
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: v => 'Rp ' + Number(v).toLocaleString('id-ID') }
                    }
                }
            }
        });
    </script>
@endpush