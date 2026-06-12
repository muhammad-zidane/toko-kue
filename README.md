# Jagoan Kue

E-commerce toko kue berbasis Laravel. Final project mata kuliah Pemrograman Web,
Informatika UNP 2025.

**Nama:** Muhammad Zidane | **NIM:** 25343071 | **Kelas:** Informatika A

---

## Fitur Utama

### Customer
- Registrasi & Login (Laravel Breeze)
- Katalog produk dengan filter kategori, harga, dan pencarian
- Detail produk + kustomisasi (rasa, ukuran, tulisan, dll)
- Keranjang belanja (AJAX, tanpa redirect)
- Checkout dengan pilihan pengiriman / ambil di toko
- Pembayaran DP 50% atau lunas
- Upload bukti pembayaran
- Lacak status pesanan
- Riwayat & detail pesanan
- Ulasan produk
- Voucher diskon

### Admin
- Dashboard ringkasan (pendapatan, pesanan, produk)
- CRUD produk + upload gambar
- CRUD kategori
- Manajemen pesanan & update status
- Manajemen user/pelanggan
- CRUD voucher diskon
- Kustomisasi per kategori produk
- Analitik & laporan penjualan (filter tanggal, grafik Chart.js, export Excel)
- Manajemen zona pengiriman & ongkir
- Manajemen banner homepage
- Kalender produksi
- Notifikasi pesanan baru

---

## Tech Stack

| Komponen    | Teknologi                        |
|-------------|----------------------------------|
| Backend     | PHP 8.2+, Laravel 12             |
| Frontend    | Blade Template, vanilla JS       |
| Auth        | Laravel Breeze                   |
| Database    | MySQL                            |
| ORM         | Eloquent                         |
| Storage     | Laravel Storage (lokal)          |
| Export      | Maatwebsite/Excel (PhpSpreadsheet)|

---

## Cara Menjalankan

### Prasyarat
- PHP >= 8.2 (dengan extension: `gd`, `zip`, `fileinfo`, `pdo_mysql`)
- Composer
- Node.js & NPM
- MySQL

### Langkah Instalasi

```bash
git clone <url-repository> jagoan-kue
cd jagoan-kue

composer install
npm install

cp .env.example .env
php artisan key:generate

# Isi konfigurasi DB di .env:
# DB_DATABASE=jagoan_kue
# DB_USERNAME=root
# DB_PASSWORD=

php artisan migrate:fresh --seed
php artisan storage:link

npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`

---

## Akun Demo

| Role     | Email                        | Password |
|----------|------------------------------|----------|
| Admin    | admin@jagoankue.test         | password |
| Customer | customer@jagoan-kue.test     | password |
| Customer | budi@mail.test               | password |
| Customer | siti@mail.test               | password |
| Customer | dian@mail.test               | password |

---

## Struktur Folder Penting

```
app/
├── Exports/                        # Export Excel (LaporanPenjualanExport)
├── Http/
│   ├── Controllers/
│   │   ├── AdminController.php          # Dashboard & semua fitur admin
│   │   ├── Admin/
│   │   │   └── CustomizationController.php
│   │   ├── AccountController.php        # Ganti password
│   │   ├── AddressController.php        # Alamat tersimpan
│   │   ├── CartController.php           # Keranjang (session-based, AJAX)
│   │   ├── HomeController.php           # Halaman utama
│   │   ├── OrderController.php          # Pemesanan, pembayaran, invoice
│   │   ├── ProductController.php        # CRUD produk (admin)
│   │   ├── ProductReviewController.php  # Ulasan produk
│   │   ├── ProfileController.php        # Profil pengguna
│   │   └── VoucherController.php        # Validasi voucher
│   └── Middleware/
│       └── EnsureUserIsAdmin.php        # Guard admin routes (alias: admin)
└── Models/                              # Eloquent models

database/seeders/
├── AdminSeeder.php                 # Akun admin
├── CategorySeeder.php              # Kategori produk
├── ProductSeeder.php               # Produk dasar
├── ShippingZoneSeeder.php          # Zona ongkir
├── TestimonialSeeder.php           # Testimoni homepage
├── BannerSeeder.php                # Banner homepage
├── CustomizationOptionSeeder.php   # Opsi kustomisasi
├── VoucherSeeder.php               # Voucher diskon
├── DemoSeeder.php                  # 4 customer + 6 produk extra + 10 pesanan
└── ProductReviewSeeder.php         # Ulasan demo

resources/views/
├── admin/                          # Semua tampilan admin
├── orders/                         # Checkout, payment, success, detail, invoice
├── products/                       # Katalog & detail produk
└── partials/navbar.blade.php       # Navbar dengan cart badge

routes/web.php                      # Semua route (public + auth + admin)
```

---

## ERD

Jalankan `php artisan db:show` untuk melihat skema lengkap, atau lihat `docs/database.md`.

Relasi utama:
- `users` → hasMany `orders`, `addresses`, `product_reviews`
- `categories` → hasMany `products`, `customization_options`
- `products` → belongsTo `category`, hasMany `order_items`, `product_reviews`
- `orders` → belongsTo `user`, hasMany `order_items`, `payments`, `product_reviews`
- `order_items` → belongsTo `order`, `product`, hasMany `order_item_customizations`
- `product_reviews` → belongsTo `user`, `product`, `order`, hasMany `product_review_images`

---

## Screenshots

> Screenshot belum tersedia.

---

## Quick Reference

```bash
php artisan serve                    # Jalankan dev server
php artisan migrate:fresh --seed     # Reset DB + isi data demo
php artisan tinker                   # REPL Laravel
php artisan route:list --except-vendor
php artisan optimize:clear           # Clear semua cache
npm run dev                          # Asset dev mode
npm run build                        # Asset production
```
