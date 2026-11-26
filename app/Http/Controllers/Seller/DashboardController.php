<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $sellerId = auth()->id();
        $productIds = Product::where('seller_id', $sellerId)->select('id');
        $orderQuery = Order::whereHas('items', fn ($q) => $q->whereIn('product_id', $productIds));

        $stats = [
            ['label' => 'Produk Saya', 'value' => Product::where('seller_id', $sellerId)->count(), 'icon' => '📦', 'hint' => 'Produk terdaftar'],
            ['label' => 'Pesanan Masuk', 'value' => (clone $orderQuery)->count(), 'icon' => '🧾', 'hint' => 'Pesanan yang memuat produk Anda'],
            ['label' => 'Perlu Diproses', 'value' => (clone $orderQuery)->whereIn('status', ['pending', 'confirmed', 'processing'])->count(), 'icon' => '⏳', 'hint' => 'Perlu tindak lanjut'],
            ['label' => 'Unit Terjual', 'value' => DB::table('order_items')->join('products', 'products.id', '=', 'order_items.product_id')->join('orders', 'orders.id', '=', 'order_items.order_id')->where('products.seller_id', $sellerId)->whereIn('orders.status', ['shipped', 'completed'])->sum('order_items.quantity'), 'icon' => '🌱', 'hint' => 'Unit dalam pesanan terkirim/selesai'],
        ];

        $recentOrders = Order::with(['user:id,name', 'payment:id,order_id,status'])
            ->whereHas('items.product', fn ($q) => $q->where('seller_id', $sellerId))
            ->with(['items' => fn ($q) => $q->whereHas('product', fn ($p) => $p->where('seller_id', $sellerId))->with('product:id,name')])
            ->latest()->take(6)->get();

        $topProducts = DB::table('products')
            ->leftJoin('order_items', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('orders', function ($join) {
                $join->on('orders.id', '=', 'order_items.order_id')->whereIn('orders.status', ['shipped', 'completed']);
            })
            ->where('products.seller_id', $sellerId)
            ->groupBy('products.id', 'products.name', 'products.stock')
            ->orderByDesc(DB::raw('COALESCE(SUM(CASE WHEN orders.id IS NOT NULL THEN order_items.quantity ELSE 0 END), 0)'))
            ->select('products.id', 'products.name', 'products.stock')
            ->selectRaw('COALESCE(SUM(CASE WHEN orders.id IS NOT NULL THEN order_items.quantity ELSE 0 END), 0) as sold')
            ->take(5)->get();

        return view('seller.dashboard', compact('stats', 'recentOrders', 'topProducts'));
    }

    public function profile(): View
    {
        return view('seller.profile');
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore(auth()->id())],
            'phone' => ['nullable', 'string', 'max:25'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        auth()->user()->update($data);

        return redirect()->route('seller.profile')->with('success', 'Profil toko berhasil diperbarui.');
    }
}
