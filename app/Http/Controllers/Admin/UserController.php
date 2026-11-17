<?php
namespace App\Http\Controllers\Admin;

class UserController extends AccountController
{
    protected function role(): string { return 'customer'; }

    protected function label(): string { return 'Pengguna'; }

    protected function routeBase(): string { return 'admin.users'; }

    protected function related(): array { return ['Pesanan', 'orders', 'user_id']; }
}