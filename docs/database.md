# Dokumentasi Database — Jagoan Kue

## Daftar Tabel

| Tabel              | Fungsi                                              |
|--------------------|-----------------------------------------------------|
| `users`            | Data akun pengguna (admin dan customer)             |
| `categories`       | Kategori produk kue                                 |
| `products`         | Data produk kue yang dijual                         |
| `orders`           | Data pesanan yang dibuat customer                   |
| `order_items`      | Detail item dalam setiap pesanan                    |
| `payments`         | Informasi pembayaran tiap pesanan                   |
| `testimonials`     | Ulasan/testimoni pelanggan di halaman utama         |
| `sessions`         | Sesi login pengguna (dikelola Laravel)              |
| `password_reset_tokens` | Token reset password sementara                 |

---

## Penjelasan Kolom Tiap Tabel

### `users`

| Kolom              | Tipe         | Keterangan                              |
|--------------------|--------------|-----------------------------------------|
| `id`               | bigint PK    | Primary key auto increment              |
| `name`             | varchar(255) | Nama lengkap pengguna                   |
| `email`            | varchar(255) | Email unik, digunakan untuk login       |
| `password`         | varchar(255) | Password ter-hash (bcrypt)              |
| `role`             | varchar      | Peran: `admin` atau `customer`          |
| `email_verified_at`| timestamp    | Waktu verifikasi email (nullable)       |
| `remember_token`   | varchar(100) | Token "ingat saya" (nullable)           |
| `created_at`       | timestamp    | Waktu registrasi                        |
| `updated_at`       | timestamp    | Waktu update terakhir                   |

### `categories`

| Kolom         | Tipe         | Keterangan                          |
|---------------|--------------|-------------------------------------|
| `id`          | bigint PK    | Primary key auto increment          |
| `name`        | varchar(255) | Nama kategori (mis. "Kue Ulang Tahun") |
| `slug`        | varchar(255) | Versi URL-friendly dari nama        |
| `description` | text         | Deskripsi kategori (nullable)       |
| `created_at`  | timestamp    | —                                   |
| `updated_at`  | timestamp    | —                                   |

### `products`

| Kolom          | Tipe         | Keterangan                              |
|----------------|--------------|------------------------------------------|
| `id`           | bigint PK    | Primary key auto increment               |
| `category_id`  | bigint FK    | Referensi ke `categories.id`             |
| `name`         | varchar(255) | Nama produk                              |
| `slug`         | varchar(255) | Digunakan sebagai route key produk       |
| `description`  | text         | Deskripsi produk (nullable)              |
| `price`        | decimal      | Harga satuan produk                      |
| `stock`        | integer      | Jumlah stok tersedia                     |
| `image`        | varchar      | Path gambar produk di storage (nullable) |
| `is_available` | boolean      | Status ketersediaan produk               |
| `created_at`   | timestamp    | —                                        |
| `updated_at`   | timestamp    | —                                        |

### `orders`

| Kolom             | Tipe         | Keterangan                                                  |
|-------------------|--------------|--------------------------------------------------------------|
| `id`              | bigint PK    | Primary key auto increment                                   |
| `user_id`         | bigint FK    | Referensi ke `users.id`                                      |
| `order_code`      | varchar      | Kode unik pesanan, format `ORD-XXXXXXXX`                     |
| `status`          | enum         | `pending`, `confirmed`, `processing`, `completed`, `cancelled` |
| `shipping_address`| text         | Alamat pengiriman pesanan                                    |
| `total_price`     | decimal      | Total harga seluruh item pesanan                             |
| `notes`           | text         | Catatan tambahan dari customer (nullable)                    |
| `created_at`      | timestamp    | —                                                            |
| `updated_at`      | timestamp    | —                                                            |

### `order_items`

| Kolom        | Tipe      | Keterangan                              |
|--------------|-----------|-----------------------------------------|
| `id`         | bigint PK | Primary key auto increment              |
| `order_id`   | bigint FK | Referensi ke `orders.id`                |
| `product_id` | bigint FK | Referensi ke `products.id`              |
| `quantity`   | integer   | Jumlah produk yang dipesan              |
| `price`      | decimal   | Harga satuan saat transaksi (snapshot)  |
| `created_at` | timestamp | —                                       |
| `updated_at` | timestamp | —                                       |

### `payments`

| Kolom            | Tipe      | Keterangan                                            |
|------------------|-----------|-------------------------------------------------------|
| `id`             | bigint PK | Primary key auto increment                            |
| `order_id`       | bigint FK | Referensi ke `orders.id`                              |
| `payment_method` | varchar   | Metode: `transfer`, `ewallet`, `qris`, `cod`          |
| `status`         | enum      | `unpaid`, `paid`, `failed`                            |
| `amount`         | decimal   | Jumlah yang harus dibayar                             |
| `proof_image`    | varchar   | Path gambar bukti pembayaran di storage (nullable)    |
| `paid_at`        | timestamp | Waktu pembayaran dikonfirmasi (nullable)               |
| `created_at`     | timestamp | —                                                     |
| `updated_at`     | timestamp | —                                                     |

### `testimonials`

| Kolom        | Tipe         | Keterangan                         |
|--------------|--------------|------------------------------------|
| `id`         | bigint PK    | Primary key auto increment         |
| `name`       | varchar(255) | Nama pemberi testimoni             |
| `role`       | varchar(255) | Peran/jabatan (mis. "Pelanggan")   |
| `text`       | text         | Isi ulasan                         |
| `created_at` | timestamp    | —                                  |
| `updated_at` | timestamp    | —                                  |

---

## Diagram Relasi Antar Tabel (ERD)

```
users
  └── hasMany ──> orders
                    └── hasMany ──> order_items
                    │                 └── belongsTo ──> products
                    │                                     └── belongsTo ──> categories
                    └── hasOne  ──> payments

testimonials  (berdiri sendiri, tidak berelasi)
```

**Ringkasan relasi:**

| Model        | Relasi           | Model Tujuan  |
|--------------|------------------|---------------|
| `User`       | hasMany           | `Order`       |
| `Order`      | belongsTo         | `User`        |
| `Order`      | hasMany           | `OrderItem`   |
| `Order`      | hasOne            | `Payment`     |
| `OrderItem`  | belongsTo         | `Order`       |
| `OrderItem`  | belongsTo         | `Product`     |
| `Product`    | belongsTo         | `Category`    |
| `Product`    | hasMany           | `OrderItem`   |
| `Category`   | hasMany           | `Product`     |
| `Payment`    | belongsTo         | `Order`       |

---

## Contoh Data (Sample Data)

### Tabel `users`
```sql
INSERT INTO users (name, email, password, role) VALUES
('Admin Jagoan Kue', 'admin@jagoan-kue.com', '<bcrypt_hash>', 'admin'),
('Budi Santoso', 'customer@jagoan-kue.com', '<bcrypt_hash>', 'customer');
```

### Tabel `categories`
```sql
INSERT INTO categories (name, slug, description) VALUES
('Kue Ulang Tahun', 'kue-ulang-tahun', 'Kue spesial untuk perayaan ulang tahun'),
('Kue Pernikahan', 'kue-pernikahan', 'Kue mewah untuk hari pernikahan'),
('Kue Kering', 'kue-kering', 'Aneka kue kering untuk berbagai acara');
```

### Tabel `products`
```sql
INSERT INTO products (category_id, name, slug, price, stock, is_available) VALUES
(1, 'Kue Ulang Tahun Coklat', 'kue-ulang-tahun-coklat', 250000, 10, 1),
(1, 'Kue Ulang Tahun Vanilla', 'kue-ulang-tahun-vanilla', 200000, 15, 1),
(3, 'Nastar Keju', 'nastar-keju', 85000, 50, 1);
```

### Tabel `orders`
```sql
INSERT INTO orders (user_id, order_code, status, shipping_address, total_price) VALUES
(2, 'ORD-ABCD1234', 'completed', 'Jl. Mawar No. 5, Jakarta', 250000);
```
