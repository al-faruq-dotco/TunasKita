@extends('admin.layouts.app')

@section('title', 'Pesanan')
@section('heading', 'Pesanan')

@section('content')
    @php
        $orderStatus = [
            'pending'    => ['Menunggu',     'bg-amber-50 text-amber-700'],
            'confirmed'  => ['Dikonfirmasi', 'bg-blue-50 text-blue-700'],
            'processing' => ['Diproses',     'bg-blue-50 text-blue-700'],
            'shipped'    => ['Dikirim',      'bg-indigo-50 text-indigo-700'],
            'completed'  => ['Selesai',      'bg-green-100 text-green-800'],
            'cancelled'  => ['Dibatalkan',   'bg-red-50 text-red-700'],
        ];
        $paymentStatus = [
            'pending'  => ['Belum bayar',  'bg-amber-50 text-amber-700'],
            'paid'     => ['Lunas',        'bg-green-100 text-green-800'],
            'failed'   => ['Gagal',        'bg-red-50 text-red-700'],
            'refunded' => ['Dikembalikan', 'bg-slate-100 text-slate-600'],
        ];
        $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
    @endphp

    <form method="GET" class="mb-4 flex flex-wrap gap-2 text-sm">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode atau nama pelanggan..."
               class="rounded-xl border border-green-100 px-3 py-2 outline-none focus:border-[#315d40]">
        <select name="status" class="rounded-xl border border-green-100 px-3 py-2">
            <option value="">Semua status</option>
            @foreach ($orderStatus as $value => [$label])
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="btn text-sm">Filter</button>
    </form>

    <div class="card overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-green-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-5 py-3">Kode</th>
                    <th class="px-5 py-3">Pelanggan</th>
                    <th class="px-5 py-3">Total</th>
                    <th class="px-5 py-3">Pembayaran</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Tanggal</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    @php
                        [$oLabel, $oClass] = $orderStatus[$order->status] ?? [$order->status, 'bg-slate-100 text-slate-700'];
                        [$pLabel, $pClass] = $paymentStatus[$order->payment->status ?? ''] ?? ['-', 'bg-slate-100 text-slate-500'];
                    @endphp
                    <tr class="border-t border-green-50">
                        <td class="px-5 py-3 font-semibold green">{{ $order->order_code }}</td>
                        <td class="px-5 py-3">{{ $order->user->name ?? '-' }}</td>
                        <td class="px-5 py-3">{{ $rp($order->total_price) }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $pClass }}">{{ $pLabel }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $oClass }}">{{ $oLabel }}</span>
                        </td>
                        <td class="px-5 py-3 muted">{{ $order->created_at?->format('d M Y, H:i') }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}"
                               class="rounded-lg border border-green-100 px-3 py-1.5 hover:bg-green-50">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center muted">Belum ada pesanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
@endsection