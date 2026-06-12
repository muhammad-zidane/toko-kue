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
3. Semua route admin dilindungi middleware `EnsureUserIsAdmin` (alias `admin`). Jika bukan admin, akses ditolak dengan HTTP 403.

---

## 2. Browsing Produk

1. Pengguna mengakses `/products`.
2. `ProductController@index` mengambil semua kategori beserta produknya.
3. Produk ditampilkan berkelompok per kategori.
4. Pengguna klik produk untuk membuka detail di `/products/{slug}`.
5. Halaman detail menampilkan nama, deskripsi, harga, stok, badge, opsi kustomisasi (jika ada), dan ulasan produk yang sudah disetujui admin.
6. Tersedia tombol "Beli Sekarang" (checkout langsung) dan "Tambah ke Keranjang".

---

## 3. Keranjang Belanja

1. Keranjang berbasis session PHP — dapat dipakai tanpa login (guest).
2. Pengguna klik "Tambah ke Keranjang"; `CartController@add` menyimpan `product_id`, `quantity`, dan pilihan kustomisasi ke session.
3. Pengguna membuka `/cart` untuk melihat isi keranjang.
4. `CartController@updateItem` mengizinkan perubahan jumlah item secara AJAX.
5. `CartController@remove` menghapus item yang dipilih; `CartController@clear` mengosongkan seluruh keranjang.
6. Klik "Checkout" di `/cart` membutuhkan login dan membuka form pemesanan.

---

## 4. Kustomisasi Produk

1. Admin mengatur opsi kustomisasi di `/admin/customizations`, dikelompokkan per tipe (`rasa`, `ukuran`, `topping`, `lainnya`) dan dapat dikaitkan ke kategori tertentu.
2. Setiap opsi memiliki nama, harga tambahan (`extra_price`), dan status aktif.
3. Saat customer membuka detail produk, opsi kustomisasi yang aktif dan relevan dengan kategori produk ditampilkan.
4. Pilihan customer disimpan ke `order_item_customizations` saat checkout, dengan harga tambahan yang ikut dihitung ke total.

---

## 5. Voucher Diskon

1. Admin membuat voucher di `/admin/vouchers` dengan tipe `percent` (persentase) atau `fixed` (nominal tetap), batas pemakaian, minimum belanja, dan tanggal kadaluarsa.
2. Customer memasukkan kode voucher di halaman keranjang.
3. `VoucherController@apply` memvalidasi kode: kode harus ada, aktif, belum kadaluarsa, belum mencapai batas pemakaian, dan total belanja memenuhi minimum.
4. Jika valid, diskon dihitung dan ditampilkan. Kode dan nominal diskon disimpan ke `orders.voucher_code` dan `orders.discount_amount` saat checkout.

---

## 6. Pemesanan (Checkout)

### Checkout dari Keranjang
1. Customer membuka `/cart/checkout` (butuh login).
2. `CartController@checkout` meneruskan item ke form pemesanan.
3. Customer mengisi: alamat pengiriman (bisa pilih dari alamat tersimpan), catatan, metode pengiriman (delivery/pickup), tanggal & slot, dan metode pembayaran.
4. Form dikirim ke `OrderController@store` via POST `/orders`.
5. Controller memvalidasi stok tiap produk.
6. Jika stok cukup:
   - Record `orders` dibuat dengan kode unik (`ORD-XXXXXXXX`) dan status `pending`.
   - Record `order_items` dibuat beserta `order_item_customizations` jika ada.
   - Stok produk dikurangi otomatis.
7. Session keranjang dihapus.
8. Pengguna diarahkan ke halaman pembayaran atau halaman sukses (COD/pickup langsung).

### Checkout Langsung dari Produk
1. Pengguna klik "Beli Sekarang" di halaman detail produk.
2. Route `/checkout/{product}` membuka `OrderController@singleProductCheckout` dengan satu produk langsung.

---

## 7. Pembayaran

### Transfer Bank / E-Wallet / QRIS
1. Setelah pesanan dibuat, pengguna diarahkan ke `/orders/{order}/payment`.
2. Halaman menampilkan nomor rekening / instruksi pembayaran.
3. Customer dapat memilih bayar **DP 50%** atau **lunas**.
4. Customer upload foto bukti bayar melalui `OrderController@uploadProof`.
5. Controller memvalidasi file (jpg/png, maks 5 MB) dan menyimpannya ke storage `payment_proofs`.
6. Record `payments` dibuat.
7. Admin dapat mengonfirmasi (`confirmPayment`) atau menolak (`rejectPayment`) bukti pembayaran dari halaman detail pesanan.

### COD (Cash on Delivery)
1. Customer memilih metode "COD" saat checkout.
2. `OrderController@store` membuat `payments` langsung dengan status `paid` dan status `orders` langsung `processing`.
3. Customer diarahkan ke halaman sukses tanpa perlu upload bukti.

---

## 8. Status Pesanan

Alur status pesanan:

```
pending → processing → shipped → completed
                                ↘
                             cancelled
```

| Status       | Keterangan                                        |
|--------------|---------------------------------------------------|
| `pending`    | Pesanan dibuat, menunggu pembayaran               |
| `processing` | Pembayaran dikonfirmasi, sedang diproses          |
| `shipped`    | Pesanan dalam pengiriman                          |
| `completed`  | Pesanan selesai/diterima customer                 |
| `cancelled`  | Pesanan dibatalkan oleh admin                     |

Status pembayaran di-track terpisah di `orders.payment_status`: `unpaid`, `dp`, atau `paid`.

### Pelacakan oleh Customer
1. Customer membuka `/orders` untuk melihat daftar pesanannya.
2. Klik pesanan untuk melihat detail di `/orders/{order}`.
3. `/orders/{order}/status` menampilkan halaman pelacakan status dengan timeline.
4. Invoice pesanan dapat diunduh sebagai PDF dari `/orders/{order}/invoice`.

### Pembaruan oleh Admin
1. Admin membuka `/admin/orders/{order}`.
2. Memilih status baru dan mengklik tombol ubah status.
3. Route `PATCH /admin/orders/{order}/status/{status}` memanggil `AdminController@updateOrderStatus`.

---

## 9. Ulasan Produk

1. Customer dapat menulis ulasan setelah pesanan berstatus `completed`.
2. Halaman ulasan diakses dari `/orders/{order}/reviews`.
3. Customer mengisi rating (1–5 bintang), komentar, dan opsional foto.
4. Satu customer hanya bisa menulis satu ulasan per produk per pesanan (constraint unik).
5. Ulasan baru berstatus `is_approved = false` — tidak tampil di halaman produk sampai disetujui admin.
6. Admin memoderasi ulasan di `/admin/reviews`: menyetujui (`approveReview`) atau menghapus.
7. Ulasan yang disetujui tampil di halaman detail produk beserta foto dan rating.

---

## 10. Alamat Tersimpan

1. Customer mengelola daftar alamat di `/account/addresses`.
2. Setiap alamat memiliki label (mis. "Rumah", "Kantor"), nama penerima, nomor telepon, dan detail alamat lengkap.
3. Satu alamat dapat ditandai sebagai default.
4. Saat checkout, customer bisa memilih dari alamat tersimpan atau mengisi alamat baru secara manual.

---

## 11. Manajemen Produk (Admin)

1. Admin mengakses `/admin/products-list` untuk melihat daftar produk.
2. **Tambah produk:** Form di `/admin/products/create`, submit ke `ProductController@store`.
   - Upload gambar disimpan ke storage `products/`.
   - Slug dibuat otomatis dari nama produk.
   - Admin dapat menentukan badge: `best_seller`, `new`, atau `sale`.
3. **Edit produk:** Form di `/admin/products/{product}/edit`, submit ke `ProductController@update`.
4. **Hapus produk:** Tombol Hapus memanggil `ProductController@destroy`.

---

## 12. Manajemen Kategori (Admin)

1. Admin mengakses `/admin/categories` untuk melihat daftar kategori.
2. Tambah kategori via POST `/admin/categories`.
3. Hapus kategori via DELETE `/admin/categories/{category}`.

---

## 13. Manajemen Pesanan (Admin)

1. Admin mengakses `/admin/orders` untuk melihat semua pesanan (paginated).
2. Detail pesanan mencakup item, total, metode pembayaran, dan bukti bayar.
3. Admin dapat:
   - Mengubah status pesanan.
   - Mengonfirmasi atau menolak bukti pembayaran.
   - Mengunduh bukti pembayaran via `/admin/orders/{order}/download-proof`.

---

## 14. Manajemen Banner (Admin)

1. Admin mengelola banner/slider halaman utama di `/admin/banners`.
2. Setiap banner memiliki judul, subjudul, gambar, URL tujuan, urutan tampil, dan status aktif.
3. Gambar bersifat opsional (nullable).

---

## 15. Manajemen Voucher (Admin)

1. Admin membuat dan mengelola voucher di `/admin/vouchers`.
2. Voucher mendukung diskon persentase atau nominal tetap.
3. Admin dapat mengatur batas pemakaian, minimum belanja, dan tanggal kadaluarsa.

---

## 16. Manajemen Zona Pengiriman (Admin)

1. Admin mengatur zona dan tarif ongkir di `/admin/shipping-zones`.
2. Setiap zona memiliki nama area dan biaya.
3. Zona dapat diaktifkan atau dinonaktifkan.

---

## 17. Kustomisasi Produk (Admin)

1. Admin mengelola opsi kustomisasi di `/admin/customizations`.
2. Opsi dikategorikan sebagai `rasa`, `ukuran`, `topping`, atau `lainnya`.
3. Setiap opsi dapat dikaitkan ke kategori produk tertentu (nullable = berlaku untuk semua).
4. Admin dapat mengaktifkan/menonaktifkan opsi secara individual via toggle.

---

## 18. Kalender Produksi (Admin)

1. Admin mengakses `/admin/production-calendar`.
2. Halaman menampilkan pesanan yang harus diproses berdasarkan tanggal pengiriman/pengambilan (`delivery_date`).
3. Membantu admin merencanakan kapasitas produksi harian.

---

## 19. Laporan & Analitik (Admin)

1. `/admin/analytics` menampilkan analitik pendapatan, distribusi status pesanan, dan performa per kategori.
2. `/admin/analytics/export` menghasilkan file Excel laporan penjualan menggunakan `LaporanPenjualanExport` (Maatwebsite/Excel).
3. `/admin/finance` menampilkan rekapitulasi semua pembayaran, total pendapatan, dan jumlah unpaid.

---

## 20. Dashboard Admin

Halaman `/admin/dashboard` menampilkan:
- **KPI bulan ini:** total pesanan, pendapatan, pelanggan baru, pesanan pending — masing-masing dengan persentase pertumbuhan vs. bulan lalu.
- **Grafik pendapatan 7 hari** — batang harian dari Payment yang berstatus `paid`.
- **Top 3 produk terlaris** — berdasarkan jumlah `order_items`.
- **Aktivitas terbaru** — 5 pesanan terbaru beserta keterangan waktu relatif.

---

## 21. Notifikasi Admin

1. Notifikasi baru muncul di navbar admin saat ada pesanan masuk atau pembayaran baru.
2. Admin dapat menandai satu notifikasi sebagai sudah dibaca via POST `/admin/notifications/{id}/read`.
3. Tandai semua sekaligus via POST `/admin/notifications/read-all`.
4. Notifikasi disimpan di tabel `notifications` menggunakan Laravel database notifications.

---

## 22. Manajemen Pelanggan (Admin)

1. Admin mengakses `/admin/customers` untuk melihat daftar semua customer.
2. Halaman menampilkan nama, email, jumlah pesanan, dan total belanja tiap customer.

---

## 23. Pengaturan Akun Admin

1. Admin mengakses `/admin/settings` untuk mengubah nama dan email akun admin.
2. Perubahan disimpan via POST `/admin/settings`.
