# TunasKita – Panduan Versi Redesign

## Perubahan utama

- Halaman login kini tersedia secara terpisah: `/login/admin`, `/login/seller`, dan `/login/customer`.
- Autentikasi mengarahkan setiap role ke dashboard masing-masing.
- Admin tetap menggunakan controller, layout, dan halaman admin yang sudah ada; file desain Admin tidak diubah.
- Customer memakai layout storefront yang diselaraskan dengan palet hijau, card, typography, dan komponen Admin.
- Seller memiliki dashboard, manajemen produk miliknya, daftar/detail pesanan, pembaruan status pengiriman, dan halaman profil toko.
- Akses Seller dibatasi middleware `seller`; produk dan pesanan ditampilkan berdasarkan produk milik seller yang sedang login.
- Database dan migration tidak diubah.

## Akun uji

Akun Seller dari `SellerSeeder`:

- Email: `seller@tunaskita.test`
- Password: `password`
- Login: `http://127.0.0.1:8000/login/seller`

Akun Admin dari `AdminSeeder` (jika seeder Admin sebelumnya dijalankan):

- Email: `admin@tunaskita.test`
- Password: `password`
- Login: `http://127.0.0.1:8000/login/admin`

Customer masuk melalui `/login/customer` atau membuat akun melalui `/register`.

## Menjalankan di Laragon (Windows)

1. Ekstrak folder `TunasKita` ke `C:\laragon\www\TunasKita`.
2. Buka folder tersebut di VS Code.
3. Jika file `.env` belum ada, jalankan di Terminal VS Code:

   ```powershell
   Copy-Item .env.example .env
   ```

4. Periksa konfigurasi database di `.env`:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=tunaskita_dev
   DB_USERNAME=root
   DB_PASSWORD=
   ```

   Gunakan nama database lokal kelompok jika berbeda. Jangan membuat database baru jika database `tunaskita_dev` yang lama sudah berisi data.

5. Pasang dependency jika folder `vendor` tidak ada:

   ```powershell
   composer install
   ```

6. Buat application key hanya jika `APP_KEY` masih kosong:

   ```powershell
   php artisan key:generate
   ```

7. Siapkan folder storage publik dan bersihkan cache:

   ```powershell
   php artisan storage:link
   php artisan optimize:clear
   ```

8. Jalankan aplikasi:

   ```powershell
   php artisan serve
   ```

9. Buka `http://127.0.0.1:8000`.

## Catatan keamanan dan data

- File `.env` lokal tidak disertakan dalam ZIP. Buat dari `.env.example` dan isi sesuai konfigurasi komputer sendiri.
- Jangan menjalankan `php artisan migrate:fresh` karena perintah itu menghapus seluruh tabel dan data.
- Jangan menjalankan seeder jika tidak diperlukan. Seeder Seller yang digunakan untuk akun uji dapat dijalankan dengan `php artisan db:seed --class=SellerSeeder`; seeder ini memperbarui akun dengan email yang sama.
- Jika `php artisan db:show` menampilkan pesan bahwa ekstensi PHP `intl` diperlukan, aktifkan `extension=intl` pada `php.ini` PHP Laragon, lalu buka terminal baru. Ini diperlukan untuk format tampilan perintah tersebut.
- Pastikan ekstensi `fileinfo`, `mbstring`, `openssl`, `pdo_mysql`, dan `intl` aktif pada PHP Laragon.

## Batasan alur pesanan Seller

Status pesanan merupakan status tingkat pesanan. Jika satu pesanan berisi produk dari lebih dari satu seller, perubahan status dari panel Seller diblokir agar seorang seller tidak mengubah pesanan seller lain. Administrator tetap dapat mengelola status pesanan tersebut.
