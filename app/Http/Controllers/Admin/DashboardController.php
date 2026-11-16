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
        return view('admin.dashboard', [
            'totalUsers'    => User::where('role', 'customer')->count(),
            'totalSellers'  => User::where('role', 'seller')->count(),
            'totalProducts' => Product::count(),
            'totalOrders'   => Order::count(),
        ]);
    }
}
