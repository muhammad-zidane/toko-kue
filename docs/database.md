# Dokumentasi Database — Jagoan Kue

## Daftar Tabel

| Tabel                       | Fungsi                                                    |
|-----------------------------|-----------------------------------------------------------|
| `users`                     | Data akun pengguna (admin dan customer)                   |
| `categories`                | Kategori produk kue                                       |
| `products`                  | Data produk kue yang dijual                               |
| `orders`                    | Data pesanan yang dibuat customer                         |
| `order_items`               | Detail item dalam setiap pesanan                          |
| `order_item_customizations` | Pilihan kustomisasi per item pesanan                      |
| `payments`                  | Informasi pembayaran tiap pesanan                         |
| `product_reviews`           | Ulasan produk dari customer (dengan moderasi admin)       |
| `product_review_images`     | Gambar lampiran ulasan produk                             |
| `addresses`                 | Alamat pengiriman tersimpan milik customer                |
| `customization_options`     | Opsi kustomisasi produk per kategori (rasa, ukuran, dll)  |
| `vouchers`                  | Voucher diskon                                            |
| `shipping_zones`            | Zona pengiriman beserta tarif ongkir                      |
| `banners`                   | Banner/slider di halaman utama                            |
| `testimonials`              | Testimoni pelanggan di halaman utama                      |
| `notifications`             | Notifikasi untuk admin (pesanan baru, dll)                |
| `sessions`                  | Sesi login pengguna (dikelola Laravel)                    |
| `password_reset_tokens`     | Token reset password sementara                            |
| `cache`                     | Cache Laravel                                             |
| `jobs`                      | Antrian job Laravel                                       |

---

## Penjelasan Kolom Tiap Tabel

### `users`

| Kolom               | Tipe          | Keterangan                              |
|---------------------|---------------|-----------------------------------------|
| `id`                | bigint PK     | Primary key auto increment              |
| `name`              | varchar(255)  | Nama lengkap pengguna                   |
| `email`             | varchar(255)  | Email unik, digunakan untuk login       |
| `email_verified_at` | timestamp     | Waktu verifikasi email (nullable)       |
| `password`          | varchar(255)  | Password ter-hash (bcrypt)              |
| `role`              | varchar       | Peran: `admin` atau `customer`          |
| `remember_token`    | varchar(100)  | Token "ingat saya" (nullable)           |
| `created_at`        | timestamp     | Waktu registrasi                        |
| `updated_at`        | timestamp     | Waktu update terakhir                   |

### `categories`

| Kolom         | Tipe         | Keterangan                              |
|---------------|--------------|-----------------------------------------|
| `id`          | bigint PK    | Primary key auto increment              |
| `name`        | varchar(255) | Nama kategori (mis. "Kue Ulang Tahun")  |
| `slug`        | varchar(255) | Versi URL-friendly dari nama (unik)     |
| `description` | text         | Deskripsi kategori (nullable)           |
| `created_at`  | timestamp    | —                                       |
| `updated_at`  | timestamp    | —                                       |

### `products`

| Kolom          | Tipe                               | Keterangan                                           |
|----------------|------------------------------------|------------------------------------------------------|
| `id`           | bigint PK                          | Primary key auto increment                           |
| `category_id`  | bigint FK                          | Referensi ke `categories.id`                         |
| `name`         | varchar(255)                       | Nama produk                                          |
| `slug`         | varchar(255)                       | Digunakan sebagai route key produk (unik)            |
| `description`  | text                               | Deskripsi produk (nullable)                          |
| `price`        | decimal(10,2)                      | Harga satuan produk                                  |
| `stock`        | integer                            | Jumlah stok tersedia                                 |
| `image`        | varchar                            | Path gambar produk di storage (nullable)             |
| `is_available` | boolean                            | Status ketersediaan produk                           |
| `badge`        | enum(`best_seller`,`new`,`sale`)   | Label badge produk, nullable                         |
| `created_at`   | timestamp                          | —                                                    |
| `updated_at`   | timestamp                          | —                                                    |

### `orders`

| Kolom             | Tipe                                                                    | Keterangan                                     |
|-------------------|-------------------------------------------------------------------------|------------------------------------------------|
| `id`              | bigint PK                                                               | Primary key auto increment                     |
| `user_id`         | bigint FK                                                               | Referensi ke `users.id`                        |
| `order_code`      | varchar                                                                 | Kode unik pesanan, format `ORD-XXXXXXXX`       |
| `status`          | enum(`pending`,`processing`,`shipped`,`completed`,`cancelled`)          | Status pesanan                                 |
| `shipping_address`| text                                                                    | Alamat pengiriman pesanan                      |
| `total_price`     | decimal(10,2)                                                           | Total harga (sudah termasuk ongkir & diskon)   |
| `payment_status`  | enum(`unpaid`,`dp`,`paid`)                                              | Status pembayaran di level pesanan             |
| `dp_amount`       | decimal(10,2)                                                           | Nominal DP yang harus/sudah dibayar            |
| `paid_amount`     | decimal(10,2)                                                           | Total yang sudah dibayar customer              |
| `notes`           | text                                                                    | Catatan tambahan dari customer (nullable)      |
| `delivery_method` | enum(`pickup`,`delivery`)                                               | Metode pengambilan pesanan                     |
| `delivery_date`   | date                                                                    | Tanggal pengiriman/pengambilan (nullable)      |
| `delivery_slot`   | varchar                                                                 | Slot waktu pengiriman (nullable)               |
| `shipping_cost`   | decimal(10,2)                                                           | Biaya ongkir                                   |
| `voucher_code`    | varchar                                                                 | Kode voucher yang dipakai (nullable)           |
| `discount_amount` | decimal(10,2)                                                           | Jumlah diskon dari voucher                     |
| `created_at`      | timestamp                                                               | —                                              |
| `updated_at`      | timestamp                                                               | —                                              |

### `order_items`

| Kolom        | Tipe          | Keterangan                                |
|--------------|---------------|-------------------------------------------|
| `id`         | bigint PK     | Primary key auto increment                |
| `order_id`   | bigint FK     | Referensi ke `orders.id`                  |
| `product_id` | bigint FK     | Referensi ke `products.id`                |
| `quantity`   | integer       | Jumlah produk yang dipesan                |
| `price`      | decimal(10,2) | Harga satuan saat transaksi (snapshot)    |
| `note`       | text          | Catatan khusus untuk item ini (nullable)  |
| `created_at` | timestamp     | —                                         |
| `updated_at` | timestamp     | —                                         |

### `order_item_customizations`

| Kolom                    | Tipe          | Keterangan                                           |
|--------------------------|---------------|------------------------------------------------------|
| `id`                     | bigint PK     | Primary key auto increment                           |
| `order_item_id`          | bigint FK     | Referensi ke `order_items.id`                        |
| `customization_option_id`| bigint FK     | Referensi ke `customization_options.id`              |
| `value`                  | varchar       | Nilai bebas untuk opsi free-text (nullable)          |
| `extra_price`            | decimal(10,2) | Harga tambahan dari opsi ini                         |
| `created_at`             | timestamp     | —                                                    |
| `updated_at`             | timestamp     | —                                                    |

### `payments`

| Kolom            | Tipe                           | Keterangan                                         |
|------------------|--------------------------------|----------------------------------------------------|
| `id`             | bigint PK                      | Primary key auto increment                         |
| `order_id`       | bigint FK                      | Referensi ke `orders.id`                           |
| `payment_method` | varchar                        | Metode: `transfer_bank`, `ewallet`, `qris`, `cod`  |
| `status`         | enum(`unpaid`,`paid`,`failed`) | Status pembayaran                                  |
| `amount`         | decimal(10,2)                  | Jumlah yang dibayarkan (bisa DP atau lunas)        |
| `proof_image`    | varchar                        | Path gambar bukti pembayaran di storage (nullable) |
| `paid_at`        | timestamp                      | Waktu pembayaran dikonfirmasi (nullable)            |
| `created_at`     | timestamp                      | —                                                  |
| `updated_at`     | timestamp                      | —                                                  |

### `product_reviews`

| Kolom         | Tipe            | Keterangan                                               |
|---------------|-----------------|----------------------------------------------------------|
| `id`          | bigint PK       | Primary key auto increment                               |
| `user_id`     | bigint FK       | Referensi ke `users.id`                                  |
| `product_id`  | bigint FK       | Referensi ke `products.id`                               |
| `order_id`    | bigint FK       | Referensi ke `orders.id` (ulasan terikat pada pesanan)   |
| `rating`      | tinyint         | Nilai 1–5                                                |
| `comment`     | text            | Isi ulasan                                               |
| `is_approved` | boolean         | Status moderasi oleh admin (default `false`)             |
| `created_at`  | timestamp       | —                                                        |
| `updated_at`  | timestamp       | —                                                        |

Constraint unik: `(user_id, product_id, order_id)` — satu ulasan per produk per pesanan per customer.

### `product_review_images`

| Kolom               | Tipe      | Keterangan                                  |
|---------------------|-----------|---------------------------------------------|
| `id`                | bigint PK | Primary key auto increment                  |
| `product_review_id` | bigint FK | Referensi ke `product_reviews.id`           |
| `path`              | varchar   | Path file gambar di storage                 |
| `created_at`        | timestamp | —                                           |
| `updated_at`        | timestamp | —                                           |

### `addresses`

| Kolom            | Tipe         | Keterangan                                        |
|------------------|--------------|---------------------------------------------------|
| `id`             | bigint PK    | Primary key auto increment                        |
| `user_id`        | bigint FK    | Referensi ke `users.id`                           |
| `label`          | varchar      | Label alamat, mis. "Rumah", "Kantor"              |
| `recipient_name` | varchar(255) | Nama penerima                                     |
| `phone`          | varchar(20)  | Nomor telepon penerima                            |
| `street`         | text         | Nama jalan dan nomor                              |
| `rt_rw`          | varchar(20)  | RT/RW (nullable)                                  |
| `kelurahan`      | varchar      | Kelurahan (nullable)                              |
| `kecamatan`      | varchar      | Kecamatan (nullable)                              |
| `city`           | varchar      | Kota/kabupaten                                    |
| `postal_code`    | varchar(10)  | Kode pos (nullable)                               |
| `is_default`     | boolean      | Apakah alamat utama (default `false`)             |
| `created_at`     | timestamp    | —                                                 |
| `updated_at`     | timestamp    | —                                                 |

### `customization_options`

| Kolom         | Tipe                                      | Keterangan                                      |
|---------------|-------------------------------------------|-------------------------------------------------|
| `id`          | bigint PK                                 | Primary key auto increment                      |
| `category_id` | bigint FK                                 | Referensi ke `categories.id` (nullable)         |
| `type`        | enum(`rasa`,`ukuran`,`topping`,`lainnya`) | Jenis kustomisasi                               |
| `name`        | varchar                                   | Nama opsi, mis. "Coklat", "20cm", "Sprinkles"   |
| `extra_price` | decimal(10,2)                             | Harga tambahan opsi ini                         |
| `is_active`   | boolean                                   | Status aktif/nonaktif                           |
| `sort_order`  | integer                                   | Urutan tampil                                   |
| `created_at`  | timestamp                                 | —                                               |
| `updated_at`  | timestamp                                 | —                                               |

### `vouchers`

| Kolom          | Tipe                       | Keterangan                                        |
|----------------|----------------------------|---------------------------------------------------|
| `id`           | bigint PK                  | Primary key auto increment                        |
| `code`         | varchar                    | Kode voucher unik                                 |
| `type`         | enum(`percent`,`fixed`)    | Jenis diskon: persentase atau nominal tetap       |
| `value`        | decimal(10,2)              | Nilai diskon                                      |
| `usage_limit`  | integer                    | Batas pemakaian total (nullable = tidak terbatas) |
| `used_count`   | integer                    | Sudah dipakai berapa kali                         |
| `min_purchase` | decimal(10,2)              | Minimum total belanja untuk memakai voucher       |
| `is_active`    | boolean                    | Status aktif/nonaktif                             |
| `expires_at`   | timestamp                  | Tanggal kadaluarsa (nullable)                     |
| `created_at`   | timestamp                  | —                                                 |
| `updated_at`   | timestamp                  | —                                                 |

### `shipping_zones`

| Kolom          | Tipe          | Keterangan                      |
|----------------|---------------|---------------------------------|
| `id`           | bigint PK     | Primary key auto increment      |
| `area_name`    | varchar       | Nama area/zona pengiriman       |
| `cost`         | decimal(10,2) | Biaya ongkir untuk zona ini     |
| `is_available` | boolean       | Status aktif/nonaktif           |
| `created_at`   | timestamp     | —                               |
| `updated_at`   | timestamp     | —                               |

### `banners`

| Kolom       | Tipe      | Keterangan                                         |
|-------------|-----------|----------------------------------------------------|
| `id`        | bigint PK | Primary key auto increment                         |
| `title`     | varchar   | Judul banner                                       |
| `subtitle`  | varchar   | Subjudul (nullable)                                |
| `image`     | varchar   | Path gambar banner di storage (nullable)           |
| `link`      | varchar   | URL tujuan saat banner diklik (nullable)           |
| `is_active` | boolean   | Status aktif/nonaktif                              |
| `order`     | integer   | Urutan tampil                                      |
| `created_at`| timestamp | —                                                  |
| `updated_at`| timestamp | —                                                  |

### `testimonials`

| Kolom        | Tipe         | Keterangan                         |
|--------------|--------------|------------------------------------|
| `id`         | bigint PK    | Primary key auto increment         |
| `name`       | varchar(255) | Nama pemberi testimoni             |
| `role`       | varchar(255) | Peran/jabatan (nullable)           |
| `text`       | text         | Isi ulasan                         |
| `created_at` | timestamp    | —                                  |
| `updated_at` | timestamp    | —                                  |

### `notifications`

Menggunakan format Laravel database notifications (polymorphic).

| Kolom              | Tipe      | Keterangan                                  |
|--------------------|-----------|---------------------------------------------|
| `id`               | uuid PK   | UUID primary key                            |
| `type`             | varchar   | Nama class notifikasi                       |
| `notifiable_type`  | varchar   | Tipe model penerima                         |
| `notifiable_id`    | bigint    | ID model penerima                           |
| `data`             | text      | Isi notifikasi (JSON)                       |
| `read_at`          | timestamp | Waktu dibaca (nullable = belum dibaca)      |
| `created_at`       | timestamp | —                                           |
| `updated_at`       | timestamp | —                                           |

---

## Diagram Relasi Antar Tabel (ERD)

```
users
  ├── hasMany ──> orders
  │                 ├── hasMany ──> order_items
  │                 │                 ├── belongsTo ──> products
  │                 │                 │                   └── belongsTo ──> categories
  │                 │                 │                   └── hasMany ──> customization_options (via category)
  │                 │                 └── hasMany ──> order_item_customizations
  │                 │                                   └── belongsTo ──> customization_options
  │                 ├── hasMany ──> payments
  │                 └── hasMany ──> product_reviews (via order)
  ├── hasMany ──> product_reviews
  ├── hasMany ──> addresses
  └── hasMany ──> notifications (polymorphic)

products
  └── hasMany ──> product_reviews
                    └── hasMany ──> product_review_images

categories
  └── hasMany ──> customization_options

vouchers  (berdiri sendiri, kode disimpan di orders.voucher_code)
shipping_zones  (berdiri sendiri, cost disimpan di orders.shipping_cost)
banners  (berdiri sendiri)
testimonials  (berdiri sendiri)
```

**Ringkasan relasi model:**

| Model                      | Relasi    | Model Tujuan              |
|----------------------------|-----------|---------------------------|
| `User`                     | hasMany   | `Order`                   |
| `User`                     | hasMany   | `Address`                 |
| `User`                     | hasMany   | `ProductReview`           |
| `Order`                    | belongsTo | `User`                    |
| `Order`                    | hasMany   | `OrderItem`               |
| `Order`                    | hasMany   | `Payment`                 |
| `Order`                    | hasMany   | `ProductReview`           |
| `OrderItem`                | belongsTo | `Order`                   |
| `OrderItem`                | belongsTo | `Product`                 |
| `OrderItem`                | hasMany   | `OrderItemCustomization`  |
| `OrderItemCustomization`   | belongsTo | `OrderItem`               |
| `OrderItemCustomization`   | belongsTo | `CustomizationOption`     |
| `Product`                  | belongsTo | `Category`                |
| `Product`                  | hasMany   | `OrderItem`               |
| `Product`                  | hasMany   | `ProductReview`           |
| `Category`                 | hasMany   | `Product`                 |
| `Category`                 | hasMany   | `CustomizationOption`     |
| `Payment`                  | belongsTo | `Order`                   |
| `ProductReview`            | belongsTo | `User`                    |
| `ProductReview`            | belongsTo | `Product`                 |
| `ProductReview`            | belongsTo | `Order`                   |
| `ProductReview`            | hasMany   | `ProductReviewImage`      |
| `Address`                  | belongsTo | `User`                    |
| `CustomizationOption`      | belongsTo | `Category`                |
