# Dokumentasi Fitur — Jagoan Kue

## 1. Registrasi & Login

### Registrasi Customer
1. Pengguna mengakses `/register`.
2. Mengisi form: nama, email, password, konfirmasi password.
3. Laravel Breeze memvalidasi input dan menyimpan akun baru ke tabel `users` dengan role `customer`.
4. Pengguna otomatis login dan diarahkan ke halaman utama.

### Login
1. Pengguna mengakses `/login`.
2. Mengisi email dan password.
3. Breeze memverifikasi kredensial; jika cocok, sesi dibuat dan pengguna diarahkan ke halaman utama.
4. Jika gagal, pesan error ditampilkan.

### Login Admin
1. Admin menggunakan form login yang sama (`/login`).
2. Setelah login, admin dapat mengakses `/admin/dashboard`.
3. Setiap method di `AdminController` memanggil `checkAdmin()` untuk memastikan user adalah admin (role = `admin`). Jika bukan, akses ditolak dengan HTTP 403.

---

## 2. Browsing Produk

1. Pengguna mengakses `/products`.
2. `ProductController@index` mengambil semua kategori beserta produknya dari database.
3. Produk ditampilkan berkelompok per kategori di halaman `products/index`.
4. Pengguna klik produk untuk membuka detail di `/products/{slug}`.
5. Halaman detail menampilkan nama, deskripsi, harga, stok, dan tombol "Beli Sekarang" / "Tambah ke Keranjang".

---

## 3. Keranjang Belanja

1. Pengguna klik "Tambah ke Keranjang" pada halaman produk.
2. `CartController@add` menyimpan `product_id` dan `quantity` ke dalam session PHP.
3. Pengguna membuka `/cart` untuk melihat isi keranjang.
4. `CartController@index` membaca session dan mengambil data produk dari database.
5. Pengguna dapat menghapus item (checkbox + tombol Hapus) melalui `CartController@remove`.
6. Pengguna dapat mengosongkan keranjang melalui `CartController@clear`.
7. Klik "Checkout" membuka `CartController@checkout` yang meneruskan item ke form pemesanan.

---

## 4. Pemesanan (Checkout)

### Alur Checkout dari Keranjang
1. Pengguna mengisi form di halaman `orders/create`: alamat pengiriman, catatan, dan metode pembayaran.
2. Form dikirim ke `OrderController@store` via POST `/orders`.
3. Controller memvalidasi stok tiap produk di database.
4. Jika stok cukup:
   - Record `orders` dibuat dengan kode unik (`ORD-XXXXXXXX`) dan status `pending`.
   - Record `order_items` dibuat untuk setiap item pesanan.
   - Stok produk dikurangi otomatis (`decrement`).
   - Record `payments` dibuat dengan status `unpaid` (atau `paid` untuk COD).
5. Session keranjang dihapus.
6. Pengguna diarahkan ke halaman pembayaran atau halaman sukses (COD).

### Checkout Langsung dari Produk
1. Pengguna klik "Beli Sekarang" di halaman detail produk.
2. Route `/checkout/{product_id}` menampilkan form pemesanan dengan satu produk.

---

## 5. Pembayaran

### Transfer Bank / E-Wallet / QRIS
1. Setelah pesanan dibuat, pengguna diarahkan ke `/orders/{order}/payment`.
2. Halaman menampilkan nomor rekening / instruksi pembayaran.
3. Pengguna melakukan transfer, lalu upload foto bukti bayar.
4. Form upload dikirim ke `OrderController@uploadProof` via POST.
5. Controller memvalidasi file (jpg/png, maks 5 MB) dan menyimpannya ke storage `payment_proofs`.
6. Status `payments` diperbarui menjadi `paid`.
7. Status `orders` diperbarui menjadi `processing`.
8. Pengguna diarahkan ke halaman `/orders/{order}/success`.

### COD (Cash on Delivery)
1. Pengguna memilih metode "COD" saat checkout.
2. `OrderController@store` langsung membuat `payments` dengan status `paid` dan `paid_at` = sekarang.
3. Status `orders` langsung diperbarui menjadi `confirmed`.
4. Pengguna diarahkan ke halaman sukses tanpa perlu upload bukti bayar.

---

## 6. Status Pesanan

Urutan status pesanan normal:

```
pending → processing → completed
```

Status tambahan:
- `confirmed` — khusus pesanan COD yang langsung dikonfirmasi
- `cancelled` — pesanan dibatalkan admin

### Pelacakan oleh Customer
1. Pengguna membuka `/orders` untuk melihat daftar pesananannya.
2. Klik pesanan untuk melihat detail status di `/orders/{order}`.
3. Halaman detail menampilkan timeline status dan informasi pembayaran.

### Pembaruan oleh Admin
1. Admin membuka `/admin/orders/{order}`.
2. Admin memilih status baru dan mengklik tombol ubah status.
3. Route `PATCH /admin/orders/{order}/status/{status}` memanggil `AdminController@updateOrderStatus`.
4. Jika status diubah menjadi `completed`, status `payments` juga otomatis diperbarui menjadi `paid`.

---

## 7. Manajemen Produk (Admin)

1. Admin mengakses `/admin/products-list` untuk melihat daftar produk.
2. **Tambah produk:** Form di `/admin/products/create`, submit ke `ProductController@store`.
   - Upload gambar disimpan ke storage `products/`.
   - Slug dibuat otomatis dari nama produk.
3. **Edit produk:** Form di `/admin/products/{product}/edit`, submit ke `ProductController@update`.
4. **Hapus produk:** Tombol Hapus memanggil `ProductController@destroy`.

---

## 8. Manajemen Pesanan (Admin)

1. Admin mengakses `/admin/orders` untuk melihat semua pesanan (paginated 10 per halaman).
2. Klik pesanan untuk melihat detail lengkap termasuk item, total, dan bukti bayar.
3. Admin dapat mengubah status pesanan melalui tombol di halaman detail.
4. Halaman `/admin/finance` menampilkan rekapitulasi semua pembayaran, total pendapatan, dan jumlah unpaid.

---

## 9. Dashboard Admin

Halaman `/admin/dashboard` menampilkan:
- **KPI bulan ini:** total pesanan, pendapatan, pelanggan baru, pesanan pending — masing-masing dengan persentase pertumbuhan vs. bulan lalu.
- **Grafik pendapatan 7 hari** — batang harian dari Payment yang berstatus `paid`.
- **Top 3 produk terlaris** — berdasarkan jumlah `order_items`.
- **Aktivitas terbaru** — 5 pesanan terbaru beserta keterangan waktu relatif.
