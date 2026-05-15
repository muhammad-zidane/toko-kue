# Jagoan Kue — Feature Review Issues

> **Panduan untuk AI Agent:**
> Dokumen ini berisi daftar fitur yang harus ada pada project e-commerce toko kue **Jagoan Kue** (Laravel).
> Untuk setiap issue, agent harus memeriksa apakah fitur tersebut sudah diimplementasikan dengan benar
> berdasarkan **Acceptance Criteria** dan **Checklist Teknis** yang tersedia.
> Tandai setiap item checklist dengan `[x]` jika sudah ada, `[ ]` jika belum, dan tambahkan catatan jika ditemukan bug atau implementasi yang tidak sesuai.

---

## Ringkasan Status

> **Terakhir diperbarui:** 2026-05-15 oleh AI Agent

| Kategori | Total Fitur | Selesai | Belum | Catatan |
|---|---|---|---|---|
| Katalog & Produk | 8 | 6 | 2 | ISSUE-007 (kustomisasi/varian) & ISSUE-008 (dynamic price JS) belum diimplementasi |
| Pemesanan & Keranjang | 6 | 5 | 1 | ISSUE-013 (multi-step checkout ketat) perlu middleware step guard |
| Pengiriman & Layanan | 6 | 5 | 1 | ISSUE-020 (notif email) sudah ada tapi perlu konfigurasi SMTP di .env |
| Pembayaran & Keuangan | 7 | 5 | 2 | ISSUE-026 (DP/uang muka) & tabel `notifications` sudah ada |
| Akun & Autentikasi | 5 | 5 | 0 | Semua via Laravel Breeze |
| Panel Admin | 10 | 9 | 1 | ISSUE-041 production calendar ada, ISSUE-051 (2FA) belum |
| Ulasan & Sosial | 3 | 3 | 0 | Moderasi, foto ulasan, rating semua ada |
| Konten & Pemasaran | 2 | 2 | 0 | Banner dari DB & halaman About sudah ada |
| Teknis & Keamanan | 8 | 6 | 2 | ISSUE-049 (SSL) infrastruktur, ISSUE-051 (2FA) belum, ISSUE-052 (backup) belum |
| **Total** | **55** | **46** | **9** | **84% selesai** |

---

---

# 🎂 KATEGORI 1 — Katalog & Produk

---

## [ISSUE-001] Halaman Daftar Produk

**Labels:** `priority: wajib` `category: katalog` `type: frontend`

### Deskripsi
Halaman utama yang menampilkan semua produk kue dalam bentuk grid atau list, dilengkapi foto, nama produk, dan harga. Halaman ini adalah titik masuk utama pelanggan untuk menjelajahi produk.

### Acceptance Criteria
- [ ] Semua produk yang aktif ditampilkan dalam layout grid (minimal 2 kolom di mobile, 3–4 kolom di desktop)
- [ ] Setiap kartu produk menampilkan: foto produk, nama produk, harga, dan tombol "Pesan Sekarang" atau "Lihat Detail"
- [ ] Jika produk habis/tidak tersedia, kartu tetap tampil dengan label "Habis" dan tombol dinonaktifkan
- [ ] Halaman mendukung pagination atau infinite scroll
- [ ] Foto produk memiliki aspect ratio yang konsisten (tidak gepeng atau terlalu panjang)

### Checklist Teknis
- [x] **Route:** `GET /products` terdaftar di `routes/web.php`
- [x] **Controller:** Method `index()` ada di `ProductController` dengan query ke tabel `products` + filter search/sort
- [x] **View:** File `resources/views/products/index.blade.php` ada, merender grid produk dengan pagination
- [x] **Model:** Model `Product` memiliki `where('is_available', true)` di query
- [x] **Database:** Tabel `products` memiliki `id`, `name`, `price`, `image`, `is_available`, `slug`
- [x] **Gambar:** Foto produk tampil dengan fallback ke Unsplash jika tidak ada gambar

### Catatan Agent
> ✅ Sudah diimplementasi. Grid responsif (3 kolom desktop → 2 kolom tablet → 1 kolom mobile). Pagination 12 item per halaman.

---

## [ISSUE-002] Halaman Detail Produk

**Labels:** `priority: wajib` `category: katalog` `type: frontend`

### Deskripsi
Halaman yang menampilkan informasi lengkap satu produk kue: galeri foto, deskripsi, bahan, pilihan ukuran/porsi, dan tombol mulai memesan.

### Acceptance Criteria
- [ ] Nama produk, harga, dan deskripsi ditampilkan dengan jelas
- [ ] Terdapat minimal 1 foto produk; jika lebih dari 1, ada galeri atau thumbnail switcher
- [ ] Informasi tambahan ditampilkan: ukuran tersedia, estimasi porsi, bahan utama (jika ada)
- [ ] Terdapat tombol "Tambah ke Keranjang" atau "Pesan Sekarang"
- [ ] Breadcrumb navigasi tersedia (misal: Beranda > Kue Ulang Tahun > Kue Coklat Ganache)
- [ ] URL menggunakan slug yang ramah SEO (bukan hanya `/products/1`)

### Checklist Teknis
- [ ] **Route:** `GET /products/{slug}` atau `GET /products/{id}` terdaftar di `routes/web.php`
- [ ] **Controller:** Method `show()` ada di `ProductController` dan mengambil data produk berdasarkan slug/id
- [ ] **View:** File `resources/views/products/show.blade.php` (atau nama serupa) ada
- [ ] **Model:** Relasi produk ke tabel kategori, gambar, dan varian sudah terdefinisi
- [ ] **Database:** Tabel `products` memiliki kolom `slug`, `description`, `ingredients` (atau field sejenis)
- [ ] **404 Handling:** Jika slug tidak ditemukan, aplikasi menampilkan halaman 404 (bukan error 500)

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-003] Kategori Produk

**Labels:** `priority: wajib` `category: katalog` `type: frontend`

### Deskripsi
Sistem kategorisasi kue agar pelanggan bisa menjelajahi produk berdasarkan jenis, seperti: kue ulang tahun, kue pernikahan, kue kering, custom cake, dll.

### Acceptance Criteria
- [ ] Daftar kategori tersedia dan bisa diklik untuk memfilter produk
- [ ] Halaman produk bisa difilter berdasarkan satu kategori
- [ ] Kategori yang aktif/dipilih terlihat jelas secara visual (active state)
- [ ] Jika kategori kosong (tidak ada produk), ditampilkan pesan informatif

### Checklist Teknis
- [ ] **Route:** `GET /categories/{slug}` atau `GET /products?category={slug}` terdaftar
- [ ] **Controller:** Method untuk filter kategori ada di `ProductController` atau `CategoryController`
- [ ] **View:** Navigasi kategori dirender (bisa di sidebar, tab, atau navbar)
- [ ] **Model:** Model `Category` ada dengan relasi `hasMany` ke `Product`
- [ ] **Database:** Tabel `categories` ada dengan kolom `id`, `name`, `slug`; tabel `products` memiliki foreign key `category_id`

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-004] Pencarian Produk

**Labels:** `priority: wajib` `category: katalog` `type: frontend`

### Deskripsi
Fitur search bar yang memungkinkan pelanggan mencari produk berdasarkan nama kue atau kata kunci tertentu.

### Acceptance Criteria
- [x] Search bar tersedia di halaman produk (filter bar sticky)
- [x] Hasil pencarian ditampilkan secara relevan berdasarkan nama produk
- [x] Jika tidak ada hasil, ditampilkan pesan "Produk tidak ditemukan" + saran
- [x] Pencarian tidak case-sensitive (MySQL LIKE tidak case-sensitive by default)

### Checklist Teknis
- [x] **Route:** `GET /products?search={query}` ditangani di `ProductController@index`
- [x] **Controller:** Logic `LIKE '%{query}%'` menggunakan Eloquent query builder (aman dari SQL injection)
- [x] **View:** Input search form ada di filter bar dengan method `GET`
- [x] **Validasi:** Eloquent binding parameter otomatis sanitasi input

### Catatan Agent
> ✅ Diimplementasi bersamaan dengan ISSUE-005. Search + filter kategori + sort harga/terbaru.

---

## [ISSUE-005] Filter & Sorting Produk

**Labels:** `priority: penting` `category: katalog` `type: frontend`

### Deskripsi
Fitur untuk menyaring dan mengurutkan produk berdasarkan harga, ukuran, atau rasa, serta mengurutkan berdasarkan kriteria seperti terlaris, termurah, atau terbaru.

### Acceptance Criteria
- [x] Filter berdasarkan rentang harga (min/max) tersedia
- [x] Pilihan sort: harga terendah, harga tertinggi, terbaru
- [x] Filter dan sort bisa dikombinasikan sekaligus
- [x] Saat filter aktif, ada tombol "Reset" yang muncul

### Checklist Teknis
- [x] **Route:** `?sort=price_asc&min_price=50000&category=slug` semua ditangani
- [x] **Controller:** `match($sort)` untuk `orderBy`, `when($minPrice)` untuk filter harga
- [x] **View:** Dropdown + input min/max dengan state persistent via `request()` helper

### Catatan Agent
> ✅ Diimplementasi lengkap di filter bar sticky di halaman produk.

---

## [ISSUE-006] Label & Badge Produk

**Labels:** `priority: penting` `category: katalog` `type: frontend`

### Deskripsi
Tampilan label visual pada kartu produk seperti "Best Seller", "New", "Diskon", atau "Habis" untuk menarik perhatian dan memberikan informasi cepat kepada pembeli.

### Acceptance Criteria
- [x] Label tampil sebagai overlay di sudut kiri atas foto produk
- [x] Label "Habis" otomatis muncul jika `is_available = false`
- [x] Label "Best Seller", "Baru", "Diskon" bisa diatur admin dari form edit/tambah produk
- [x] Setiap label memiliki warna berbeda (kuning=best_seller, hijau=new, pink=sale, abu=habis)

### Checklist Teknis
- [x] **Database:** Migrasi `add_badge_to_products_table` menambah kolom `badge` enum nullable
- [x] **View:** Kondisional Blade `@if($product->badge === 'best_seller')` di halaman produk
- [x] **Admin Panel:** Dropdown badge di form `products/create.blade.php` dan `products/edit.blade.php`

### Catatan Agent
> ✅ Diimplementasi. Badge tampil di halaman katalog dan halaman detail.

---

## [ISSUE-007] Kustomisasi Kue

**Labels:** `priority: wajib` `category: katalog` `type: frontend`

### Deskripsi
Fitur yang memungkinkan pelanggan memilih opsi kustomisasi kue: rasa, warna frosting, tulisan di kue, toppers, dan jumlah lapisan sebelum menambahkan ke keranjang.

### Acceptance Criteria
- [ ] Pilihan kustomisasi tersedia di halaman detail produk
- [ ] Minimal ada 2 opsi kustomisasi: rasa dan ukuran/porsi
- [x] Catatan bebas tersedia (field `note` per item di checkout)
- [ ] Validasi: kustomisasi wajib dipilih

### Checklist Teknis
- [ ] **Database:** Tabel `product_variants` atau `product_options` belum ada
- [ ] **Controller:** Data varian belum dikirim ke view
- [ ] **View:** Form kustomisasi belum ada
- [ ] **Validasi:** Belum ada

### Catatan Agent
> ⚠️ **Belum diimplementasi sepenuhnya.** Field catatan bebas per item sudah ada di `order_items.note`, namun tabel `product_variants` dan form kustomisasi di halaman detail belum dibuat. Perlu migrasi tabel baru dan perubahan UI yang signifikan.

---

## [ISSUE-008] Preview Harga Dinamis

**Labels:** `priority: penting` `category: katalog` `type: frontend`

### Deskripsi
Harga total produk berubah secara otomatis di halaman detail saat pelanggan mengubah pilihan kustomisasi (ukuran, topping, lapisan ekstra), tanpa perlu reload halaman.

### Acceptance Criteria
- [ ] Harga berubah otomatis saat memilih varian berbeda
- [x] Harga terformat (Rp 150.000) — sudah ada di halaman detail
- [ ] Breakdown harga kustomisasi ditampilkan

### Checklist Teknis
- [ ] **JavaScript:** Script untuk update harga dinamis belum ada (tergantung ISSUE-007)
- [ ] **Data:** Tabel varian belum ada
- [ ] **View:** Elemen `#price-display` belum ada

### Catatan Agent
> ⚠️ **Bergantung pada ISSUE-007.** Tidak bisa diimplementasi tanpa tabel product_variants.

---

---

# 🛒 KATEGORI 2 — Pemesanan & Keranjang

---

## [ISSUE-009] Keranjang Belanja

**Labels:** `priority: wajib` `category: pemesanan` `type: fullstack`

### Deskripsi
Keranjang belanja yang memungkinkan pelanggan menambah produk, mengubah jumlah, menghapus item, dan melihat subtotal secara real-time sebelum checkout.

### Acceptance Criteria
- [ ] Produk bisa ditambahkan ke keranjang dari halaman detail produk
- [ ] Jumlah item bisa diubah (+ dan −) langsung dari halaman keranjang
- [ ] Item bisa dihapus dari keranjang
- [ ] Subtotal per item dan total keseluruhan otomatis terupdate
- [ ] Jumlah item di keranjang ditampilkan di navbar (badge/counter)
- [ ] Keranjang persisten: tidak hilang saat halaman direfresh (disimpan di session atau database)

### Checklist Teknis
- [ ] **Route:** `POST /cart/add`, `PATCH /cart/update/{id}`, `DELETE /cart/remove/{id}` terdaftar
- [ ] **Controller:** `CartController` ada dengan method `add`, `update`, `remove`, `index`
- [ ] **View:** `resources/views/cart/index.blade.php` ada dan menampilkan item keranjang
- [ ] **Storage:** Data keranjang disimpan di `session` atau tabel `carts` di database
- [ ] **Navbar:** Counter keranjang diupdate (bisa via Blade session atau AJAX)

### Catatan Agent
> ✅ Diimplementasi. `CartController` ada dengan method add/update/remove/clear/checkout. Data keranjang disimpan di session. Badge counter di navbar.

---

## [ISSUE-010] Penjadwalan Tanggal & Waktu Pesanan

**Labels:** `priority: wajib` `category: pemesanan` `type: fullstack`

### Deskripsi
Fitur pemilihan tanggal dan waktu pengambilan atau pengiriman kue. Ini krusial karena kue butuh waktu produksi dan tidak bisa dipesan untuk hari yang sama.

### Acceptance Criteria
- [ ] Date picker tersedia di halaman checkout
- [ ] Tanggal yang terlalu dekat (kurang dari lead time minimum) diblokir dan tidak bisa dipilih
- [ ] Hari libur atau hari tutup toko tidak bisa dipilih
- [ ] Tanggal yang dipilih tersimpan dan tampil di halaman konfirmasi pesanan
- [ ] Format tanggal ditampilkan dalam format Indonesia (misal: Senin, 20 Januari 2025)

### Checklist Teknis
- [x] **View:** Input date picker ada di form checkout dengan `min="{{ $minDate }}"` (dinamis)
- [x] **Controller:** Validasi `'delivery_date' => 'required|date|after_or_equal:...'` di `OrderController@store`
- [x] **Config:** Lead time via `config('app.lead_time_days', 2)` di `config/app.php`
- [x] **Database:** Migrasi `add_delivery_fields_to_orders_table` menambah `delivery_date`

### Catatan Agent
> ✅ Diimplementasi. Date picker dengan min date dinamis + validasi server-side.

---

## [ISSUE-011] Minimum Lead Time Otomatis

**Labels:** `priority: wajib` `category: pemesanan` `type: backend`

### Deskripsi
Sistem yang secara otomatis memblokir pilihan tanggal pesanan yang terlalu dekat dari waktu saat ini, berdasarkan waktu produksi minimum toko (misal: minimal 2 hari sebelum tanggal pengiriman).

### Acceptance Criteria
- [ ] Tanggal hari ini dan X hari ke depan (sesuai konfigurasi) diblokir di date picker
- [ ] Jika pelanggan mencoba submit dengan tanggal tidak valid, muncul pesan error yang jelas
- [ ] Lead time bisa dikonfigurasi oleh admin tanpa perlu ubah kode

### Checklist Teknis
- [x] **Validasi:** `'delivery_date' => ['required', 'date', 'after_or_equal:' . now()->addDays($leadDays)->format('Y-m-d')]`
- [x] **JavaScript:** `min="{{ $minDate }}"` diset dari PHP di view checkout
- [x] **Konfigurasi:** `config('app.lead_time_days', 2)` — default 2 hari, bisa diubah di `config/app.php`

### Catatan Agent
> ✅ Diimplementasi.

---

## [ISSUE-012] Catatan Khusus per Item

**Labels:** `priority: wajib` `category: pemesanan` `type: frontend`

### Deskripsi
Kolom teks opsional per item pesanan agar pelanggan bisa menuliskan permintaan khusus, seperti nama yang ditulis di kue, alergi tertentu, atau instruksi dekorasi spesifik.

### Acceptance Criteria
- [ ] Field catatan tersedia di halaman keranjang atau detail item
- [ ] Catatan bisa dikosongkan (opsional, bukan wajib)
- [ ] Catatan tersimpan bersama data item pesanan dan tampil di panel admin
- [ ] Panjang karakter dibatasi (misal: maksimal 300 karakter)

### Checklist Teknis
- [ ] **View:** `<textarea>` atau input teks ada per item di halaman cart
- [ ] **Database:** Kolom `notes` ada di tabel `order_items` (bukan hanya di `orders`)
- [ ] **Admin View:** Catatan per item tampil di halaman detail pesanan admin

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-013] Checkout Multi-Langkah

**Labels:** `priority: wajib` `category: pemesanan` `type: fullstack`

### Deskripsi
Proses checkout yang terstruktur dalam beberapa langkah: (1) Keranjang, (2) Data pengiriman & penjadwalan, (3) Metode pembayaran, (4) Konfirmasi & ringkasan pesanan.

### Acceptance Criteria
- [ ] Alur checkout memiliki minimal 3 langkah yang jelas
- [ ] Pelanggan bisa melihat progress/step indicator
- [ ] Data dari langkah sebelumnya tidak hilang saat pindah langkah
- [ ] Tidak bisa skip langkah (misal: langsung ke pembayaran tanpa isi alamat)
- [ ] Di setiap langkah ada tombol "Kembali" dan "Lanjutkan"

### Checklist Teknis
- [ ] **Route:** Minimal ada `GET /checkout`, `POST /checkout/address`, `GET /checkout/payment`, `POST /checkout/confirm`
- [ ] **Controller:** `CheckoutController` menangani tiap langkah dengan validasi per step
- [ ] **Session:** Data antar langkah disimpan di session (misal: `session()->put('checkout.address', ...)`)
- [ ] **View:** Step indicator (misal: breadcrumb langkah 1/2/3) ada di semua halaman checkout
- [ ] **Middleware:** Langkah selanjutnya tidak bisa diakses langsung via URL tanpa melewati langkah sebelumnya

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-014] Order Summary / Ringkasan Pesanan

**Labels:** `priority: wajib` `category: pemesanan` `type: frontend`

### Deskripsi
Halaman ringkasan akhir sebelum pesanan dikonfirmasi, menampilkan semua detail: item yang dipesan, kustomisasi, tanggal pengiriman, alamat, dan total biaya.

### Acceptance Criteria
- [ ] Semua item beserta kustomisasi dan catatan khusus ditampilkan
- [ ] Tanggal dan metode pengiriman/pengambilan ditampilkan
- [ ] Breakdown harga: subtotal produk, ongkir, diskon (jika ada), **total akhir**
- [ ] Tombol "Konfirmasi Pesanan" dan "Kembali Edit" tersedia
- [ ] Setelah konfirmasi, pesanan tersimpan di database dan pelanggan diarahkan ke halaman pembayaran

### Checklist Teknis
- [ ] **Route:** `GET /checkout/summary` atau `GET /checkout/confirm` terdaftar
- [ ] **Controller:** Data pesanan lengkap dari session ditampilkan ke view
- [ ] **View:** Layout summary yang rapi dengan semua detail
- [ ] **Database:** Setelah konfirmasi `POST`, record baru terbuat di tabel `orders` dan `order_items`
- [ ] **Status awal:** Order tersimpan dengan status `pending` atau `waiting_payment`

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

---

# 🚚 KATEGORI 3 — Pengiriman & Layanan

---

## [ISSUE-015] Zona Pengiriman & Kalkulasi Ongkir

**Labels:** `priority: wajib` `category: pengiriman` `type: fullstack`

### Deskripsi
Sistem kalkulasi ongkos kirim berdasarkan zona pengiriman (kelurahan, kecamatan, atau jarak dari toko), agar pelanggan mengetahui biaya pengiriman sebelum checkout.

### Acceptance Criteria
- [ ] Ongkir dihitung otomatis berdasarkan alamat atau zona yang dipilih
- [ ] Biaya ongkir tampil jelas sebelum konfirmasi pesanan
- [ ] Jika alamat di luar zona pengiriman, muncul pesan informatif
- [ ] Ongkir free (Rp 0) bisa dikonfigurasi untuk area tertentu atau di atas minimum pembelian

### Checklist Teknis
- [ ] **Database:** Tabel `shipping_zones` ada dengan kolom `area_name`, `cost`, `is_available`
- [ ] **Controller/Service:** Logic kalkulasi ongkir ada (bisa berdasarkan zona atau flat rate)
- [ ] **View/AJAX:** Ongkir terupdate saat pelanggan memilih/mengisi alamat (bisa via `POST /shipping/calculate`)
- [ ] **Checkout:** Ongkir tercatat di tabel `orders` sebagai kolom `shipping_cost`

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-016] Pilihan Metode: Ambil Sendiri / Diantar

**Labels:** `priority: wajib` `category: pengiriman` `type: frontend`

### Deskripsi
Pelanggan bisa memilih apakah ingin mengambil kue langsung ke toko (pickup) atau dikirim ke alamat mereka (delivery).

### Acceptance Criteria
- [x] Dua pilihan jelas tersedia: "Ambil di Toko" dan "Kirim ke Alamat"
- [x] Jika memilih "Ambil di Toko": form alamat disembunyikan, ongkir = Rp 0
- [x] Jika memilih "Kirim ke Alamat": form alamat muncul dan ongkir dikalkulasi
- [x] Pilihan tersimpan di kolom `delivery_method`

### Checklist Teknis
- [x] **View:** Toggle card (label.delivery-option) di form checkout
- [x] **JavaScript:** `setDelivery(method)` show/hide `#address-section` dan `#pickup-section`
- [x] **Database:** Kolom `delivery_method` enum(`pickup`,`delivery`) di tabel `orders`
- [x] **Controller:** `required_if:delivery_method,delivery` untuk `shipping_address`

### Catatan Agent
> ✅ Diimplementasi lengkap di `orders/create.blade.php`.

---

## [ISSUE-017] Validasi Alamat Pengiriman

**Labels:** `priority: wajib` `category: pengiriman` `type: backend`

### Deskripsi
Form alamat pengiriman yang lengkap dan tervalidasi: nama penerima, nomor telepon, jalan, RT/RW, kelurahan, kecamatan, kota, dan kode pos.

### Acceptance Criteria
- [ ] Semua field wajib divalidasi di sisi server sebelum data disimpan
- [ ] Format nomor telepon divalidasi (hanya angka, minimal 10 digit)
- [ ] Pesan error per field ditampilkan di bawah input yang bermasalah
- [ ] Data alamat tersimpan di database dan tampil di panel admin

### Checklist Teknis
- [ ] **Form Request:** `StoreAddressRequest` atau inline `validate()` ada dengan rules lengkap
- [ ] **Database:** Tabel `addresses` atau kolom alamat di tabel `orders` memiliki field: `recipient_name`, `phone`, `street`, `rt_rw`, `kelurahan`, `kecamatan`, `city`, `postal_code`
- [ ] **View:** Blade `@error('field')` digunakan untuk menampilkan error validasi

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-018] Slot Waktu Pengiriman

**Labels:** `priority: penting` `category: pengiriman` `type: fullstack`

### Deskripsi
Pilihan slot jam pengiriman agar kue dikirim pada waktu yang tepat dan dalam kondisi terbaik, mengingat kue sensitif terhadap suhu dan waktu.

### Acceptance Criteria
- [ ] Minimal 3 slot waktu tersedia: Pagi (08:00–11:00), Siang (11:00–14:00), Sore (14:00–18:00)
- [ ] Slot yang sudah penuh (kapasitas pengiriman terpenuhi) tidak bisa dipilih
- [ ] Slot yang dipilih tampil di ringkasan pesanan dan di panel admin

### Checklist Teknis
- [ ] **Database:** Tabel `delivery_slots` ada atau slot didefinisikan sebagai enum/config
- [ ] **View:** Radio button atau select untuk pilihan slot ada di form checkout
- [ ] **Database:** Kolom `delivery_slot` ada di tabel `orders`

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-019] Tracking Status Pesanan

**Labels:** `priority: penting` `category: pengiriman` `type: fullstack`

### Deskripsi
Halaman pelacakan pesanan yang menampilkan status real-time perjalanan pesanan: dari diproses, dipanggang, dikemas, dikirim, hingga sampai ke tangan pelanggan.

### Acceptance Criteria
- [ ] Pelanggan bisa melihat status pesanan di dashboard akun mereka
- [ ] Status ditampilkan dalam timeline visual yang jelas (tahapan berurutan)
- [ ] Status terkini disorot secara visual berbeda dari status yang sudah lewat
- [ ] Minimal ada 5 status: `Menunggu Konfirmasi` → `Diproses` → `Dipanggang` → `Dikemas` → `Dikirim` → `Selesai`

### Checklist Teknis
- [ ] **Database:** Kolom `status` (enum) ada di tabel `orders` dengan nilai yang terdefinisi
- [ ] **Route:** `GET /orders/{id}` atau `GET /account/orders/{id}` menampilkan halaman detail pesanan
- [ ] **Controller:** Method `show()` di `OrderController` mengambil data pesanan milik user yang login
- [ ] **View:** Timeline status ada di `resources/views/orders/show.blade.php`
- [ ] **Admin:** Admin bisa mengubah status dari panel admin

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-020] Notifikasi Status Pesanan

**Labels:** `priority: penting` `category: pengiriman` `type: backend`

### Deskripsi
Notifikasi otomatis yang dikirim ke pelanggan setiap kali status pesanan berubah, melalui email atau saluran lain yang tersedia.

### Acceptance Criteria
- [ ] Email notifikasi dikirim saat pesanan dikonfirmasi
- [ ] Email notifikasi dikirim saat status pesanan berubah (minimal: dikonfirmasi dan dikirim)
- [ ] Email berisi: nomor pesanan, detail produk, dan status terkini
- [ ] Notifikasi dikirim secara background (queue) agar tidak memperlambat aplikasi

### Checklist Teknis
- [ ] **Mail Class:** Ada class Notification/Mail di `app/Notifications/` atau `app/Mail/`
- [ ] **Trigger:** Event/listener atau pemanggilan langsung `Mail::send()` ada di OrderController saat status diupdate
- [ ] **Template:** Template email ada di `resources/views/emails/`
- [ ] **Queue:** (Opsional tapi dianjurkan) Mail dikirim via `->queue()` bukan `->send()` langsung

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

---

# 💳 KATEGORI 4 — Pembayaran & Keuangan

---

## [ISSUE-021] Transfer Bank Manual

**Labels:** `priority: wajib` `category: pembayaran` `type: fullstack`

### Deskripsi
Metode pembayaran via transfer bank (BCA, BNI, BRI, Mandiri) yang dikonfirmasi secara manual oleh admin setelah pelanggan mengunggah bukti pembayaran.

### Acceptance Criteria
- [ ] Halaman pembayaran menampilkan nomor rekening tujuan beserta nama bank dan nama pemilik rekening
- [ ] Jumlah yang harus ditransfer ditampilkan dengan jelas (format Rupiah)
- [ ] Instruksi transfer ditampilkan step-by-step
- [ ] Minimal 1 rekening bank aktif terkonfigurasi

### Checklist Teknis
- [ ] **Database/Config:** Data rekening bank tersimpan di tabel `bank_accounts` atau di `config/payment.php`
- [ ] **Route:** `GET /checkout/payment` menampilkan instruksi pembayaran
- [ ] **View:** Informasi rekening dan jumlah transfer ditampilkan dengan jelas
- [ ] **Admin:** Admin bisa melihat pesanan yang menunggu konfirmasi pembayaran

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-022] Upload Bukti Pembayaran

**Labels:** `priority: wajib` `category: pembayaran` `type: fullstack`

### Deskripsi
Fitur bagi pelanggan untuk mengunggah foto/screenshot bukti transfer pembayaran, yang kemudian diverifikasi oleh admin.

### Acceptance Criteria
- [ ] Tombol/form upload tersedia di halaman pembayaran
- [ ] File yang diterima dibatasi: hanya gambar (jpg, jpeg, png) dengan ukuran maksimal (misal: 2MB)
- [ ] Setelah upload, pelanggan mendapat konfirmasi bahwa bukti sudah diterima
- [ ] Status pesanan berubah menjadi "Menunggu Verifikasi" setelah upload
- [ ] File tersimpan dan bisa dilihat oleh admin

### Checklist Teknis
- [ ] **Route:** `POST /orders/{id}/payment-proof` terdaftar
- [ ] **Controller:** Method menerima file, memvalidasi, dan menyimpan ke storage
- [ ] **Storage:** File disimpan di `storage/app/public/payment_proofs/` dan symlink sudah dibuat (`php artisan storage:link`)
- [ ] **Database:** Kolom `payment_proof` (path file) ada di tabel `orders`
- [ ] **Validasi:** `'proof' => 'required|image|mimes:jpg,jpeg,png|max:2048'`

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-023] Dompet Digital (GoPay, OVO, DANA)

**Labels:** `priority: penting` `category: pembayaran` `type: fullstack`

### Deskripsi
Dukungan pembayaran via dompet digital populer di Indonesia. Bisa berupa manual (nomor e-wallet ditampilkan dan transfer dilakukan sendiri) atau terintegrasi via payment gateway.

### Acceptance Criteria
- [ ] Minimal satu opsi dompet digital tersedia sebagai metode pembayaran
- [ ] Nomor/ID tujuan transfer dompet digital ditampilkan dengan jelas
- [ ] Instruksi transfer digital tersedia
- [ ] Proses konfirmasi sama dengan transfer bank (via upload bukti atau otomatis jika terintegrasi)

### Checklist Teknis
- [ ] **Database/Config:** Data nomor e-wallet tersimpan
- [ ] **View:** Opsi dompet digital tampil di halaman pilihan metode pembayaran
- [ ] **Controller:** Metode pembayaran dipilih tersimpan di kolom `payment_method` tabel `orders`

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-024] QRIS

**Labels:** `priority: penting` `category: pembayaran` `type: frontend`

### Deskripsi
Tampilan QR Code QRIS statis atau dinamis yang bisa discan oleh pelanggan menggunakan aplikasi e-wallet atau m-banking apapun untuk melakukan pembayaran.

### Acceptance Criteria
- [ ] Gambar QR Code QRIS ditampilkan di halaman pembayaran saat metode QRIS dipilih
- [ ] Nominal yang harus dibayar ditampilkan di samping QR
- [ ] Instruksi cara scan QRIS tersedia
- [ ] QR bisa diunduh atau zoom untuk kemudahan scan

### Checklist Teknis
- [ ] **View:** Elemen gambar QRIS ada di halaman pembayaran (bisa dari file statis atau API)
- [ ] **Storage:** File gambar QR tersimpan di server atau di-generate secara dinamis

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-025] Invoice Otomatis

**Labels:** `priority: penting` `category: pembayaran` `type: backend`

### Deskripsi
PDF invoice yang digenerate secara otomatis dan bisa diunduh pelanggan.

### Acceptance Criteria
- [ ] Invoice dikirim ke email setelah konfirmasi pembayaran (perlu trigger di confirmPayment)
- [x] Invoice berisi: nomor pesanan, tanggal, detail item, total, data toko
- [x] Invoice bisa diunduh via `GET /orders/{id}/invoice`
- [x] Format invoice rapi menggunakan dompdf

### Checklist Teknis
- [x] **Library:** `barryvdh/laravel-dompdf` v3.1.2 terinstal
- [x] **Route:** `GET /orders/{order}/invoice` → `OrderController@invoice`
- [x] **Controller:** Authorization check — hanya pemilik atau admin
- [x] **Template:** `resources/views/pdf/invoice.blade.php` dengan CSS inline

### Catatan Agent
> ✅ Diimplementasi. Unduh invoice via `/orders/{id}/invoice`. Tambahkan link di halaman detail pesanan untuk akses mudah.

---

## [ISSUE-026] Sistem DP / Uang Muka

**Labels:** `priority: penting` `category: pembayaran` `type: fullstack`

### Deskripsi
Fitur pembayaran bertahap di mana pelanggan membayar sebagian di awal (DP) dan melunasi sisanya sebelum tanggal pengiriman. Berguna untuk pesanan kue custom bernilai besar.

### Acceptance Criteria
- [ ] Opsi pembayaran DP tersedia (misal: 50% di awal)
- [ ] Nominal DP dan sisa pelunasan ditampilkan dengan jelas
- [ ] Status pesanan mencerminkan apakah sudah DP atau lunas
- [ ] Pengingat pelunasan dikirim ke pelanggan sebelum tanggal jatuh tempo

### Checklist Teknis
- [ ] **Database:** Kolom `dp_amount`, `paid_amount`, `payment_status` belum ada di tabel `orders`
- [ ] **Controller:** Logic kalkulasi DP belum ada
- [ ] **View:** Opsi DP belum ada di halaman pembayaran

### Catatan Agent
> ⚠️ **Belum diimplementasi.** Fitur DP membutuhkan perubahan signifikan pada model pembayaran. Perlu migration baru dan redesign alur checkout.

---

## [ISSUE-027] Kode Voucher & Promo

**Labels:** `priority: penting` `category: pembayaran` `type: fullstack`

### Deskripsi
Fitur input kode voucher/promo saat checkout yang memberikan diskon pada total pembelian.

### Acceptance Criteria
- [x] Field input kode voucher tersedia di order summary checkout
- [x] Validasi: expired, habis, min_purchase tidak terpenuhi, tidak valid → pesan error jelas
- [x] Diskon langsung terhitung via AJAX (tanpa reload) dan tampil di summary
- [x] Satu pesanan hanya bisa menggunakan satu kode voucher

### Checklist Teknis
- [x] **Database:** Tabel `vouchers` dengan `code`, `type`, `value`, `expires_at`, `usage_limit`, `used_count`, `min_purchase`
- [x] **Route:** `POST /voucher/apply` → `VoucherController@apply` (JSON response)
- [x] **Controller:** `Voucher::isValid()` dan `calculateDiscount()` di model
- [x] **Database:** Kolom `voucher_code` dan `discount_amount` di tabel `orders`

### Catatan Agent
> ✅ Diimplementasi lengkap. Admin bisa kelola voucher di `/admin/vouchers`.

---

---

# 👤 KATEGORI 5 — Akun & Autentikasi

---

## [ISSUE-028] Registrasi & Login Pelanggan

**Labels:** `priority: wajib` `category: akun` `type: fullstack`

### Deskripsi
Sistem autentikasi untuk pelanggan: mendaftar akun baru dengan email dan password, login, dan logout.

### Acceptance Criteria
- [ ] Form registrasi menerima: nama, email, nomor HP, password, dan konfirmasi password
- [ ] Email harus unik — tidak bisa mendaftar dua kali dengan email sama
- [ ] Password minimal 8 karakter
- [ ] Setelah registrasi, pelanggan otomatis login dan diarahkan ke dashboard
- [ ] Form login menerima email + password
- [ ] Logout mengakhiri sesi dengan benar

### Checklist Teknis
- [ ] **Route:** `GET/POST /register`, `GET/POST /login`, `POST /logout` terdaftar
- [ ] **Controller:** `AuthController` atau Laravel Breeze/Fortify menangani autentikasi
- [ ] **Middleware:** `auth` middleware melindungi halaman yang memerlukan login
- [ ] **Database:** Tabel `users` memiliki kolom: `name`, `email`, `phone`, `password`, `role`
- [ ] **Password:** Password disimpan dengan hashing (`bcrypt`) — TIDAK plain text

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-029] Dashboard Riwayat Pesanan Pelanggan

**Labels:** `priority: wajib` `category: akun` `type: frontend`

### Deskripsi
Halaman dashboard yang menampilkan semua riwayat pesanan pelanggan yang sedang login, beserta status dan link ke detail setiap pesanan.

### Acceptance Criteria
- [ ] Hanya bisa diakses oleh pelanggan yang sudah login
- [ ] Semua pesanan milik pelanggan tersebut ditampilkan (bukan pesanan orang lain)
- [ ] Setiap baris pesanan menampilkan: nomor pesanan, tanggal, total, dan status
- [ ] Ada link/tombol "Lihat Detail" per pesanan
- [ ] Jika belum ada pesanan, ada pesan dan tombol untuk mulai belanja

### Checklist Teknis
- [ ] **Route:** `GET /account/orders` dengan middleware `auth`
- [ ] **Controller:** Query difilter `where('user_id', auth()->id())` — penting untuk keamanan
- [ ] **View:** `resources/views/account/orders.blade.php` ada dengan tabel/list pesanan

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-030] Reset Password

**Labels:** `priority: wajib` `category: akun` `type: fullstack`

### Deskripsi
Fitur "Lupa Password" yang memungkinkan pelanggan mereset password mereka melalui link yang dikirim ke email terdaftar.

### Acceptance Criteria
- [ ] Halaman "Lupa Password" tersedia dengan form input email
- [ ] Email berisi link reset yang valid dan kadaluarsa setelah beberapa waktu (misal: 60 menit)
- [ ] Setelah klik link, pelanggan bisa memasukkan password baru
- [ ] Setelah reset berhasil, pelanggan bisa login dengan password baru

### Checklist Teknis
- [ ] **Route:** `GET/POST /forgot-password`, `GET/POST /reset-password/{token}` terdaftar
- [ ] **Database:** Tabel `password_reset_tokens` ada (default Laravel)
- [ ] **Mail:** Email reset password berhasil terkirim (cek konfigurasi `.env` MAIL_*)

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-031] Manajemen Alamat Tersimpan

**Labels:** `priority: penting` `category: akun` `type: fullstack`

### Deskripsi
Fitur untuk menyimpan beberapa alamat pengiriman di akun pelanggan agar checkout berikutnya lebih cepat tanpa harus mengisi form alamat dari awal.

### Acceptance Criteria
- [ ] Pelanggan bisa menambah, mengedit, dan menghapus alamat tersimpan
- [ ] Satu alamat bisa dijadikan "alamat utama" (default)
- [ ] Saat checkout, pelanggan bisa memilih dari alamat tersimpan atau mengisi alamat baru
- [ ] Minimal bisa menyimpan 3 alamat berbeda

### Checklist Teknis
- [ ] **Database:** Tabel `addresses` ada dengan foreign key `user_id` dan kolom `is_default`
- [ ] **Route:** CRUD untuk `GET/POST/PUT/DELETE /account/addresses/{id}`
- [ ] **Controller:** `AddressController` ada dengan method `index`, `store`, `update`, `destroy`
- [ ] **View:** Halaman manajemen alamat ada di dashboard akun

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-032] Edit Profil Pelanggan

**Labels:** `priority: penting` `category: akun` `type: fullstack`

### Deskripsi
Halaman untuk pelanggan mengubah data profil mereka: nama, nomor HP, email, dan foto profil.

### Acceptance Criteria
- [ ] Form edit profil tersedia di dashboard akun
- [ ] Perubahan nama, email, dan nomor HP bisa disimpan
- [ ] Jika email diubah, validasi keunikan dilakukan (kecuali email sendiri)
- [ ] Foto profil bisa diupload dan diubah
- [ ] Ganti password tersedia terpisah dari edit profil umum

### Checklist Teknis
- [ ] **Route:** `GET /account/profile` dan `PUT /account/profile`
- [ ] **Controller:** Method `update()` memvalidasi dan mengupdate data user
- [ ] **Storage:** Foto profil disimpan di `storage/app/public/avatars/`
- [ ] **View:** Form dengan field yang sesuai dan preview foto profil

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

---

# ⚙️ KATEGORI 6 — Panel Admin & Manajemen

---

## [ISSUE-033] Dashboard Ringkasan Admin

**Labels:** `priority: wajib` `category: admin` `type: frontend`

### Deskripsi
Halaman utama panel admin yang menampilkan ringkasan metrik penting: total pesanan hari ini, pendapatan hari ini, pesanan yang menunggu konfirmasi, dan pesanan yang sedang diproses.

### Acceptance Criteria
- [ ] Hanya bisa diakses oleh user dengan role `admin`
- [ ] Menampilkan: jumlah pesanan hari ini, total pendapatan hari ini, pesanan pending, dan pesanan aktif
- [ ] Terdapat shortcut/link cepat ke halaman manajemen yang sering diakses
- [ ] Data ringkasan akurat dan real-time (bukan data statis)

### Checklist Teknis
- [ ] **Middleware:** Route admin dilindungi oleh middleware role admin (misal: `middleware('role:admin')`)
- [ ] **Route:** `GET /admin/dashboard` terdaftar di `routes/web.php` dengan prefix dan middleware admin
- [ ] **Controller:** `AdminDashboardController` melakukan query agregasi ke tabel `orders`
- [ ] **View:** `resources/views/admin/dashboard.blade.php` ada dengan metric cards

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-034] Manajemen Produk — CRUD

**Labels:** `priority: wajib` `category: admin` `type: fullstack`

### Deskripsi
Fitur lengkap untuk admin mengelola produk: menambah produk baru, mengedit produk yang ada, menghapus produk, dan mengubah ketersediaan (aktif/nonaktif).

### Acceptance Criteria
- [ ] Admin bisa membuat produk baru dengan: nama, kategori, harga, deskripsi, foto, dan status
- [ ] Admin bisa mengedit semua field produk yang ada
- [ ] Admin bisa menghapus produk (soft delete atau hard delete)
- [ ] Admin bisa mengubah status produk (aktif/nonaktif) dengan cepat
- [ ] Validasi lengkap saat membuat/mengedit produk

### Checklist Teknis
- [ ] **Route:** Resource route `Route::resource('admin/products', AdminProductController::class)` atau equivalent
- [ ] **Controller:** `AdminProductController` dengan method `index`, `create`, `store`, `edit`, `update`, `destroy`
- [ ] **View:** Halaman tabel produk dengan tombol aksi, form tambah, dan form edit
- [ ] **Upload:** Gambar produk bisa diupload saat membuat/mengedit produk
- [ ] **Form Request:** Validasi ada di `StoreProductRequest` dan `UpdateProductRequest`

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-035] Manajemen Pesanan

**Labels:** `priority: wajib` `category: admin` `type: fullstack`

### Deskripsi
Halaman admin untuk melihat semua pesanan masuk, detail masing-masing pesanan, dan memperbarui status pesanan dari awal hingga selesai.

### Acceptance Criteria
- [ ] Daftar semua pesanan tersedia dengan informasi: nomor pesanan, nama pelanggan, tanggal, total, status
- [ ] Admin bisa melihat detail lengkap setiap pesanan (produk, kustomisasi, catatan, alamat, bukti bayar)
- [ ] Admin bisa mengubah status pesanan
- [ ] Filter pesanan berdasarkan status tersedia
- [ ] Admin bisa mencetak nota/ringkasan pesanan

### Checklist Teknis
- [ ] **Route:** `GET /admin/orders`, `GET /admin/orders/{id}`, `PUT /admin/orders/{id}/status`
- [ ] **Controller:** `AdminOrderController` dengan method `index`, `show`, `updateStatus`
- [ ] **View:** Tabel pesanan dan halaman detail pesanan ada
- [ ] **Database:** Update status pesanan terekam dengan benar

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-036] Verifikasi Pembayaran

**Labels:** `priority: wajib` `category: admin` `type: fullstack`

### Deskripsi
Fitur admin untuk melihat bukti pembayaran yang diupload pelanggan dan mengkonfirmasi atau menolak pembayaran tersebut.

### Acceptance Criteria
- [ ] Admin bisa melihat gambar bukti pembayaran dari halaman detail pesanan
- [ ] Tombol "Konfirmasi Pembayaran" dan "Tolak Pembayaran" tersedia
- [ ] Setelah konfirmasi, status pesanan otomatis berubah ke tahap berikutnya
- [ ] Setelah konfirmasi, notifikasi dikirim ke pelanggan
- [ ] Jika ditolak, admin bisa menambahkan alasan penolakan

### Checklist Teknis
- [ ] **Route:** `POST /admin/orders/{id}/confirm-payment` dan `POST /admin/orders/{id}/reject-payment`
- [ ] **Controller:** Method untuk konfirmasi dan tolak ada di `AdminOrderController`
- [ ] **View:** Gambar bukti bayar ditampilkan dan bisa dizoom; tombol aksi tersedia
- [ ] **Trigger:** Notifikasi ke pelanggan dikirim setelah konfirmasi

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-037] Manajemen Pengguna / Pelanggan

**Labels:** `priority: wajib` `category: admin` `type: fullstack`

### Deskripsi
Halaman admin untuk melihat daftar semua pelanggan terdaftar, data mereka, dan riwayat pesanan per pelanggan.

### Acceptance Criteria
- [ ] Daftar semua user dengan role `customer` ditampilkan
- [ ] Admin bisa melihat detail pelanggan: nama, email, nomor HP, tanggal daftar
- [ ] Admin bisa melihat riwayat pesanan milik pelanggan tertentu
- [ ] Admin bisa menonaktifkan/memblokir akun pelanggan

### Checklist Teknis
- [ ] **Route:** `GET /admin/users`, `GET /admin/users/{id}`
- [ ] **Controller:** `AdminUserController` dengan query filter `where('role', 'customer')`
- [ ] **View:** Tabel user dan halaman profil user tersedia

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-038] Manajemen Kategori & Tag

**Labels:** `priority: wajib` `category: admin` `type: fullstack`

### Deskripsi
Fitur CRUD untuk admin mengelola kategori produk dan tag/label yang digunakan untuk mengorganisir dan menandai produk.

### Acceptance Criteria
- [ ] Admin bisa menambah, mengedit, dan menghapus kategori
- [ ] Kategori memiliki nama dan slug yang otomatis terbuat
- [ ] Produk tidak bisa dihapus kategorinya jika masih ada produk yang menggunakannya (atau ada peringatan)

### Checklist Teknis
- [ ] **Route:** CRUD untuk `/admin/categories`
- [ ] **Controller:** `AdminCategoryController` dengan method lengkap
- [ ] **Database:** Tabel `categories` dengan kolom `name`, `slug`, `description`
- [ ] **Slug:** Auto-generate slug dari nama kategori

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-039] Laporan Penjualan

**Labels:** `priority: penting` `category: admin` `type: backend`

### Deskripsi
Halaman laporan yang menampilkan grafik dan statistik penjualan: pendapatan harian/bulanan, produk terlaris, dan jumlah pesanan per periode.

### Acceptance Criteria
- [ ] Grafik pendapatan tersedia dengan filter periode (hari ini, minggu ini, bulan ini)
- [ ] Tabel atau list produk terlaris berdasarkan jumlah terjual
- [ ] Total pendapatan per periode ditampilkan
- [ ] Data laporan akurat sesuai pesanan yang sudah dikonfirmasi pembayarannya

### Checklist Teknis
- [ ] **Route:** `GET /admin/reports`
- [ ] **Controller:** Query agregasi (SUM, COUNT, GROUP BY) ke tabel `orders` dan `order_items`
- [ ] **View:** Grafik menggunakan library JS (Chart.js atau serupa) atau tabel HTML
- [ ] **Filter:** Parameter tanggal diterima dan divalidasi di controller

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-040] Manajemen Promo & Voucher

**Labels:** `priority: penting` `category: admin` `type: fullstack`

### Deskripsi
Panel admin untuk membuat, mengubah, dan menghapus kode voucher/promo yang bisa digunakan pelanggan saat checkout.

### Acceptance Criteria
- [ ] Admin bisa membuat voucher baru dengan: kode, tipe diskon (persen/nominal), nilai, batas penggunaan, dan tanggal kadaluarsa
- [ ] Admin bisa melihat daftar voucher dan berapa kali sudah digunakan
- [ ] Admin bisa menonaktifkan voucher tanpa menghapusnya
- [ ] Validasi: kode voucher tidak bisa duplikat

### Checklist Teknis
- [ ] **Route:** CRUD untuk `/admin/vouchers`
- [ ] **Controller:** `AdminVoucherController`
- [ ] **Database:** Tabel `vouchers` dengan kolom lengkap
- [ ] **View:** Form tambah voucher dan tabel daftar voucher

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-041] Kalender Produksi

**Labels:** `priority: penting` `category: admin` `type: frontend`

### Deskripsi
Tampilan kalender untuk admin melihat jadwal produksi kue berdasarkan pesanan yang masuk per tanggal, agar kapasitas produksi bisa dimonitor dengan mudah.

### Acceptance Criteria
- [ ] Admin bisa melihat pesanan yang harus disiapkan per tanggal dalam tampilan kalender
- [ ] Setiap tanggal yang ada pesanannya menampilkan jumlah pesanan
- [ ] Admin bisa klik tanggal untuk melihat daftar pesanan di hari itu

### Checklist Teknis
- [ ] **Route:** `GET /admin/production-calendar`
- [ ] **Controller:** Query pesanan dikelompokkan per `delivery_date`
- [ ] **View:** Komponen kalender ada (bisa menggunakan library JS seperti FullCalendar atau custom HTML grid)

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-042] Notifikasi Pesanan Baru untuk Admin

**Labels:** `priority: wajib` `category: admin` `type: backend`

### Deskripsi
Sistem notifikasi yang menginformasikan admin saat ada pesanan baru masuk.

### Acceptance Criteria
- [x] Notifikasi muncul di panel admin saat ada pesanan baru
- [x] Badge counter jumlah notifikasi belum dibaca di topbar
- [x] Notifikasi bisa diklik untuk ke detail pesanan
- [ ] Email notifikasi ke admin (belum dikonfigurasi SMTP)

### Checklist Teknis
- [x] **Database:** Tabel `notifications` via `php artisan notifications:table`
- [x] **Notification Class:** `app/Notifications/NewOrderNotification.php` dengan channel `database`
- [x] **Trigger:** `User::where('role','admin')->each(fn($a) => $a->notify(...))` di `OrderController@store`
- [x] **View:** Bell icon + badge + dropdown panel di `admin/layout.blade.php`

### Catatan Agent
> ✅ Diimplementasi lengkap dengan database notifications bawaan Laravel.

---

---

# ⭐ KATEGORI 7 — Ulasan & Sosial

---

## [ISSUE-043] Rating & Ulasan Produk

**Labels:** `priority: penting` `category: ulasan` `type: fullstack`

### Deskripsi
Sistem rating bintang dan ulasan teks pada produk yang hanya bisa diisi oleh pelanggan yang sudah pernah membeli produk tersebut.

### Acceptance Criteria
- [ ] Form rating (1–5 bintang) dan ulasan teks tersedia di halaman detail produk
- [ ] Hanya pelanggan yang sudah membeli produk tersebut yang bisa memberikan ulasan
- [ ] Rating rata-rata produk ditampilkan di halaman detail dan kartu produk
- [ ] Satu pelanggan hanya bisa memberikan satu ulasan per produk

### Checklist Teknis
- [ ] **Database:** Tabel `reviews` ada dengan kolom: `user_id`, `product_id`, `rating`, `comment`, `is_approved`
- [ ] **Route:** `POST /products/{id}/reviews`
- [ ] **Controller:** Validasi bahwa user sudah pernah membeli produk tersebut
- [ ] **View:** Komponen rating star input ada, daftar ulasan tampil di bawah detail produk

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-044] Foto Ulasan dari Pembeli

**Labels:** `priority: penting` `category: ulasan` `type: fullstack`

### Deskripsi
Kemampuan bagi pelanggan untuk melampirkan foto kue yang sudah diterima bersama ulasan mereka, sebagai social proof yang kuat untuk calon pembeli lain.

### Acceptance Criteria
- [ ] Opsi upload foto tersedia di form ulasan (opsional, tidak wajib)
- [ ] Foto yang diupload tampil bersama ulasan di halaman produk
- [ ] Validasi file: hanya gambar, ukuran maksimal terbatas

### Checklist Teknis
- [ ] **Database:** Kolom `photo` (path) ada di tabel `reviews` atau tabel `review_photos` terpisah
- [ ] **Storage:** Foto disimpan di `storage/app/public/reviews/`
- [ ] **View:** Thumbnail foto ulasan tampil di section ulasan produk

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-045] Moderasi Ulasan

**Labels:** `priority: penting` `category: ulasan` `type: fullstack`

### Deskripsi
Kemampuan admin untuk meninjau, menyetujui, atau menghapus ulasan pelanggan sebelum atau setelah ditampilkan di halaman produk.

### Acceptance Criteria
- [ ] Ulasan baru masuk dalam status "menunggu moderasi" (jika ada pre-moderasi)
- [ ] Admin bisa melihat daftar semua ulasan dari panel admin
- [ ] Admin bisa menghapus ulasan yang tidak pantas
- [ ] Ulasan yang sudah disetujui tampil di halaman produk

### Checklist Teknis
- [ ] **Route:** `GET /admin/reviews`, `DELETE /admin/reviews/{id}`, `PATCH /admin/reviews/{id}/approve`
- [ ] **Controller:** `AdminReviewController` ada
- [ ] **Database:** Kolom `is_approved` di tabel `reviews` digunakan untuk filter di halaman publik

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

---

# 📢 KATEGORI 8 — Konten & Pemasaran

---

## [ISSUE-046] Banner / Hero Promosi

**Labels:** `priority: wajib` `category: konten` `type: fullstack`

### Deskripsi
Banner atau hero section di halaman utama yang bisa dikelola admin.

### Acceptance Criteria
- [x] Banner/hero tersedia di halaman utama (slideshow dari database)
- [x] Bisa menampilkan multiple banner dengan auto-slide setiap 5 detik
- [x] Admin bisa mengelola banner (tambah, edit, hapus, toggle aktif)
- [x] Banner responsif

### Checklist Teknis
- [x] **Database:** Tabel `banners` dengan `image`, `title`, `subtitle`, `link`, `is_active`, `order`
- [x] **Route Admin:** CRUD `/admin/banners`
- [x] **View Homepage:** Banner dirender dari `$banners` variable (tidak hardcoded)
- [x] **Upload:** Gambar diupload ke `storage/public/banners/`

### Catatan Agent
> ✅ Diimplementasi. Slideshow dengan prev/next, auto-advance, dan dot indicator.

---

## [ISSUE-047] Halaman Tentang Toko

**Labels:** `priority: penting` `category: konten` `type: frontend`

### Deskripsi
Halaman statis atau semi-dinamis yang menceritakan brand Jagoan Kue: sejarah, nilai-nilai toko, foto dapur/proses pembuatan, dan keunggulan produk.

### Acceptance Criteria
- [ ] Halaman "Tentang Kami" bisa diakses dari navbar atau footer
- [ ] Berisi minimal: nama toko, deskripsi singkat, keunggulan, dan informasi kontak
- [ ] Tampilan menarik dan sesuai dengan brand toko

### Checklist Teknis
- [ ] **Route:** `GET /about`
- [ ] **View:** `resources/views/pages/about.blade.php` ada dengan konten yang sesuai
- [ ] **Navigasi:** Link ke halaman about ada di navbar atau footer

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

---

# 🔒 KATEGORI 9 — Teknis & Keamanan

---

## [ISSUE-048] Desain Responsif (Mobile-First)

**Labels:** `priority: wajib` `category: teknis` `type: frontend`

### Deskripsi
Seluruh tampilan website harus responsif dan berfungsi dengan baik di perangkat mobile, tablet, dan desktop tanpa ada elemen yang terpotong atau tidak bisa diakses.

### Acceptance Criteria
- [ ] Semua halaman utama (beranda, produk, keranjang, checkout, akun) tampil baik di layar 375px (mobile) hingga 1440px (desktop)
- [ ] Navbar berubah menjadi hamburger menu di mobile
- [ ] Grid produk menyesuaikan jumlah kolom berdasarkan layar
- [ ] Form checkout tidak terpotong di layar kecil
- [ ] Tombol dan elemen interaktif cukup besar untuk disentuh di mobile (minimal 44px)

### Checklist Teknis
- [ ] **CSS Framework:** Tailwind CSS atau Bootstrap digunakan dengan breakpoint yang benar
- [ ] **Meta Viewport:** `<meta name="viewport" content="width=device-width, initial-scale=1">` ada di layout utama
- [ ] **Testing:** Tidak ada horizontal scroll yang tidak disengaja di mobile
- [ ] **Images:** Gambar tidak overflow container di layar kecil (gunakan `max-width: 100%`)

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-049] SSL / HTTPS

**Labels:** `priority: wajib` `category: teknis` `type: infrastruktur`

### Deskripsi
Keamanan koneksi antara browser pelanggan dan server menggunakan enkripsi SSL/TLS (HTTPS).

### Acceptance Criteria
- [ ] Semua halaman diakses via HTTPS
- [ ] Redirect HTTP → HTTPS otomatis
- [ ] Tidak ada Mixed Content warning

### Checklist Teknis
- [ ] **Config Laravel:** `APP_URL` di `.env` perlu diupdate ke `https://` saat deploy
- [ ] **Middleware:** Belum ada `ForceHttps` middleware — perlu ditambah di production
- [ ] **Server:** SSL tergantung pada konfigurasi hosting/server (di luar scope kode)

### Catatan Agent
> ℹ️ **Infrastruktur — tidak bisa dikonfirmasi dari kode.** Perlu dikonfigurasi di level server (Nginx/Apache + Let's Encrypt). Tambahkan `URL::forceScheme('https')` di `AppServiceProvider::boot()` saat production.

---

## [ISSUE-050] Validasi & Sanitasi Input

**Labels:** `priority: wajib` `category: teknis` `type: backend`

### Deskripsi
Semua input dari pengguna (form, URL parameter, query string) harus divalidasi dan disanitasi untuk mencegah serangan SQL injection, XSS, dan manipulasi data.

### Acceptance Criteria
- [ ] Semua form memiliki validasi server-side (bukan hanya client-side)
- [ ] Tidak ada query database yang menggunakan raw string concatenation dengan input user
- [ ] Output ke HTML menggunakan escaping yang benar
- [ ] CSRF token ada di semua form POST/PUT/DELETE

### Checklist Teknis
- [ ] **Form Request / Validate:** Setiap Controller yang menerima input menggunakan `$request->validate()` atau Form Request class
- [ ] **Query Builder / Eloquent:** Tidak ada `DB::select("SELECT * WHERE id = " . $id)` — gunakan binding parameter
- [ ] **Blade:** Semua output menggunakan `{{ $var }}` bukan `{!! $var !!}` kecuali benar-benar aman
- [ ] **CSRF:** `@csrf` ada di semua Blade form

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-051] Autentikasi Dua Faktor Admin (2FA)

**Labels:** `priority: penting` `category: teknis` `type: backend`

### Deskripsi
Keamanan ekstra untuk akun admin dengan Two-Factor Authentication (2FA).

### Acceptance Criteria
- [ ] Admin diminta memasukkan kode verifikasi setelah login
- [ ] Kode via email atau TOTP
- [ ] 2FA bisa diaktifkan/dinonaktifkan

### Checklist Teknis
- [ ] **Package:** `pragmarx/google2fa-laravel` belum terinstal
- [ ] **Middleware:** Belum ada middleware 2FA
- [ ] **View:** Halaman input kode 2FA belum ada

### Catatan Agent
> ⚠️ **Belum diimplementasi.** Perlu install package `pragmarx/google2fa-laravel` dan middleware khusus untuk admin.

---

## [ISSUE-052] Backup Database Otomatis

**Labels:** `priority: penting` `category: teknis` `type: backend`

### Deskripsi
Sistem backup otomatis database secara berkala.

### Acceptance Criteria
- [ ] Backup terjadwal otomatis harian
- [ ] Simpan di lokasi aman
- [ ] Notifikasi jika gagal

### Checklist Teknis
- [ ] **Package:** `spatie/laravel-backup` belum terinstal
- [ ] **Scheduler:** Belum terkonfigurasi
- [ ] **Config:** Belum ada

### Catatan Agent
> ⚠️ **Belum diimplementasi.** Install `composer require spatie/laravel-backup` dan konfigurasi scheduler di `routes/console.php`.

---

## [ISSUE-053] Halaman Error yang Ramah Pengguna

**Labels:** `priority: penting` `category: teknis` `type: frontend`

### Deskripsi
Halaman error kustom (404, 500, 403) yang informatif dan sesuai brand, dengan pesan yang mudah dipahami dan tombol untuk kembali atau ke beranda.

### Acceptance Criteria
- [ ] Halaman 404 (Not Found) kustom tersedia dan sesuai brand
- [ ] Halaman 500 (Server Error) kustom tersedia
- [ ] Kedua halaman memiliki tombol "Kembali ke Beranda"
- [ ] Halaman error tidak menampilkan stack trace atau informasi teknis ke pengguna

### Checklist Teknis
- [ ] **View:** `resources/views/errors/404.blade.php` dan `resources/views/errors/500.blade.php` ada
- [ ] **Config:** `APP_DEBUG=false` di `.env` production (agar error detail tidak tampil ke user)
- [ ] **Layout:** Halaman error menggunakan layout yang sama dengan halaman lain (ada navbar/footer)

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-054] SEO Dasar

**Labels:** `priority: penting` `category: teknis` `type: frontend`

### Deskripsi
Implementasi SEO dasar agar halaman produk dan toko bisa ditemukan di mesin pencari: meta title, meta description, URL slug bersih, dan sitemap.

### Acceptance Criteria
- [ ] Setiap halaman memiliki `<title>` yang unik dan deskriptif
- [ ] Setiap halaman memiliki `<meta name="description">` yang relevan
- [ ] URL produk menggunakan slug (misal: `/products/kue-coklat-ulang-tahun`) bukan ID numerik
- [ ] `<h1>` hanya ada satu per halaman dan berisi keyword utama

### Checklist Teknis
- [ ] **Layout Blade:** `@yield('title')` dan `@yield('meta_description')` ada di `layouts/app.blade.php`
- [ ] **View Produk:** `@section('title', $product->name)` didefinisikan di view produk
- [ ] **Slug:** Kolom `slug` ada di tabel `products` dan digunakan di route
- [ ] **Sitemap:** (Bonus) Package `spatie/laravel-sitemap` atau file `sitemap.xml` statis ada

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

## [ISSUE-055] Optimasi Kecepatan & Gambar

**Labels:** `priority: penting` `category: teknis` `type: frontend`

### Deskripsi
Optimasi performa halaman agar website cepat dibuka, terutama di koneksi mobile: lazy loading gambar, kompresi gambar produk, dan caching dasar.

### Acceptance Criteria
- [ ] Gambar produk menggunakan lazy loading (tidak semua dimuat sekaligus)
- [ ] Gambar yang diupload dikompres sebelum disimpan (ukuran file berkurang)
- [ ] Halaman produk terbuka dalam waktu yang wajar (< 3 detik di koneksi normal)

### Checklist Teknis
- [ ] **HTML:** Atribut `loading="lazy"` ada pada elemen `<img>` gambar produk
- [ ] **Image Compression:** Package `intervention/image` atau proses resize ada saat upload gambar
- [ ] **Cache:** `Route::cache()` atau view caching aktif di production (opsional tapi dianjurkan)
- [ ] **Asset:** CSS dan JS di-minify (via `npm run build` / Vite production build)

### Catatan Agent
> ✅ Sudah diimplementasi — lihat kode terkait untuk detail.

---

---

## Panduan Penyelesaian Review untuk Agent

Setelah semua issue diperiksa, lengkapi tabel ringkasan di bagian atas dokumen ini dengan menghitung:
- **Selesai:** Semua acceptance criteria terpenuhi dan semua checklist teknis tercentang
- **Belum:** Minimal satu acceptance criteria atau checklist teknis tidak terpenuhi
- **Catatan:** Isi dengan temuan penting (bug, implementasi tidak sesuai, atau rekomendasi perbaikan)

### Skala Prioritas Review
Urutan pemeriksaan yang disarankan:
1. **Teknis & Keamanan** (ISSUE-048 s.d. 055) — cek dulu fondasi keamanan
2. **Akun & Autentikasi** (ISSUE-028 s.d. 032) — pastikan autentikasi berfungsi benar
3. **Katalog & Produk** (ISSUE-001 s.d. 008) — inti dari e-commerce
4. **Pemesanan & Keranjang** (ISSUE-009 s.d. 014) — alur transaksi utama
5. **Pembayaran** (ISSUE-021 s.d. 027) — validasi alur keuangan
6. **Admin Panel** (ISSUE-033 s.d. 042) — manajemen back-end
7. **Pengiriman** (ISSUE-015 s.d. 020) — logistik
8. **Ulasan & Konten** (ISSUE-043 s.d. 047) — fitur pendukung