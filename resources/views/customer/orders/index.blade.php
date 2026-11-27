@extends('customer.layouts.app')
@section('title', 'Pesanan Saya')
@section('content')
    @php
        $statusLabel = [
            'pending' => 'Menunggu',
            'confirmed' => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];
    @endphp

    <h1 class="text-3xl font-bold green mb-6">Pesanan Saya</h1>

    <div class="space-y-4">
        @forelse ($orders as $order)
            <a href="{{ route('customer.orders.show', $order) }}" class="card p-5 flex flex-col md:flex-row justify-between gap-3">
                <div>
                    <b>{{ $order->order_code }}</b>
                    <p class="text-sm muted">
                        {{ $order->created_at->format('d M Y H:i') }} · {{ $order->items->count() }} jenis produk
                    </p>
                </div>
                <div class="md:text-right">
                    <b class="green">Rp {{ number_format($order->total_price, 0, ',', '.') }}</b>
                    <p class="text-sm">Status: {{ $statusLabel[$order->status] ?? ucfirst($order->status) }}</p>
                </div>
            </a>
        @empty
            <p class="muted">Kamu belum memiliki pesanan.</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $orders->links() }}</div>
@endsection
