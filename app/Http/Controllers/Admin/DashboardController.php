<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $startOfMonth = now()->startOfMonth();

        $stats = [
            [
                'label' => 'Total Pengguna', 'icon' => '👥',
                'total' => User::where('role', 'customer')->count(),
                'new'   => User::where('role', 'customer')->where('created_at', '>=', $startOfMonth)->count(),
            ],
            [
                'label' => 'Total Penjual', 'icon' => '🧑‍🌾',
                'total' => User::where('role', 'seller')->count(),
                'new'   => User::where('role', 'seller')->where('created_at', '>=', $startOfMonth)->count(),
            ],
            [
                'label' => 'Total Produk', 'icon' => '📦',
                'total' => Product::count(),
                'new'   => Product::where('created_at', '>=', $startOfMonth)->count(),
            ],
            [
                'label' => 'Total Transaksi', 'icon' => '🛒',
                'total' => Order::count(),
                'new'   => Order::where('created_at', '>=', $startOfMonth)->count(),
            ],
        ];

        // Sales for the last 6 months (cancelled orders excluded)
        $months = collect(range(5, 0))->map(fn ($i) => now()->startOfMonth()->subMonths($i));

        $revenue = Order::where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $months->first())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, SUM(total_price) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $salesChart = $months->map(fn ($m) => [
            'label' => $m->locale('id')->isoFormat('MMM'),
            'total' => (float) ($revenue[$m->format('Y-m')] ?? 0),
        ])->values();

        $recentOrders = Order::query()
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->select('orders.id', 'orders.order_code', 'orders.total_price', 'orders.status', 'orders.created_at', 'users.name as customer_name')
            ->latest('orders.created_at')
            ->take(5)
            ->get();

        $bestSellers = Product::query()
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '!=', 'cancelled')
            ->select('products.id', 'products.name', 'products.price')
            ->selectRaw('SUM(order_items.quantity) as sold')
            ->groupBy('products.id', 'products.name', 'products.price')
            ->orderByDesc('sold')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'salesChart', 'recentOrders', 'bestSellers'));
    }
}