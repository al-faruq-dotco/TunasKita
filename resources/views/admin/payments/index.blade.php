@extends('admin.layouts.app')

@section('title', 'Pembayaran')
@section('heading', 'Pembayaran')

@section('content')
    @php
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
            @foreach ($paymentStatus as $value => [$label])
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="method" class="rounded-xl border border-green-100 px-3 py-2">
            <option value="">Semua metode</option>
            @foreach ($methods as $method)
                <option value="{{ $method }}" @selected(request('method') === $method)>{{ $method }}</option>
            @endforeach
        </select>
        <button class="btn text-sm">Filter</button>
    </form>

    <div class="card overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-green-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-5 py-3">Pesanan</th>
                    <th class="px-5 py-3">Pelanggan</th>
                    <th class="px-5 py-3">Metode</th>
                    <th class="px-5 py-3">Jumlah</th>
                    <th class="px-5 py-3">Bukti</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    @php [$label, $class] = $paymentStatus[$payment->status] ?? [$payment->status, 'bg-slate-100 text-slate-700']; @endphp
                    <tr class="border-t border-green-50">
                        <td class="px-5 py-3 font-semibold green">{{ $payment->order->order_code ?? '-' }}</td>
                        <td class="px-5 py-3">{{ $payment->order->user->name ?? '-' }}</td>
                        <td class="px-5 py-3">{{ $payment->payment_method }}</td>
                        <td class="px-5 py-3">{{ $rp($payment->amount) }}</td>
                        <td class="px-5 py-3">{{ $payment->proof_image ? 'Ada' : '-' }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $class }}">{{ $label }}</span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.payments.show', $payment) }}"
                               class="rounded-lg border border-green-100 px-3 py-1.5 hover:bg-green-50">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center muted">Belum ada pembayaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $payments->links() }}</div>
@endsection