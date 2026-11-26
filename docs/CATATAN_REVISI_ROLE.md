# Catatan Revisi Role Customer dan Seller

## Acuan yang tersedia di source ZIP
Source ZIP yang diterima berisi `docs/PANDUAN_TUNASKITA_FINAL.md` dan `docs/database/TunasKita_Structure.sql`.
Dokumen Ideate/HMW dan gambar desain referensi terpisah tidak terdapat di dalam ZIP ini. Karena itu, revisi ini mengikuti kebutuhan fungsional yang tertulis di panduan project dan struktur database yang tersedia; bukan mengklaim cocok pixel-perfect dengan dokumen desain yang tidak ikut terunggah.

## Revisi pada versi ini
- Dashboard Seller diperbarui dengan kartu Produk Aktif, Pesanan Baru, Sedang Diproses, dan Total Penjualan.
- Ditambahkan grafik penjualan 7 hari terakhir yang menghitung subtotal produk milik Seller pada pesanan dengan pembayaran lunas.
- Bagian Produk Terlaris dan Pesanan Terbaru tetap dibatasi pada data produk/pesanan milik Seller yang sedang login.
- Halaman detail produk Customer diperjelas, termasuk informasi usia/ukuran tanaman, stok, deskripsi, dan kondisi tombol pembelian saat stok habis.
- Katalog Customer tidak menampilkan produk nonaktif, stok nol, atau availability `out_of_stock`.
- Route, autentikasi, middleware role, controller Admin, layout Admin, dan migration database tidak diubah dalam revisi tampilan ini.

## Menjalankan project
Ikuti `PANDUAN_TUNASKITA_FINAL.md`. Karena folder `vendor` tidak disertakan, jalankan `composer install` dari folder project sebelum menjalankan Artisan.
Jangan menjalankan `migrate:fresh`.
