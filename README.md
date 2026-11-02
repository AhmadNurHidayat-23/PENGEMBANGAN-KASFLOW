# PENGEMBANGAN KASFLOW — Pengelolaan Kas Kecil Berbasis Web

## Deskripsi Aplikasi

KasFlow adalah aplikasi pengelolaan kas kecil untuk mencatat pemasukan, pengeluaran, dan memantau saldo melalui antarmuka web berbahasa Indonesia. Project portofolio Laravel ini mengutamakan pencatatan sederhana, data konsisten, dan tampilan minimalis pada desktop maupun ponsel.

Pencatatan digital membantu pengguna menelusuri penggunaan dana tanpa bergantung pada catatan terpisah. Setiap perubahan transaksi langsung diperhitungkan oleh server sehingga ringkasan keuangan selalu mengikuti riwayat yang tersimpan. Saldo negatif tetap ditampilkan ketika pengeluaran melebihi pemasukan.

## Fitur Utama

- Dashboard berisi total pemasukan, total pengeluaran, saldo kas, dan lima transaksi terbaru.
- Pencatatan pemasukan dan pengeluaran dengan tanggal, kategori, keterangan, serta nominal Rupiah.
- Perhitungan saldo otomatis dari transaksi, tanpa menyimpan saldo statis.
- Riwayat berurutan dari tanggal terbaru, pencarian keterangan atau kategori, filter jenis dan bulan, serta pagination.
- Pengelolaan transaksi melalui tambah, edit, dan hapus dengan konfirmasi.
- Validasi server berbahasa Indonesia, notifikasi hasil penyimpanan, dan tampilan saat data kosong.

## Teknologi yang Digunakan

Laravel 13 menyediakan routing, controller, validasi, migration, dan Eloquent ORM. Blade menjadi antarmuka utama dengan rendering di server. SQLite menyimpan transaksi, sedangkan PHP menangani logika aplikasi. HTML5, CSS3, dan JavaScript minimal membentuk tampilan responsif; Vite membangun aset. Font Inter disertakan lokal dengan lisensi SIL Open Font License. Nominal menggunakan integer Rupiah, maksimal Rp 999.999.999.999 per transaksi, untuk menghindari pembulatan pecahan.

## Alur Penggunaan

1. Buka dashboard untuk melihat ringkasan seluruh periode.
2. Klik **Tambah Transaksi** dan pilih pemasukan atau pengeluaran.
3. Isi tanggal, kategori, nominal bulat, dan keterangan.
4. Simpan transaksi, kemudian periksa perubahan saldo pada dashboard.
5. Gunakan menu **Transaksi** untuk mencari, menyaring, mengedit, atau menghapus catatan.

## Cara Menjalankan

Siapkan PHP 8.3 atau lebih baru beserta ekstensi SQLite, Composer, serta Node.js yang kompatibel dengan Vite 8 (20.19+ atau 22.12+). Pada MAMP, gunakan PHP 8.3 ke atas dan arahkan document root ke folder `public`; alternatif paling sederhana adalah server Artisan.

```bash
git clone https://github.com/AhmadNurHidayat-23/PENGEMBANGAN-KASFLOW.git
cd PENGEMBANGAN-KASFLOW
composer install
npm ci
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`. Konfigurasi contoh sudah menggunakan SQLite, sesi berbasis file, zona waktu Asia/Jakarta, dan bahasa Indonesia. Database dimulai kosong. Untuk memeriksa aplikasi, jalankan `php artisan test` dan `npm run build`. Pengujian menggunakan SQLite dalam memori sehingga tidak mengubah catatan lokal.

## Batasan Aplikasi

KasFlow merupakan demo lokal tanpa authentication, registrasi, maupun dukungan multi-user. Aplikasi tidak terintegrasi dengan bank dan tidak menyediakan payment gateway. Jangan memublikasikan halaman pengelolaan tanpa authentication dan perlindungan akses yang sesuai. Repository hanya menyertakan source code; `.env`, kredensial, dependency, hasil build, dan database lokal tidak disertakan. Cadangkan database SQLite secara terpisah apabila mulai menyimpan catatan yang ingin dipertahankan.
