# Dokumentasi Route — Jagoan Kue

## Route Publik

| Method | URL             | Nama Route    | Controller@Method         | Middleware | Keterangan                                     |
|--------|-----------------|---------------|---------------------------|------------|------------------------------------------------|
| GET    | `/`             | `home`        | Closure                   | —          | Halaman utama dengan kategori, produk unggulan, dan testimoni |
| GET    | `/products`     | `products.index` | `ProductController@index` | —       | Daftar semua produk berdasarkan kategori        |
| GET    | `/products/{product}` | `products.show` | `ProductController@show` | — | Detail produk (route key: slug)               |

---

## Route Autentikasi (Laravel Breeze)

| Method | URL                | Nama Route          | Keterangan                      |
|--------|--------------------|---------------------|---------------------------------|
| GET    | `/register`        | `register`          | Form registrasi customer        |
| POST   | `/register`        | `register`          | Proses registrasi               |
| GET    | `/login`           | `login`             | Form login                      |
| POST   | `/login`           | `login`             | Proses login                    |
| POST   | `/logout`          | `logout`            | Proses logout                   |
| GET    | `/forgot-password` | `password.request`  | Form lupa password              |
| POST   | `/forgot-password` | `password.email`    | Kirim email reset password      |
| GET    | `/reset-password/{token}` | `password.reset` | Form reset password       |
| POST   | `/reset-password`  | `password.update`   | Proses reset password           |

---

## Route Pengguna (Wajib Login)

Semua route di bawah menggunakan middleware: `auth`

### Profil

| Method  | URL              | Nama Route       | Controller@Method            | Keterangan                              |
|---------|------------------|------------------|------------------------------|-----------------------------------------|
| GET     | `/profile`       | `profile.index`  | `ProfileController@index`    | Halaman ringkasan profil dan statistik  |
| GET     | `/profile/edit`  | `profile.edit`   | `ProfileController@edit`     | Form edit data profil                   |
| PATCH   | `/profile`       | `profile.update` | `ProfileController@update`   | Simpan perubahan data profil            |
| DELETE  | `/profile`       | `profile.destroy`| `ProfileController@destroy`  | Hapus akun pengguna                     |

### Pesanan

| Method | URL                           | Nama Route          | Controller@Method           | Keterangan                                       |
|--------|-------------------------------|---------------------|-----------------------------|--------------------------------------------------|
| GET    | `/orders`                     | `orders.index`      | `OrderController@index`     | Daftar pesanan milik pengguna                    |
| POST   | `/orders`                     | `orders.store`      | `OrderController@store`     | Buat pesanan baru                                |
| GET    | `/orders/{order}`             | `orders.show`       | `OrderController@show`      | Detail pesanan                                   |
| GET    | `/checkout/{product_id}`      | `orders.create`     | Closure                     | Form checkout langsung dari halaman produk       |
| GET    | `/orders/{order}/payment`     | `orders.payment`    | Closure                     | Halaman instruksi dan upload bukti pembayaran    |
| GET    | `/orders/{order}/success`     | `orders.success`    | Closure                     | Halaman konfirmasi pesanan berhasil              |
| POST   | `/orders/{order}/upload-proof`| `orders.uploadProof`| `OrderController@uploadProof`| Upload bukti pembayaran transfer/e-wallet/QRIS  |

### Keranjang

| Method | URL               | Nama Route      | Controller@Method       | Keterangan                              |
|--------|-------------------|-----------------|-------------------------|-----------------------------------------|
| GET    | `/cart`           | `cart.index`    | `CartController@index`  | Tampilkan isi keranjang belanja         |
| POST   | `/cart/add`       | `cart.add`      | `CartController@add`    | Tambah produk ke keranjang              |
| POST   | `/cart/remove`    | `cart.remove`   | `CartController@remove` | Hapus satu atau beberapa item keranjang |
| POST   | `/cart/clear`     | `cart.clear`    | `CartController@clear`  | Kosongkan seluruh keranjang             |
| GET    | `/cart/checkout`  | `cart.checkout` | `CartController@checkout`| Lanjut ke form checkout dari keranjang |

---

## Route Admin (Prefix: `/admin`, Name: `admin.*`)

Semua route admin menggunakan middleware: `auth`  
Akses ditolak (403) jika pengguna bukan admin (dicek via `AdminController::checkAdmin()`).

### Dashboard & Laporan

| Method | URL                  | Nama Route         | Controller@Method              | Keterangan                                   |
|--------|----------------------|--------------------|--------------------------------|----------------------------------------------|
| GET    | `/admin/dashboard`   | `admin.dashboard`  | `AdminController@dashboard`    | Statistik KPI, grafik pendapatan 7 hari, top produk |
| GET    | `/admin/analytics`   | `admin.analytics`  | `AdminController@analytics`    | Analitik pendapatan, distribusi status, kategori   |
| GET    | `/admin/finance`     | `admin.finance`    | `AdminController@finance`      | Daftar semua pembayaran dan rekapitulasi keuangan  |

### Manajemen Pesanan

| Method | URL                                       | Nama Route              | Controller@Method                    | Keterangan                          |
|--------|-------------------------------------------|-------------------------|--------------------------------------|-------------------------------------|
| GET    | `/admin/orders`                           | `admin.orders`          | `AdminController@orders`             | Daftar semua pesanan dengan pagination |
| GET    | `/admin/orders/{order}`                   | `admin.orders.detail`   | `AdminController@orderDetail`        | Detail pesanan beserta item dan pembayaran |
| PATCH  | `/admin/orders/{order}/status/{status}`   | `admin.orders.status`   | `AdminController@updateOrderStatus`  | Ubah status pesanan                 |

### Manajemen Produk

| Method | URL                         | Nama Route                  | Controller@Method             | Keterangan                    |
|--------|-----------------------------|-----------------------------|-------------------------------|-------------------------------|
| GET    | `/admin/products-list`      | `admin.products.index`      | `AdminController@adminProducts` | Daftar produk untuk admin   |
| GET    | `/admin/products/create`    | `admin.products.create`     | `ProductController@create`    | Form tambah produk baru       |
| POST   | `/admin/products`           | `admin.products.store`      | `ProductController@store`     | Simpan produk baru            |
| GET    | `/admin/products/{product}/edit` | `admin.products.edit`  | `ProductController@edit`      | Form edit produk              |
| PUT    | `/admin/products/{product}` | `admin.products.update`     | `ProductController@update`    | Simpan perubahan produk       |
| DELETE | `/admin/products/{product}` | `admin.products.destroy`    | `ProductController@destroy`   | Hapus produk                  |

### Manajemen Kategori

| Method | URL                             | Nama Route                  | Controller@Method                 | Keterangan              |
|--------|---------------------------------|-----------------------------|-----------------------------------|-------------------------|
| GET    | `/admin/categories`             | `admin.categories`          | `AdminController@categories`      | Daftar kategori         |
| POST   | `/admin/categories`             | `admin.categories.store`    | `AdminController@storeCategory`   | Tambah kategori baru    |
| DELETE | `/admin/categories/{category}`  | `admin.categories.destroy`  | `AdminController@destroyCategory` | Hapus kategori          |

### Manajemen Pelanggan & Pengaturan

| Method | URL                   | Nama Route          | Controller@Method                | Keterangan                       |
|--------|-----------------------|---------------------|----------------------------------|----------------------------------|
| GET    | `/admin/customers`    | `admin.customers`   | `AdminController@customers`      | Daftar pelanggan dan statistik   |
| GET    | `/admin/settings`     | `admin.settings`    | `AdminController@settings`       | Form pengaturan akun admin       |
| POST   | `/admin/settings`     | `admin.settings.update` | `AdminController@updateSettings` | Simpan pengaturan akun admin |
