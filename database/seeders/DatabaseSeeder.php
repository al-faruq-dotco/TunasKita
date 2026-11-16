<?php
namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat akun Seller/Admin sebagai pemilik produk (wajib untuk kolom seller_id)
        $seller = User::firstOrCreate(
            ['email' => 'admin@tunaskita.com'],
            [
                'name'     => 'Admin Kebun TunasKita',
                'phone'    => '081234567890',
                'address'  => 'Kebun Utama TunasKita, Pekanbaru',
                'password' => bcrypt('12345678'),
                'role'     => 'admin',
            ]
        );

        // 2. Buat Kategori Bibit Anggur
        $catImpor = Category::firstOrCreate(
            ['name' => 'Anggur Impor'],
            ['description' => 'Varietas bibit anggur unggulan impor dengan rasa manis dan buah besar.']
        );

        $catLokal = Category::firstOrCreate(
            ['name' => 'Anggur Lokal Unggul'],
            ['description' => 'Bibit anggur adaptif iklim tropis yang mudah berbuah di pekarangan.']
        );

        $catSeedless = Category::firstOrCreate(
            ['name' => 'Anggur Tanpa Biji (Seedless)'],
            ['description' => 'Varian anggur meja tanpa biji, renyah, dan praktis dikonsumsi.']
        );

        // 3. Buat Daftar Produk Bibit Anggur
        $products = [
            [
                'category_id'  => $catImpor->id,
                'name'         => 'Bibit Anggur Jupiter',
                'description'  => 'Anggur impor asal Amerika Serikat dengan aroma muscat yang khas, tekstur renyah, dan sangat genjah berbuah.',
                'price'        => 85000,
                'stock'        => 25,
                'plant_age'    => '3 Bulan (Grafting)',
                'plant_size'   => '40 - 50 cm',
                'availability' => 'available',
                'status'       => 'active',
            ],
            [
                'category_id'  => $catSeedless->id,
                'name'         => 'Bibit Anggur Shine Muscat',
                'description'  => 'Varietas premium asal Jepang berwarna hijau kekuningan, kulit tipis bisa dimakan, dan tanpa biji.',
                'price'        => 120000,
                'stock'        => 15,
                'plant_age'    => '4 Bulan (Grafting)',
                'plant_size'   => '50 - 60 cm',
                'availability' => 'available',
                'status'       => 'active',
            ],
            [
                'category_id'  => $catImpor->id,
                'name'         => 'Bibit Anggur Ninel',
                'description'  => 'Anggur merah keunguan asal Ukraina yang terkenal tahan hujan dan mudah dibuahkan di dalam pot (tabulampot).',
                'price'        => 75000,
                'stock'        => 30,
                'plant_age'    => '3 Bulan (Grafting)',
                'plant_size'   => '35 - 45 cm',
                'availability' => 'available',
                'status'       => 'active',
            ],
            [
                'category_id'  => $catImpor->id,
                'name'         => 'Bibit Anggur Transfiguration',
                'description'  => 'Memiliki dompolan buah besar memanjang berwarna kuning kemerahan dengan tingkat kemanisan tinggi.',
                'price'        => 90000,
                'stock'        => 18,
                'plant_age'    => '3.5 Bulan',
                'plant_size'   => '45 - 55 cm',
                'availability' => 'available',
                'status'       => 'active',
            ],
            [
                'category_id'  => $catSeedless->id,
                'name'         => 'Bibit Anggur Autumn Royal',
                'description'  => 'Anggur hitam tanpa biji dengan ukuran butir jumbo dan daging buah padat berair.',
                'price'        => 110000,
                'stock'        => 10,
                'plant_age'    => '4 Bulan',
                'plant_size'   => '50 cm',
                'availability' => 'available',
                'status'       => 'active',
            ],
            [
                'category_id'  => $catLokal->id,
                'name'         => 'Bibit Anggur Red Master (Rootstock)',
                'description'  => 'Sangat kokoh dan tahan hama tanah, cocok untuk pemula maupun sebagai batang bawah sambung pucuk.',
                'price'        => 45000,
                'stock'        => 40,
                'plant_age'    => '2.5 Bulan',
                'plant_size'   => '30 - 40 cm',
                'availability' => 'available',
                'status'       => 'active',
            ],
        ];

        foreach ($products as $item) {
            Product::firstOrCreate(
                ['slug' => Str::slug($item['name'])],
                array_merge($item, [
                    'seller_id' => $seller->id,
                    'slug'      => Str::slug($item['name']),
                ])
            );
        }
    }
}
