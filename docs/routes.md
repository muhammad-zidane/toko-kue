# Dokumentasi Route — Jagoan Kue

## Route Publik

Dapat diakses tanpa login.

| Method | URL                    | Nama Route       | Controller@Method          | Keterangan                                          |
|--------|------------------------|------------------|----------------------------|-----------------------------------------------------|
| GET    | `/`                    | `home`           | `HomeController@index`     | Halaman utama: banner, kategori, produk, testimoni  |
| GET    | `/about`               | `about`          | Closure (view)             | Halaman tentang toko                                |
| GET    | `/products`            | `products.index` | `ProductController@index`  | Katalog semua produk berdasarkan kategori           |
| GET    | `/products/{product}`  | `products.show`  | `ProductController@show`   | Detail produk (route key: slug)                     |
| POST   | `/voucher/apply`       | `voucher.apply`  | `VoucherController@apply`  | Validasi & terapkan kode voucher                    |

---

## Route Keranjang (Guest & Auth)

Keranjang berbasis session, dapat diakses tanpa login. Hanya halaman checkout yang memerlukan autentikasi.

| Method | URL                  | Nama Route          | Controller@Method            | Middleware        | Keterangan                              |
|--------|----------------------|---------------------|------------------------------|-------------------|-----------------------------------------|
| GET    | `/cart`              | `cart.index`        | `CartController@index`       | —                 | Tampilkan isi keranjang                 |
| POST   | `/cart/add`          | `cart.add`          | `CartController@add`         | throttle:20,1     | Tambah produk ke keranjang              |
| POST   | `/cart/update-item`  | `cart.updateItem`   | `CartController@updateItem`  | throttle:20,1     | Ubah jumlah item di keranjang           |
| POST   | `/cart/remove`       | `cart.remove`       | `CartController@remove`      | —                 | Hapus satu atau beberapa item           |
| POST   | `/cart/clear`        | `cart.clear`        | `CartController@clear`       | —                 | Kosongkan seluruh keranjang             |
| GET    | `/cart/checkout`     | `cart.checkout`     | `CartController@checkout`    | auth              | Lanjut ke form checkout (butuh login)   |

---

## Route Autentikasi (Laravel Breeze)

| Method | URL                       | Nama Route           | Keterangan                      |
|--------|---------------------------|----------------------|---------------------------------|
| GET    | `/register`               | `register`           | Form registrasi customer        |
| POST   | `/register`               | `register`           | Proses registrasi               |
| GET    | `/login`                  | `login`              | Form login                      |
| POST   | `/login`                  | `login`              | Proses login                    |
| POST   | `/logout`                 | `logout`             | Proses logout                   |
| GET    | `/forgot-password`        | `password.request`   | Form lupa password              |
| POST   | `/forgot-password`        | `password.email`     | Kirim email reset password      |
| GET    | `/reset-password/{token}` | `password.reset`     | Form reset password             |
| POST   | `/reset-password`         | `password.update`    | Proses reset password           |

---

## Route Pengguna (Wajib Login)

Semua route di bawah menggunakan middleware: `auth`

### Profil

| Method  | URL              | Nama Route        | Controller@Method           | Keterangan                            |
|---------|------------------|-------------------|-----------------------------|---------------------------------------|
| GET     | `/profile`       | `profile.index`   | `ProfileController@index`   | Halaman ringkasan profil & statistik  |
| GET     | `/profile/edit`  | `profile.edit`    | `ProfileController@edit`    | Form edit data profil                 |
| PATCH   | `/profile`       | `profile.update`  | `ProfileController@update`  | Simpan perubahan data profil          |
| DELETE  | `/profile`       | `profile.destroy` | `ProfileController@destroy` | Hapus akun pengguna                   |

### Ganti Password

| Method | URL                     | Nama Route               | Controller@Method                  | Keterangan            |
|--------|-------------------------|--------------------------|------------------------------------|-----------------------|
| GET    | `/akun/ganti-password`  | `account.change-password`| `AccountController@showChangePassword` | Form ganti password |
| POST   | `/akun/ganti-password`  | `account.update-password`| `AccountController@updatePassword` | Proses ganti password |

### Alamat Tersimpan

| Method | URL                                        | Nama Route                    | Controller@Method              | Keterangan                        |
|--------|--------------------------------------------|-------------------------------|--------------------------------|-----------------------------------|
| GET    | `/account/addresses`                       | `account.addresses.index`     | `AddressController@index`      | Daftar alamat tersimpan           |
| POST   | `/account/addresses`                       | `account.addresses.store`     | `AddressController@store`      | Tambah alamat baru                |
| PUT    | `/account/addresses/{address}`             | `account.addresses.update`    | `AddressController@update`     | Edit alamat                       |
| DELETE | `/account/addresses/{address}`             | `account.addresses.destroy`   | `AddressController@destroy`    | Hapus alamat                      |
| POST   | `/account/addresses/{address}/set-default` | `account.addresses.setDefault`| `AddressController@setDefault` | Jadikan alamat utama              |

### Pesanan

| Method | URL                                  | Nama Route           | Controller@Method               | Keterangan                                            |
|--------|--------------------------------------|----------------------|---------------------------------|-------------------------------------------------------|
| GET    | `/orders`                            | `orders.index`       | `OrderController@index`         | Daftar pesanan milik pengguna                         |
| POST   | `/orders`                            | `orders.store`       | `OrderController@store`         | Buat pesanan baru (throttle:20,1)                     |
| GET    | `/orders/{order}`                    | `orders.show`        | `OrderController@show`          | Detail pesanan                                        |
| GET    | `/orders/{order}/status`             | `orders.status`      | `OrderController@showStatus`    | Halaman pelacakan status pesanan                      |
| GET    | `/checkout/{product}`                | `orders.create`      | `OrderController@singleProductCheckout` | Form checkout langsung dari halaman produk   |
| GET    | `/orders/{order}/payment`            | `orders.payment`     | `OrderController@payment`       | Halaman instruksi & upload bukti pembayaran           |
| POST   | `/orders/{order}/upload-proof`       | `orders.uploadProof` | `OrderController@uploadProof`   | Upload bukti pembayaran (throttle:20,1)               |
| GET    | `/orders/{order}/success`            | `orders.success`     | `OrderController@success`       | Halaman konfirmasi pesanan berhasil                   |
| GET    | `/orders/{order}/invoice`            | `orders.invoice`     | `OrderController@invoice`       | Download invoice pesanan sebagai PDF                  |

### Ulasan Produk

| Method | URL                                              | Nama Route                      | Controller@Method                  | Keterangan                                  |
|--------|--------------------------------------------------|---------------------------------|------------------------------------|---------------------------------------------|
| GET    | `/orders/{order}/reviews`                        | `orders.reviews.index`          | `ProductReviewController@index`    | Form tulis ulasan untuk produk dalam pesanan|
| POST   | `/orders/{order}/reviews/{product}`              | `orders.reviews.store`          | `ProductReviewController@store`    | Simpan ulasan (throttle:20,1)               |
| PATCH  | `/orders/{order}/reviews/{review}`               | `orders.reviews.update`         | `ProductReviewController@update`   | Edit ulasan (throttle:20,1)                 |
| DELETE | `/orders/{order}/reviews/{review}`               | `orders.reviews.destroy`        | `ProductReviewController@destroy`  | Hapus ulasan                                |
| DELETE | `/orders/{order}/reviews/{review}/images/{image}`| `orders.reviews.images.destroy` | `ProductReviewController@destroyImage` | Hapus gambar lampiran ulasan            |

---

## Route Admin (Prefix: `/admin`, Name: `admin.*`)

Semua route admin menggunakan middleware: `['auth', 'admin']`  
Middleware `admin` (`EnsureUserIsAdmin`) menolak akses (403) jika role pengguna bukan `admin`.

### Dashboard

| Method | URL                  | Nama Route         | Controller@Method            | Keterangan                                          |
|--------|----------------------|--------------------|------------------------------|-----------------------------------------------------|
| GET    | `/admin/dashboard`   | `admin.dashboard`  | `AdminController@dashboard`  | KPI bulan ini, grafik 7 hari, top produk, aktivitas |

### Manajemen Pesanan

| Method | URL                                         | Nama Route                     | Controller@Method                    | Keterangan                              |
|--------|---------------------------------------------|--------------------------------|--------------------------------------|-----------------------------------------|
| GET    | `/admin/orders`                             | `admin.orders.index`           | `AdminController@orders`             | Daftar semua pesanan (paginated)        |
| GET    | `/admin/orders/{order}`                     | `admin.orders.show`            | `AdminController@orderDetail`        | Detail pesanan beserta item & pembayaran|
| GET    | `/admin/orders/{order}/download-proof`      | `admin.orders.downloadProof`   | `AdminController@downloadProof`      | Download bukti pembayaran               |
| PATCH  | `/admin/orders/{order}/status/{status}`     | `admin.orders.status`          | `AdminController@updateOrderStatus`  | Ubah status pesanan                     |
| POST   | `/admin/orders/{order}/confirm-payment`     | `admin.orders.confirmPayment`  | `AdminController@confirmPayment`     | Konfirmasi pembayaran customer          |
| POST   | `/admin/orders/{order}/reject-payment`      | `admin.orders.rejectPayment`   | `AdminController@rejectPayment`      | Tolak/minta ulang bukti pembayaran      |

### Manajemen Produk

| Method | URL                              | Nama Route                 | Controller@Method           | Keterangan              |
|--------|----------------------------------|----------------------------|-----------------------------|-------------------------|
| GET    | `/admin/products-list`           | `admin.products.index`     | `AdminController@adminProducts` | Daftar produk admin |
| GET    | `/admin/products/create`         | `admin.products.create`    | `ProductController@create`  | Form tambah produk baru |
| POST   | `/admin/products`                | `admin.products.store`     | `ProductController@store`   | Simpan produk baru      |
| GET    | `/admin/products/{product}/edit` | `admin.products.edit`      | `ProductController@edit`    | Form edit produk        |
| PUT    | `/admin/products/{product}`      | `admin.products.update`    | `ProductController@update`  | Simpan perubahan produk |
| DELETE | `/admin/products/{product}`      | `admin.products.destroy`   | `ProductController@destroy` | Hapus produk            |

### Manajemen Kategori

| Method | URL                              | Nama Route                   | Controller@Method                    | Keterangan          |
|--------|----------------------------------|------------------------------|--------------------------------------|---------------------|
| GET    | `/admin/categories`              | `admin.categories.index`     | `AdminController@categories`         | Daftar kategori     |
| POST   | `/admin/categories`              | `admin.categories.store`     | `AdminController@storeCategory`      | Tambah kategori     |
| DELETE | `/admin/categories/{category}`   | `admin.categories.destroy`   | `AdminController@destroyCategory`    | Hapus kategori      |

### Manajemen Voucher

| Method | URL                         | Nama Route                | Controller@Method                  | Keterangan        |
|--------|-----------------------------|---------------------------|------------------------------------|-------------------|
| GET    | `/admin/vouchers`           | `admin.vouchers.index`    | `AdminController@vouchers`         | Daftar voucher    |
| POST   | `/admin/vouchers`           | `admin.vouchers.store`    | `AdminController@storeVoucher`     | Tambah voucher    |
| PUT    | `/admin/vouchers/{voucher}` | `admin.vouchers.update`   | `AdminController@updateVoucher`    | Edit voucher      |
| DELETE | `/admin/vouchers/{voucher}` | `admin.vouchers.destroy`  | `AdminController@destroyVoucher`   | Hapus voucher     |

### Manajemen Zona Pengiriman

| Method | URL                             | Nama Route                     | Controller@Method                    | Keterangan              |
|--------|---------------------------------|--------------------------------|--------------------------------------|-------------------------|
| GET    | `/admin/shipping-zones`         | `admin.shipping-zones.index`   | `AdminController@shippingZones`      | Daftar zona ongkir      |
| POST   | `/admin/shipping-zones`         | `admin.shipping-zones.store`   | `AdminController@storeShippingZone`  | Tambah zona             |
| PUT    | `/admin/shipping-zones/{zone}`  | `admin.shipping-zones.update`  | `AdminController@updateShippingZone` | Edit zona               |
| DELETE | `/admin/shipping-zones/{zone}`  | `admin.shipping-zones.destroy` | `AdminController@destroyShippingZone`| Hapus zona              |

### Manajemen Banner

| Method | URL                        | Nama Route                | Controller@Method                | Keterangan        |
|--------|----------------------------|---------------------------|----------------------------------|-------------------|
| GET    | `/admin/banners`           | `admin.banners.index`     | `AdminController@banners`        | Daftar banner     |
| POST   | `/admin/banners`           | `admin.banners.store`     | `AdminController@storeBanner`    | Tambah banner     |
| PUT    | `/admin/banners/{banner}`  | `admin.banners.update`    | `AdminController@updateBanner`   | Edit banner       |
| DELETE | `/admin/banners/{banner}`  | `admin.banners.destroy`   | `AdminController@destroyBanner`  | Hapus banner      |

### Kustomisasi Produk

| Method | URL                                   | Nama Route                        | Controller@Method                      | Keterangan                    |
|--------|---------------------------------------|-----------------------------------|----------------------------------------|-------------------------------|
| GET    | `/admin/customizations`               | `admin.customizations.index`      | `CustomizationController@index`        | Daftar opsi kustomisasi       |
| POST   | `/admin/customizations`               | `admin.customizations.store`      | `CustomizationController@store`        | Tambah opsi                   |
| PUT    | `/admin/customizations/{option}`      | `admin.customizations.update`     | `CustomizationController@update`       | Edit opsi                     |
| POST   | `/admin/customizations/{option}/toggle` | `admin.customizations.toggle`   | `CustomizationController@toggle`       | Aktifkan/nonaktifkan opsi     |
| DELETE | `/admin/customizations/{option}`      | `admin.customizations.destroy`    | `CustomizationController@destroy`      | Hapus opsi                    |

### Moderasi Ulasan

| Method | URL                              | Nama Route                  | Controller@Method                  | Keterangan                |
|--------|----------------------------------|-----------------------------|------------------------------------|---------------------------|
| GET    | `/admin/reviews`                 | `admin.reviews.index`       | `AdminController@reviews`          | Daftar ulasan menunggu & tersetujui |
| PATCH  | `/admin/reviews/{review}/approve`| `admin.reviews.approve`     | `AdminController@approveReview`    | Setujui ulasan            |
| DELETE | `/admin/reviews/{review}`        | `admin.reviews.destroy`     | `AdminController@destroyReview`    | Hapus ulasan              |

### Laporan & Keuangan

| Method | URL                       | Nama Route                  | Controller@Method                  | Keterangan                                       |
|--------|---------------------------|-----------------------------|------------------------------------|--------------------------------------------------|
| GET    | `/admin/analytics`        | `admin.analytics.index`     | `AdminController@analytics`        | Analitik pendapatan, distribusi status, kategori |
| GET    | `/admin/analytics/export` | `admin.analytics.export`    | `AdminController@exportLaporan`    | Export laporan ke file Excel                     |
| GET    | `/admin/finance`          | `admin.finance.index`       | `AdminController@finance`          | Rekapitulasi semua pembayaran                    |

### Lainnya

| Method | URL                                  | Nama Route                        | Controller@Method                          | Keterangan                             |
|--------|--------------------------------------|-----------------------------------|--------------------------------------------|----------------------------------------|
| GET    | `/admin/customers`                   | `admin.customers.index`           | `AdminController@customers`                | Daftar pelanggan dan statistik         |
| GET    | `/admin/production-calendar`         | `admin.production-calendar.index` | `AdminController@productionCalendar`       | Kalender produksi pesanan              |
| GET    | `/admin/settings`                    | `admin.settings.index`            | `AdminController@settings`                 | Form pengaturan akun admin             |
| POST   | `/admin/settings`                    | `admin.settings.update`           | `AdminController@updateSettings`           | Simpan pengaturan akun admin           |
| POST   | `/admin/notifications/read-all`      | `admin.notifications.readAll`     | `AdminController@markAllNotificationsRead` | Tandai semua notifikasi sudah dibaca   |
| POST   | `/admin/notifications/{id}/read`     | `admin.notifications.read`        | `AdminController@markNotificationRead`     | Tandai satu notifikasi sudah dibaca    |
