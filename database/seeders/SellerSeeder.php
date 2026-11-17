<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SellerSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'seller@tunaskita.test'],
            [
                'name'     => 'Kebun Anggur Riau',
                'phone'    => '081200000000',
                'address'  => 'Pekanbaru, Riau',
                'password' => 'password',
                'role'     => 'seller',
            ]
        );
    }
}