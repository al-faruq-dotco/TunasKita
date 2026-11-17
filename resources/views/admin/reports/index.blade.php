@extends('admin.layouts.app')

@section('title', 'Laporan')
@section('heading', 'Laporan')

@section('content')
    @php
        $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
        $statusLabel = [
            'pending'    => 'Menunggu',
            'confirmed'  => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'shipped'    => 'Dikirim',
            'completed'  => 'Selesai',
            'cancelled'  => 'Dibatalkan',
        ];
        $field = 'rounded-xl border border-green-100 px-3 py-2 outline-none focus:border-[#315d40]';
        $cards = [
            ['Pendapatan',          $rp($revenue),                '💰'],
            ['Pesanan Lunas',       number_format($paidCount),    '🧾'],
            ['Rata-rata per Pesanan', $rp($average),              '📈'],
            ['Dikembalikan',        $rp($refunded),               '↩️'],
        ];
    @endphp

    <form method="GET" class="mb-5 flex flex-wrap items-end gap-3 text-sm">
        <div>
            <label class="mb-1 block text-xs muted">Dari</label>
            <input type="date" name="from" value="{{ $from->toDateString() }}" class="{{ $field }}">
        </div>
        <div>
            <label class="mb-1 block text-xs muted">Sampai</label>
            <input type="date" name="to" value="{{ $to->toDateString() }}" class="{{ $field }}">
        </div>
        <button class="btn text-sm">Terapkan</button>
        <a href="{{ route('admin.reports.export', ['from' => $from->toDateString(), 'to' => $to->toDateString()]) }}"
           class="rounded-full border border-green-200 px-4 py-2.5 text-sm font-semibold green hover:bg-green-50">
            ⬇ Ekspor CSV
        </a>
    </form>

    <p class="mb-4 text-xs muted">Pendapatan dihitung dari pembayaran berstatus Lunas pada rentang tanggal di atas.</p>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($cards as [$label, $value, $icon])
            <div class="card p-5">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 text-2xl">{{ $icon }}</div>
                    <div>
                        <div class="text-xl font-extrabold green">{{ $value }}</div>
                        <div class="text-sm text-slate-600">{{ $label }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="card p-5 lg:col-span-2">
            <div class="font-bold green">Pendapatan per Bulan</div>
            <div class="relative mt-4 h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="card p-5">
            <div class="mb-3 font-bold green">Status Pesanan</div>
            @forelse ($orderStatus as $status => $total)
                <div class="flex items-center justify-between border-b border-green-50 py-2.5 text-sm last:border-0">
                    <span>{{ $statusLabel[$status] ?? $status }}</span>
                    <span class="font-semibold">{{ $total }}</span>
                </div>
            @empty
                <p class="py-6 text-center text-sm muted">Belum ada pesanan pada rentang ini.</p>
            @endforelse
        </div>
    </div>

    <div class="card mt-6 overflow-x-auto">
        <div class="px-5 pt-5 font-bold green">Rincian Bulanan</div>
        <table class="mt-3 w-full text-left text-sm">
            <thead class="bg-green-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-5 py-3">Bulan</th>
                    <th class="px-5 py-3">Pesanan lunas</th>
                    <th class="px-5 py-3 text-right">Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($months as $month)
                    <tr class="border-t border-green-50">
                        <td class="px-5 py-3">{{ $month['label'] }}</td>
                        <td class="px-5 py-3">{{ $month['count'] }}</td>
                        <td class="px-5 py-3 text-right font-semibold">{{ $rp($month['total']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="card overflow-x-auto lg:col-span-2">
            <div class="px-5 pt-5 font-bold green">Produk Terlaris</div>
            <table class="mt-3 w-full text-left text-sm">
                <thead class="bg-green-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Produk</th>
                        <th class="px-5 py-3">Terjual</th>
                        <th class="px-5 py-3 text-right">Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($topProducts as $product)
                        <tr class="border-t border-green-50">
                            <td class="px-5 py-3 font-semibold green">{{ $product->name }}</td>
                            <td class="px-5 py-3">{{ $product->qty }}</td>
                            <td class="px-5 py-3 text-right">{{ $rp($product->revenue) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-10 text-center muted">Belum ada penjualan lunas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card p-5">
            <div class="mb-3 font-bold green">Metode Pembayaran</div>
            @forelse ($byMethod as $method)
                <div class="flex items-center justify-between border-b border-green-50 py-2.5 text-sm last:border-0">
                    <div>
                        <div class="font-semibold">{{ $method->method }}</div>
                        <div class="text-xs muted">{{ $method->cnt }} pembayaran</div>
                    </div>
                    <div class="font-semibold green">{{ $rp($method->total) }}</div>
                </div>
            @empty
                <p class="py-6 text-center text-sm muted">Belum ada pembayaran lunas.</p>
            @endforelse
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        const months = @json($months);
        new Chart(document.getElementById('revenueChart'), {
            type: 'bar',
            data: {
                labels: months.map(m => m.label),
                datasets: [{
                    data: months.map(m => m.total),
                    backgroundColor: 'rgba(49, 93, 64, 0.75)',
                    borderRadius: 6,
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