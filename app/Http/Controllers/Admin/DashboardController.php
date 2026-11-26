<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $startOfMonth = now()->startOfMonth();

        $stats = [
            [
                'label' => 'Total Pengguna', 'icon' => '👥',
                'total' => User::where('role', 'customer')->count(),
                'new' => User::where('role', 'customer')->where('created_at', '>=', $startOfMonth)->count(),
            ],
            [
                'label' => 'Total Penjual', 'icon' => '🧑‍🌾',
                'total' => User::where('role', 'seller')->count(),
                'new' => User::where('role', 'seller')->where('created_at', '>=', $startOfMonth)->count(),
            ],
            [
                'label' => 'Total Produk', 'icon' => '📦',
                'total' => Product::count(),
                'new' => Product::where('created_at', '>=', $startOfMonth)->count(),
            ],
            [
                'label' => 'Total Transaksi', 'icon' => '🛒',
                'total' => Order::count(),
                'new' => Order::where('created_at', '>=', $startOfMonth)->count(),
            ],
        ];

        // Revenue for the last 6 months. Same rule as the Laporan page:
        // only payments with status "paid", dated by when they were paid.
        $months = collect(range(5, 0))->map(fn ($i) => now()->startOfMonth()->subMonths($i));

        $revenue = DB::table('payments')
            ->where('status', 'paid')
            ->whereRaw('COALESCE(paid_at, created_at) >= ?', [$months->first()])
            ->selectRaw("DATE_FORMAT(COALESCE(paid_at, created_at), '%Y-%m') as ym, SUM(amount) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $salesChart = $months->map(fn ($m) => [
            'label' => $m->copy()->locale('id')->isoFormat('MMM'),
            'total' => (float) ($revenue[$m->format('Y-m')] ?? 0),
        ])->values();

        $recentOrders = Order::query()
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->select('orders.id', 'orders.order_code', 'orders.total_price', 'orders.status', 'orders.created_at', 'users.name as customer_name')
            ->latest('orders.created_at')
            ->take(5)
            ->get();

        // Best sellers count only items from orders whose payment is paid.
        $bestSellers = Product::query()
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->whereExists(fn ($query) => $query
                ->selectRaw('1')
                ->from('payments')
                ->whereColumn('payments.order_id', 'order_items.order_id')
                ->where('payments.status', 'paid'))
            ->select('products.id', 'products.name', 'products.price')
            ->selectRaw('SUM(order_items.quantity) as sold')
            ->groupBy('products.id', 'products.name', 'products.price')
            ->orderByDesc('sold')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'salesChart', 'recentOrders', 'bestSellers'));
    }
}
