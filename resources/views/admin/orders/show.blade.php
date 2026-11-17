@extends('admin.layouts.app')

@section('title', 'Detail Pesanan')
@section('heading', 'Detail Pesanan')

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

        $payment  = $order->payment;
        $shipment = $order->shipment;
        [$oLabel, $oClass] = $orderStatus[$order->status] ?? [$order->status, 'bg-slate-100 text-slate-700'];
        [$pLabel, $pClass] = $paymentStatus[$payment->status ?? ''] ?? ['-', 'bg-slate-100 text-slate-500'];
        $field = 'w-full rounded-xl border border-green-100 px-3 py-2 text-sm outline-none focus:border-[#315d40]';
    @endphp

    <a href="{{ route('admin.orders.index') }}" class="text-sm muted hover:underline">← Kembali ke pesanan</a>

    <div class="mb-5 mt-3 flex flex-wrap items-center justify-between gap-3">
        <div>
            <div class="text-2xl font-extrabold green">{{ $order->order_code }}</div>
            <div class="text-xs muted">Dibuat {{ $order->created_at?->format('d M Y, H:i') }}</div>
        </div>
        <span class="rounded-full px-4 py-1.5 text-sm font-semibold {{ $oClass }}">{{ $oLabel }}</span>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <div class="card overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-green-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Produk</th>
                            <th class="px-5 py-3">Harga</th>
                            <th class="px-5 py-3">Qty</th>
                            <th class="px-5 py-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr class="border-t border-green-50">
                                <td class="px-5 py-3 font-semibold green">{{ $item->product->name ?? 'Produk dihapus' }}</td>
                                <td class="px-5 py-3">{{ $rp($item->price) }}</td>
                                <td class="px-5 py-3">{{ $item->quantity }}</td>
                                <td class="px-5 py-3 text-right">{{ $rp($item->subtotal) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-green-100 bg-green-50/50">
                            <td colspan="3" class="px-5 py-3 font-bold">Total pesanan</td>
                            <td class="px-5 py-3 text-right font-bold green">{{ $rp($order->total_price) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if ($order->notes)
                <div class="card p-5">
                    <div class="mb-1 font-bold green">Catatan pelanggan</div>
                    <p class="whitespace-pre-line text-sm">{{ $order->notes }}</p>
                </div>
            @endif
        </div>

        <div class="space-y-5">
            <div class="card p-5 text-sm">
                <div class="mb-2 font-bold green">Pelanggan</div>
                <div class="font-semibold">{{ $order->user->name ?? '-' }}</div>
                <div class="muted">{{ $order->user->email ?? '' }}</div>
                <div class="muted">{{ $order->user->phone ?? '' }}</div>
            </div>

            <div class="card p-5 text-sm">
                <div class="mb-2 font-bold green">Pengiriman</div>
                @if ($shipment)
                    <div class="font-semibold">{{ $shipment->recipient_name }}</div>
                    <div class="muted">{{ $shipment->phone }}</div>
                    <div class="mt-1 whitespace-pre-line">{{ $shipment->address }}</div>
                    <hr class="my-3 border-green-100">
                    <div>Kurir: <span class="font-semibold">{{ $shipment->courier ?: '-' }}</span></div>
                    <div>No. resi: <span class="font-semibold">{{ $shipment->tracking_number ?: '-' }}</span></div>
                    <div>Ongkir: <span class="font-semibold">{{ $rp($shipment->shipping_cost) }}</span></div>
                    <div>Status kirim: <span class="font-semibold">{{ ucfirst($shipment->status) }}</span></div>
                @else
                    <p class="muted">Data pengiriman tidak ditemukan.</p>
                @endif
            </div>

            <div class="card p-5 text-sm">
                <div class="mb-2 flex items-center justify-between">
                    <div class="font-bold green">Pembayaran</div>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $pClass }}">{{ $pLabel }}</span>
                </div>
                @if ($payment)
                    <div>Metode: <span class="font-semibold">{{ $payment->payment_method }}</span></div>
                    <div>Jumlah: <span class="font-semibold">{{ $rp($payment->amount) }}</span></div>
                    @if ($payment->paid_at)
                        <div>Dibayar: <span class="font-semibold">{{ \Illuminate\Support\Carbon::parse($payment->paid_at)->format('d M Y, H:i') }}</span></div>
                    @endif

                    @if ($payment->proof_image)
                        <a href="{{ asset('storage/' . $payment->proof_image) }}" target="_blank" class="mt-3 block">
                            <img src="{{ asset('storage/' . $payment->proof_image) }}" class="h-32 rounded-lg object-cover" alt="Bukti pembayaran">
                        </a>
                    @else
                        <p class="mt-2 text-xs muted">Belum ada bukti pembayaran.</p>
                    @endif

                    @if (\Illuminate\Support\Facades\Route::has('admin.payments.show'))
                        <a href="{{ route('admin.payments.show', $payment) }}" class="mt-3 inline-block text-xs font-semibold green hover:underline">
                            Kelola pembayaran →
                        </a>
                    @endif
                @else
                    <p class="muted">Data pembayaran tidak ditemukan.</p>
                @endif
            </div>

            <div class="card p-5">
                <div class="mb-3 font-bold green">Ubah Status</div>

                @if (empty($allowed))
                    <p class="text-sm muted">Status ini sudah final dan tidak bisa diubah.</p>
                @else
                    @if (($payment->status ?? null) !== 'paid')
                        <p class="mb-3 rounded-lg bg-amber-50 p-3 text-xs text-amber-700">
                            Pembayaran belum berstatus lunas. Pastikan pembayaran sudah diterima sebelum memproses pesanan.
                        </p>
                    @endif

                    <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="space-y-3"
                          onsubmit="return this.status.value !== 'cancelled' || confirm('Batalkan pesanan ini? Stok produk akan dikembalikan.')">
                        @csrf
                        @method('PATCH')

                        <select name="status" class="{{ $field }}">
                            @foreach ($allowed as $next)
                                <option value="{{ $next }}">{{ $orderStatus[$next][0] }}</option>
                            @endforeach
                        </select>

                        @if (in_array('shipped', $allowed, true))
                            <input type="text" name="courier" value="{{ old('courier', $shipment->courier ?? '') }}"
                                   placeholder="Kurir (diisi saat status Dikirim)" class="{{ $field }}">
                            <input type="text" name="tracking_number" value="{{ old('tracking_number', $shipment->tracking_number ?? '') }}"
                                   placeholder="Nomor resi (diisi saat status Dikirim)" class="{{ $field }}">
                        @endif

                        <button class="btn w-full text-sm">Simpan Status</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection