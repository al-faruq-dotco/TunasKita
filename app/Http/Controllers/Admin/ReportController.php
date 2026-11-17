<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /** A payment counts on the day it was paid (falls back to its creation date). */
    private const PAID_AT = 'COALESCE(payments.paid_at, payments.created_at)';

    public function index(Request $request)
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to'   => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        [$from, $to] = $this->range($request);

        if ($from->diffInMonths($to) > 36) {
            return back()->withErrors(['from' => 'Rentang laporan maksimal 36 bulan.'])->withInput();
        }

        $revenue   = (float) $this->paid($from, $to)->sum('payments.amount');
        $paidCount = $this->paid($from, $to)->count();

        $refunded = (float) DB::table('payments')
            ->where('status', 'refunded')
            ->whereRaw('COALESCE(paid_at, created_at) between ? and ?', [$from, $to])
            ->sum('amount');

        $byMonth = $this->paid($from, $to)
            ->selectRaw('DATE_FORMAT(' . self::PAID_AT . ", '%Y-%m') as ym, SUM(payments.amount) as total, COUNT(*) as cnt")
            ->groupBy('ym')
            ->get()
            ->keyBy('ym');

        $months = collect();

        for ($m = $from->copy()->startOfMonth(); $m->lte($to); $m->addMonth()) {
            $row = $byMonth->get($m->format('Y-m'));

            $months->push([
                'label' => $m->copy()->locale('id')->isoFormat('MMM YYYY'),
                'total' => (float) ($row->total ?? 0),
                'count' => (int) ($row->cnt ?? 0),
            ]);
        }

        $topProducts = DB::table('order_items')
            ->join('payments', 'payments.order_id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('payments.status', 'paid')
            ->whereRaw(self::PAID_AT . ' between ? and ?', [$from, $to])
            ->selectRaw('products.id, products.name, SUM(order_items.quantity) as qty, SUM(order_items.subtotal) as revenue')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('qty')
            ->limit(10)
            ->get();

        $byMethod = $this->paid($from, $to)
            ->selectRaw('payments.payment_method as method, COUNT(*) as cnt, SUM(payments.amount) as total')
            ->groupBy('payments.payment_method')
            ->orderByDesc('total')
            ->get();

        $orderStatus = DB::table('orders')
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.reports.index', [
            'from'        => $from,
            'to'          => $to,
            'revenue'     => $revenue,
            'paidCount'   => $paidCount,
            'average'     => $paidCount ? $revenue / $paidCount : 0,
            'refunded'    => $refunded,
            'months'      => $months,
            'topProducts' => $topProducts,
            'byMethod'    => $byMethod,
            'orderStatus' => $orderStatus,
        ]);
    }

    public function export(Request $request)
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to'   => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        [$from, $to] = $this->range($request);
        $filename = 'laporan-pesanan-' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($from, $to) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // so Excel reads UTF-8 correctly

            $this->csvRow($out, ['Kode', 'Tanggal', 'Pelanggan', 'Total', 'Status Pesanan', 'Metode', 'Status Pembayaran']);

            Order::with(['user:id,name', 'payment'])
                ->whereBetween('created_at', [$from, $to])
                ->orderBy('id')
                ->chunk(200, function ($orders) use ($out) {
                    foreach ($orders as $order) {
                        $this->csvRow($out, [
                            $order->order_code,
                            $order->created_at?->format('Y-m-d H:i'),
                            $order->user->name ?? '',
                            $order->total_price,
                            $order->status,
                            $order->payment->payment_method ?? '',
                            $order->payment->status ?? '',
                        ]);
                    }
                });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function range(Request $request): array
    {
        $from = Carbon::parse($request->input('from') ?: now()->subMonths(5)->startOfMonth())->startOfDay();
        $to   = Carbon::parse($request->input('to') ?: now())->endOfDay();

        return [$from, $to];
    }

    private function paid(Carbon $from, Carbon $to)
    {
        return DB::table('payments')
            ->where('payments.status', 'paid')
            ->whereRaw(self::PAID_AT . ' between ? and ?', [$from, $to]);
    }

    private function csvRow($handle, array $row): void
    {
        // Names are typed by customers. A leading = + - @ would run as a formula in Excel.
        $row = array_map(
            fn ($v) => is_string($v) && preg_match('/^[=+\-@\t\r]/', $v) ? "'" . $v : $v,
            $row
        );

        fputcsv($handle, $row, ',', '"', '\\');
    }
}