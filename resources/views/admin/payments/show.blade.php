@extends('admin.layouts.app')

@section('title', 'Detail Pembayaran')
@section('heading', 'Detail Pembayaran')

@section('content')
    @php
        $paymentStatus = [
            'pending'  => ['Belum bayar',  'bg-amber-50 text-amber-700'],
            'paid'     => ['Lunas',        'bg-green-100 text-green-800'],
            'failed'   => ['Gagal',        'bg-red-50 text-red-700'],
            'refunded' => ['Dikembalikan', 'bg-slate-100 text-slate-600'],
        ];
        $actionLabel = [
            'paid'     => 'Tandai lunas',
            'failed'   => 'Tandai gagal',
            'refunded' => 'Tandai dikembalikan',
        ];
        $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
        $order = $payment->order;
        [$label, $class] = $paymentStatus[$payment->status] ?? [$payment->status, 'bg-slate-100 text-slate-700'];
        $field = 'w-full rounded-xl border border-green-100 px-3 py-2 text-sm outline-none focus:border-[#315d40]';
    @endphp

    <a href="{{ route('admin.payments.index') }}" class="text-sm muted hover:underline">← Kembali ke pembayaran</a>

    <div class="mb-5 mt-3 flex flex-wrap items-center justify-between gap-3">
        <div>
            <div class="text-2xl font-extrabold green">{{ $order->order_code }}</div>
            <div class="text-xs muted">Pesanan dibuat {{ $order->created_at?->format('d M Y, H:i') }}</div>
        </div>
        <span class="rounded-full px-4 py-1.5 text-sm font-semibold {{ $class }}">{{ $label }}</span>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <div class="card p-5 text-sm">
                <div class="mb-3 font-bold green">Rincian Pembayaran</div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div><span class="muted">Metode</span><div class="font-semibold">{{ $payment->payment_method }}</div></div>
                    <div><span class="muted">Jumlah</span><div class="font-semibold">{{ $rp($payment->amount) }}</div></div>
                    <div>
                        <span class="muted">Dibayar pada</span>
                        <div class="font-semibold">
                            {{ $payment->paid_at ? \Illuminate\Support\Carbon::parse($payment->paid_at)->format('d M Y, H:i') : '-' }}
                        </div>
                    </div>
                    <div><span class="muted">Total pesanan</span><div class="font-semibold">{{ $rp($order->total_price) }}</div></div>
                </div>
            </div>

            <div class="card p-5">
                <div class="mb-3 font-bold green">Bukti Pembayaran</div>
                @if ($payment->proof_image)
                    <a href="{{ asset('storage/' . $payment->proof_image) }}" target="_blank">
                        <img src="{{ asset('storage/' . $payment->proof_image) }}"
                             class="max-h-96 rounded-xl border border-green-100 object-contain" alt="Bukti pembayaran">
                    </a>
                @else
                    <p class="text-sm muted">Belum ada bukti pembayaran yang diunggah.</p>
                @endif
            </div>
        </div>

        <div class="space-y-5">
            <div class="card p-5 text-sm">
                <div class="mb-2 font-bold green">Pesanan</div>
                <div>Status pesanan: <span class="font-semibold">{{ ucfirst($order->status) }}</span></div>
                <div>Pelanggan: <span class="font-semibold">{{ $order->user->name ?? '-' }}</span></div>
                <a href="{{ route('admin.orders.show', $order) }}" class="mt-3 inline-block text-xs font-semibold green hover:underline">
                    Lihat pesanan →
                </a>
            </div>

            <div class="card p-5">
                <div class="mb-3 font-bold green">Ubah Status</div>

                @if (empty($allowed))
                    <p class="text-sm muted">Status ini sudah final dan tidak bisa diubah.</p>
                @else
                    <div class="space-y-2">
                        @foreach ($allowed as $next)
                            <form method="POST" action="{{ route('admin.payments.status', $payment) }}"
                                  onsubmit="return confirm('{{ $actionLabel[$next] }}?')">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $next }}">
                                <button class="{{ $next === 'paid' ? 'btn w-full text-sm' : 'w-full rounded-full border border-red-100 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50' }}">
                                    {{ $actionLabel[$next] }}
                                </button>
                            </form>
                        @endforeach
                    </div>

                    @if ($payment->status === 'pending')
                        <p class="mt-3 text-xs muted">
                            Menandai lunas juga mengonfirmasi pesanan yang masih menunggu. Menandai gagal tidak membatalkan pesanan otomatis.
                        </p>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection