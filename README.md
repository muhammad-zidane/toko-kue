# Jagoan Kue

Aplikasi web e-commerce pemesanan kue online berbasis Laravel. Dibangun sebagai tugas akademik mata kuliah Pemrograman Web dengan metodologi Waterfall.

---

## Fitur Utama

- **Registrasi & Login** — autentikasi pelanggan dan admin menggunakan Laravel Breeze
- **Katalog Produk** — browsing kue berdasarkan kategori
- **Keranjang Belanja** — tambah, hapus, dan checkout produk
- **Pemesanan** — checkout dengan validasi stok otomatis
- **Pembayaran** — Transfer Bank, E-Wallet, QRIS, dan COD
- **Upload Bukti Bayar** — pelanggan upload foto bukti transfer
- **Status Pesanan** — pelacakan status (pending → processing → completed)
- **Dashboard Admin** — statistik, grafik pendapatan, manajemen produk & pesanan
- **Manajemen Kategori** — CRUD kategori produk
- **Manajemen Pelanggan** — daftar dan statistik pelanggan
- **Analitik & Keuangan** — laporan pendapatan dan distribusi pesanan

---

## Teknologi yang Digunakan

| Komponen        | Teknologi                  |
|-----------------|----------------------------|
| Backend         | PHP 8.2+, Laravel 12       |
| Frontend        | Blade Template             |
| Autentikasi     | Laravel Breeze             |
| Database        | MySQL                      |
| ORM             | Eloquent                   |
| Penyimpanan     | Laravel Storage (lokal)    |
| Package Manager | Composer, NPM              |

---

## Instalasi dan Menjalankan Project

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL

### Langkah Instalasi

```bash
# 1. Clone repository
git clone <url-repository> jagoan-kue
cd jagoan-kue

# 2. Install dependensi PHP
composer install

# 3. Salin file environment
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Konfigurasi database di .env
# Ubah bagian ini sesuai konfigurasi MySQL Anda:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=jagoan_kue
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Jalankan migrasi dan seeder
php artisan migrate --seed

# 7. Buat symbolic link untuk storage
php artisan storage:link

# 8. Install dependensi frontend
npm install

# 9. Build asset frontend
npm run build

# 10. Jalankan server
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

---

## Struktur Folder Project

```
jagoan-kue/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php     # Dashboard dan manajemen admin
│   │   │   ├── CartController.php      # Keranjang belanja (session-based)
│   │   │   ├── OrderController.php     # Pemesanan dan pembayaran
│   │   │   ├── ProductController.php   # CRUD produk
│   │   │   └── ProfileController.php   # Profil pengguna
│   │   └── Requests/
│   │       └── ProfileUpdateRequest.php
│   └── Models/
│       ├── Category.php
│       ├── Order.php
│       ├── OrderItem.php
│       ├── Payment.php
│       ├── Product.php
│       ├── Testimonial.php
│       └── User.php
├── database/
│   ├── migrations/                     # Skema tabel database
│   └── seeders/                        # Data awal
├── resources/
│   └── views/
│       ├── admin/                      # Tampilan dashboard admin
│       ├── auth/                       # Tampilan login & register
│       ├── cart/                       # Tampilan keranjang
│       ├── home/                       # Tampilan halaman utama
│       ├── layouts/                    # Layout utama aplikasi
│       ├── orders/                     # Tampilan pesanan
│       ├── products/                   # Tampilan katalog produk
│       └── profile/                    # Tampilan profil pengguna
├── routes/
│   ├── web.php                         # Semua route web
│   └── auth.php                        # Route autentikasi (Breeze)
└── public/
    └── storage/                        # Gambar produk & bukti bayar
```

---

## Informasi Database

**Nama database:** `jagoan_kue`

### Cara Import

Menggunakan migrasi Laravel:
```bash
php artisan migrate --seed
```

Dokumentasi lengkap skema database tersedia di [docs/database.md](docs/database.md).

---

## Kredensial Akun Default

| Role     | Email                   | Password  |
|----------|-------------------------|-----------|
| Admin    | admin@jagoan-kue.com    | password  |
| Customer | customer@jagoan-kue.com | password  |

> Kredensial di atas adalah bawaan seeder. Ubah segera setelah instalasi di lingkungan produksi.

---

## Dokumentasi Tambahan

- [Dokumentasi Database](docs/database.md)
- [Dokumentasi Route](docs/routes.md)
- [Dokumentasi Fitur](docs/features.md)
