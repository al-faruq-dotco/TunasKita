<?php
namespace App\Http\Controllers\Admin;

class SellerController extends AccountController
{
    protected function role(): string { return 'seller'; }

    protected function label(): string { return 'Penjual'; }

    protected function routeBase(): string { return 'admin.sellers'; }

    protected function related(): array { return ['Produk', 'products', 'seller_id']; }
}