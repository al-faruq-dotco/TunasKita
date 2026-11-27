@extends('customer.layouts.app')
@section('title', 'Detail Pesanan')
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
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-3xl font-bold text-[#244d35]">Detail Pesanan</h1>
        <span class="rounded-full bg-green-100 px-4 py-2 text-green-800">{{ $statusLabel[$order->status] ?? ucfirst($order->status) }}</span>
    </div>
    <div class="grid gap-5 md:grid-cols-3">
        <section class="rounded-3xl border border-green-100 bg-white p-5 md:col-span-2">
            <b>{{ $order->order_code }}</b>
            <p class="text-sm text-[#718071]">{{ $order->created_at->format('d M Y H:i') }}</p>
            @foreach ($order->items as $item)
                <div class="flex justify-between border-b border-green-50 py-4">
                    <span>{{ $item->product->name }} × {{ $item->quantity }}</span>
                    <b>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</b>
                </div>
            @endforeach
            <div class="mt-5 flex justify-between text-sm"><span>Ongkos kirim</span><span>Rp {{ number_format($order->shipment?->shipping_cost ?? 0, 0, ',', '.') }}</span></div>
            <div class="mt-2 flex justify-between text-xl font-bold"><span>Total</span><span class="text-[#244d35]">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span></div>
        </section>
        <aside class="rounded-3xl border border-green-100 bg-white p-5">
            <b>Pengiriman</b>
            <p class="mt-3 text-sm">{{ $order->shipment?->recipient_name }}</p>
            <p class="text-sm">{{ $order->shipment?->phone }}</p>
            <p class="text-sm">{{ $order->shipment?->address }}</p>
            <p class="mt-3 text-sm">Kurir: <b>{{ $order->shipment?->courier ?? '-' }}</b></p>
            <p class="text-sm">Nomor resi: <b>{{ $order->shipment?->tracking_number ?? '-' }}</b></p>
            @if ($order->shipment?->tracking?->isNotEmpty())
                <div class="mt-5 border-t border-green-50 pt-4">
                    <h2 class="font-bold text-[#244d35]">Riwayat pengiriman</h2>
                    <ol class="mt-3 space-y-3">
                        @foreach ($order->shipment->tracking->sortByDesc('tracked_at') as $tracking)
                            <li class="border-l-2 border-green-200 pl-3 text-sm">
                                <p class="font-semibold">{{ ucfirst($tracking->status) }}</p>
                                <p class="text-[#718071]">{{ $tracking->description }}</p>
                                <time class="text-xs text-[#718071]">{{ $tracking->tracked_at?->format('d M Y H:i') }}</time>
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endif
            <hr class="my-4 border-green-50">
            <b>Pembayaran</b>
            <p class="text-sm">{{ $order->payment?->payment_method }} · {{ ucfirst($order->payment?->status ?? '-') }}</p>
            @if ($order->status === 'pending')
                <form method="POST" action="{{ route('customer.orders.cancel', $order) }}" class="mt-5">
                    @csrf
                    @method('PATCH')
                    <button class="text-sm text-red-600" onclick="return confirm('Batalkan pesanan ini?')">Batalkan Pesanan</button>
                </form>
            @endif
        </aside>
    </div>
@endsection
