# Issue: Implementasi Unit Test untuk Semua Fitur

## Deskripsi

Buatkan unit test lengkap untuk seluruh fitur aplikasi **Toko-Kue** (Laravel).

## Aturan Umum

1. **Framework Test:** Gunakan `bun test`.
2. **Lokasi File:** Simpan semua file test di folder `tests/`.
3. **Konsistensi Data:** Setiap skenario test **WAJIB** menghapus/reset data terkait terlebih dahulu sebelum test dijalankan, agar hasilnya konsisten dan tidak tergantung state sebelumnya.
4. **Isolasi:** Setiap test harus bisa dijalankan secara independen, tidak bergantung urutan eksekusi test lain.

---

## Referensi Struktur Database

| Tabel | Kolom Penting |
|---|---|
| `users` | `id`, `name`, `email`, `password`, `role` (admin/customer) |
| `categories` | `id`, `name`, `slug` (unique), `description` |
| `products` | `id`, `category_id` (FK), `name`, `slug` (unique), `description`, `price`, `stock`, `image`, `is_available` |
| `orders` | `id`, `user_id` (FK), `order_code` (unique), `status` (pending/processing/completed/cancelled), `shipping_address`, `total_price`, `notes` |
| `order_items` | `id`, `order_id` (FK), `product_id` (FK), `quantity`, `price` |
| `payments` | `id`, `order_id` (FK), `payment_method`, `status` (unpaid/paid/failed), `amount`, `proof_image`, `paid_at` |
| `testimonials` | `id`, `name`, `role`, `text` |

> **Catatan:** Product menggunakan `slug` sebagai route key (`getRouteKeyName()`), bukan `id`.

---

## Daftar Skenario Test

### 1. Autentikasi (`tests/auth.test.ts`)

#### Register
- Berhasil register dengan `name`, `email`, `password`, `password_confirmation` yang valid
- Gagal register jika `email` sudah terdaftar
- Gagal register jika `email` format tidak valid
- Gagal register jika `password` dan `password_confirmation` tidak cocok
- Gagal register jika field wajib (`name`, `email`, `password`) kosong
- Setelah register berhasil, user otomatis ter-login (session aktif)

#### Login
- Berhasil login dengan email dan password yang benar
- Gagal login dengan email yang tidak terdaftar
- Gagal login dengan password yang salah
- Setelah login berhasil, session ter-regenerate

#### Logout
- Berhasil logout, session di-invalidate
- Setelah logout, tidak bisa mengakses halaman yang butuh autentikasi

---

### 2. Halaman Publik (`tests/public-pages.test.ts`)

#### Halaman Utama (`/`)
- Berhasil memuat halaman utama (status 200)
- Response mengandung data categories (beserta jumlah produknya)
- Response mengandung data featured products (maks 3, hanya yang `is_available = true`)
- Response mengandung data testimonials (maks 3, urutan terbaru)

#### Daftar Produk (`/products`)
- Berhasil memuat halaman daftar produk (status 200)
- Response mengandung data categories beserta produk-produknya

#### Detail Produk (`/products/{slug}`)
- Berhasil memuat detail produk berdasarkan slug (status 200)
- Mengembalikan 404 jika slug produk tidak ditemukan

---

### 3. Profil Pengguna (`tests/profile.test.ts`)

> Semua endpoint profil membutuhkan autentikasi. Test harus memastikan guest di-redirect.

#### Lihat Profil (`GET /profile`)
- Berhasil menampilkan profil user yang sedang login
- Response mengandung data: `orderCount`, `activeOrders`, `totalSpent`
- Guest (tidak login) di-redirect ke halaman login

#### Edit Profil (`GET /profile/edit`)
- Berhasil menampilkan form edit profil

#### Update Profil (`PATCH /profile`)
- Berhasil update `name` dan `email` dengan data valid
- Jika email diubah, maka `email_verified_at` di-reset ke null
- Gagal update jika format email tidak valid

#### Hapus Akun (`DELETE /profile`)
- Berhasil hapus akun jika password yang dimasukkan benar
- Gagal hapus akun jika password yang dimasukkan salah
- Setelah akun dihapus, user otomatis ter-logout dan session di-invalidate

---

### 4. Keranjang Belanja (`tests/cart.test.ts`)

> Keranjang menggunakan **session** (bukan database). Semua endpoint butuh autentikasi.

#### Lihat Keranjang (`GET /cart`)
- Berhasil menampilkan halaman keranjang (kosong jika belum ada item)
- Menampilkan semua item yang sudah ditambahkan beserta kuantitasnya

#### Tambah ke Keranjang (`POST /cart/add`)
- Berhasil menambahkan produk ke keranjang dengan `product_id` dan `quantity` yang valid
- Jika produk yang sama ditambahkan lagi, kuantitas bertambah (bukan duplikat)
- Gagal jika `product_id` tidak ada di database
- Default `quantity` = 1 jika tidak dikirim

#### Hapus Item (`POST /cart/remove`)
- Berhasil menghapus satu atau lebih item dari keranjang berdasarkan array `ids`
- Response berupa JSON `{ success: true }`

#### Kosongkan Keranjang (`POST /cart/clear`)
- Berhasil mengosongkan seluruh isi keranjang (session `cart` dihapus)

#### Checkout dari Keranjang (`GET /cart/checkout`)
- Berhasil menampilkan halaman checkout jika keranjang ada isinya
- Redirect dengan error jika keranjang kosong

---

### 5. Pesanan / Order (`tests/order.test.ts`)

> Semua endpoint pesanan membutuhkan autentikasi.

#### Buat Pesanan (`POST /orders`)
- Berhasil membuat pesanan dengan data valid (`shipping_address`, `notes`, `items[]` berisi `product_id` dan `quantity`)
- Setelah order berhasil: record Order, OrderItem, dan Payment otomatis terbuat
- Status order awal = `pending`, status payment awal = `unpaid`
- Stok produk berkurang sesuai kuantitas yang dipesan
- Session `cart` otomatis dibersihkan setelah order berhasil
- `order_code` ter-generate otomatis dengan format `ORD-XXXXXXXX`
- Gagal jika `shipping_address` kosong
- Gagal jika `items` kosong
- Gagal jika stok produk tidak mencukupi (return error dengan info sisa stok)

#### Riwayat Pesanan (`GET /orders`)
- Berhasil menampilkan pesanan milik user yang login saja (bukan milik user lain)
- Data di-paginate (10 per halaman)
- Urutan dari yang terbaru

#### Detail Pesanan (`GET /orders/{order}`)
- Berhasil melihat detail pesanan milik sendiri
- Mengembalikan 403 jika mencoba melihat pesanan milik user lain

#### Halaman Pembayaran (`GET /orders/{order}/payment`)
- Berhasil memuat halaman pembayaran untuk pesanan milik sendiri
- Mengembalikan 403 jika pesanan bukan milik user yang login

#### Halaman Sukses (`GET /orders/{order}/success`)
- Berhasil memuat halaman sukses untuk pesanan milik sendiri
- Mengembalikan 403 jika pesanan bukan milik user yang login

---

### 6. Admin - Dashboard (`tests/admin-dashboard.test.ts`)

> Semua endpoint admin membutuhkan autentikasi **dan** role `admin`. User biasa (role customer) harus mendapat 403.

#### Otorisasi Admin
- User dengan role `admin` berhasil mengakses `/admin/dashboard` (status 200)
- User dengan role `customer` mendapat 403 saat mengakses `/admin/dashboard`
- Guest (tidak login) di-redirect ke halaman login

#### Dashboard Data (`GET /admin/dashboard`)
- Response mengandung statistik: `totalOrders`, `totalProducts`, `totalCustomers`, `totalRevenue`
- Response mengandung data growth bulan ini vs bulan lalu: `orderGrowth`, `revenueGrowth`, `customerGrowth`
- Response mengandung `pendingOrdersCount`
- Response mengandung `latestOrders` (5 pesanan terbaru)
- Response mengandung `dailyRevenue` (7 hari terakhir)
- Response mengandung `topProducts` (3 produk terlaris)
- Response mengandung `recentActivities`

---

### 7. Admin - Manajemen Pesanan (`tests/admin-orders.test.ts`)

#### Daftar Pesanan (`GET /admin/orders`)
- Berhasil menampilkan semua pesanan (paginate 10)
- Customer mendapat 403

#### Detail Pesanan (`GET /admin/orders/{order}`)
- Berhasil menampilkan detail pesanan termasuk relasi `user`, `orderItems.product`, `payment`

#### Update Status Pesanan (`PATCH /admin/orders/{order}/status/{status}`)
- Berhasil update status ke `processing`
- Berhasil update status ke `completed` → payment otomatis jadi `paid` dan `paid_at` terisi
- Berhasil update status ke `cancelled`
- Gagal jika status yang dikirim tidak valid (selain `pending`, `processing`, `completed`, `cancelled`)

---

### 8. Admin - CRUD Produk (`tests/admin-products.test.ts`)

#### Daftar Produk Admin (`GET /admin/products-list`)
- Berhasil menampilkan daftar produk beserta kategori (paginate 15)

#### Tambah Produk (`POST /admin/products`)
- Berhasil menambahkan produk dengan data lengkap (`name`, `category_id`, `price`, `stock`)
- Slug otomatis ter-generate dari nama produk
- Gagal jika field wajib tidak diisi
- Gagal jika `category_id` tidak valid
- Gagal jika `price` atau `stock` bernilai negatif

#### Edit Produk (`PUT /admin/products/{product}`)
- Berhasil mengubah data produk yang sudah ada
- Slug ter-update sesuai nama baru

#### Hapus Produk (`DELETE /admin/products/{product}`)
- Berhasil menghapus produk dari database

---

### 9. Admin - Manajemen Kategori (`tests/admin-categories.test.ts`)

#### Daftar Kategori (`GET /admin/categories`)
- Berhasil menampilkan semua kategori beserta jumlah produknya (`products_count`)

#### Tambah Kategori (`POST /admin/categories`)
- Berhasil menambahkan kategori dengan `name` dan `description`
- Slug otomatis ter-generate dari nama

#### Hapus Kategori (`DELETE /admin/categories/{category}`)
- Berhasil menghapus kategori

---

### 10. Admin - Pelanggan (`tests/admin-customers.test.ts`)

#### Daftar Pelanggan (`GET /admin/customers`)
- Berhasil menampilkan semua user non-admin beserta relasi orders
- Response mengandung `totalCustomers`, `newCustomers` (bulan ini), `totalOrders`

---

### 11. Admin - Analitik (`tests/admin-analytics.test.ts`)

#### Halaman Analitik (`GET /admin/analytics`)
- Berhasil menampilkan data revenue bulan ini dan bulan lalu
- Response mengandung `growthPercent`, `avgOrderValue`
- Response mengandung `dailyRevenue` (7 hari)
- Response mengandung `topProducts` (5 produk terlaris)
- Response mengandung `statusCounts` (distribusi status order)
- Response mengandung data `categories` beserta `products_count`

---

### 12. Admin - Keuangan (`tests/admin-finance.test.ts`)

#### Halaman Keuangan (`GET /admin/finance`)
- Berhasil menampilkan semua data pembayaran beserta relasi `order.user`
- Response mengandung `totalRevenue`, `pendingPayments`, `paidCount`, `pendingCount`

---

### 13. Admin - Pengaturan (`tests/admin-settings.test.ts`)

#### Lihat Pengaturan (`GET /admin/settings`)
- Berhasil menampilkan halaman pengaturan

#### Update Pengaturan (`POST /admin/settings`)
- Berhasil update `name` dan `email` admin
- Berhasil update `password` jika diisi (dengan konfirmasi)
- Gagal jika `password` diisi tapi `password_confirmation` tidak cocok
- Gagal jika `email` format tidak valid
- Gagal jika field wajib (`name`, `email`) kosong

---

### 14. Middleware & Keamanan (`tests/middleware.test.ts`)

#### Auth Middleware
- Semua route di dalam grup `auth` harus redirect ke login jika belum login
- Route yang ditest: `/profile`, `/orders`, `/cart`, `/admin/dashboard`

#### Ownership Check
- User A tidak bisa melihat detail pesanan milik User B (403)
- User A tidak bisa mengakses halaman pembayaran pesanan milik User B (403)

#### Admin Authorization
- User biasa (customer) mendapat 403 di semua route `/admin/*`
- Uji semua endpoint admin: dashboard, orders, products, categories, customers, analytics, finance, settings

---

### 15. Model Relationships (`tests/model-relations.test.ts`)

#### User
- `User->orders()` mengembalikan hasMany Order
- `User->isAdmin()` mengembalikan `true` jika role = `admin`, `false` jika bukan

#### Category
- `Category->products()` mengembalikan hasMany Product

#### Product
- `Product->category()` mengembalikan belongsTo Category
- `Product->orderItems()` mengembalikan hasMany OrderItem
- Route key menggunakan `slug`

#### Order
- `Order->user()` mengembalikan belongsTo User
- `Order->orderItems()` mengembalikan hasMany OrderItem
- `Order->payment()` mengembalikan hasOne Payment

#### Payment
- `Payment->order()` mengembalikan belongsTo Order
- `paid_at` di-cast ke datetime
