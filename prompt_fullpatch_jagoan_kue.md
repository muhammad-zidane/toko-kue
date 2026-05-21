# FULL PATCH — Jagoan Kue Critical Issues
# Gunakan prompt ini SATU PER SATU di Claude Code, tunggu selesai sebelum lanjut ke berikutnya.

---

## ⚠️ SEBELUM MULAI — Jalankan ini dulu di Claude Code:

```
Buat git commit dulu sebelum kita mulai patch apapun:
git add -A && git commit -m "chore: snapshot sebelum full patch critical issues"
```

---

---
# PATCH 1 — K1: Tambah kolom is_approved + fix moderasi ulasan
---

Kerjakan fix berikut secara berurutan. Jangan skip langkah apapun.

**Masalah:**
Kolom `is_approved` dirujuk di AdminController dan view admin/reviews.blade.php, tapi tidak ada di tabel `product_reviews`. Akan crash saat admin klik approve ulasan.

**Yang harus dilakukan:**

1. Buat migration baru:
   - Nama: `add_is_approved_to_product_reviews_table`
   - Tambah kolom: `is_approved` boolean, default false, setelah kolom `comment`
   - Jalankan migration

2. Update `AdminController::reviews()`:
   - Pass variabel `$pendingCount` (is_approved = false) ke view
   - Pass variabel `$approvedCount` (is_approved = true) ke view

3. Update `AdminController::approveReview()`:
   - Pastikan method ini toggle `is_approved` dengan benar
   - Return redirect dengan pesan sukses yang sesuai

4. Update `resources/views/admin/reviews.blade.php`:
   - Pastikan `$pendingCount` dan `$approvedCount` dipakai dengan benar
   - Tampilkan badge status "Menunggu" / "Disetujui" di tiap ulasan

5. Update `resources/views/home/index.blade.php` (seksi testimoni):
   - Tambah filter `->where('is_approved', true)` agar hanya ulasan yang disetujui tampil di homepage
   - Cek apakah query testimoni ada di HomeController atau langsung di view, sesuaikan tempatnya

Setelah selesai, test manual:
- Login sebagai admin → buka menu Ulasan → klik Approve → pastikan tidak error
- Buka homepage → pastikan testimoni hanya tampil ulasan yang approved

Commit setelah selesai:
```
git add -A && git commit -m "fix(K1): tambah kolom is_approved ke product_reviews, fix moderasi ulasan & filter homepage"
```

---

---
# PATCH 2 — K7: Guest bisa akses cart tanpa login
---

**Masalah:**
Semua route cart ada di dalam `Route::middleware('auth')`. Guest yang klik "Tambah ke Keranjang" langsung di-redirect ke login dan kehilangan konteks produk.

**Yang harus dilakukan:**

1. Buka `routes/web.php`
2. Pindahkan route-route berikut ke LUAR grup middleware auth:
   - `cart.index` (GET /cart)
   - `cart.add` (POST /cart/add)
   - `cart.update` atau `cart.updateItem` (PATCH/PUT)
   - `cart.remove` (DELETE)
   - `cart.clear` (DELETE)

3. Hanya route ini yang TETAP di dalam middleware auth:
   - `cart.checkout` (POST /cart/checkout atau route ke orders.store)
   - Semua route `orders.*`

4. Update `CartController`:
   - Pastikan semua method cart yang sekarang bisa diakses guest tidak memanggil `auth()->id()` secara langsung tanpa guard
   - Untuk method yang menyimpan ke session (bukan DB), tidak ada masalah
   - Untuk method yang butuh user_id, tambah pengecekan: jika guest, simpan ke session dulu

5. Update view cart/navbar:
   - Jika tombol "Checkout" diklik oleh guest, redirect ke halaman login dengan pesan "Silakan login untuk melanjutkan pemesanan"
   - Gunakan `intended()` agar setelah login langsung kembali ke cart

Setelah selesai, test:
- Tanpa login → buka produk → klik tambah ke keranjang → harus berhasil masuk cart
- Tanpa login → klik checkout di cart → harus redirect ke login
- Setelah login → harus kembali ke cart (intended redirect)

Commit:
```
git add -A && git commit -m "fix(K7): cart accessible untuk guest, hanya checkout yang butuh auth"
```

---

---
# PATCH 3 — K2 + K5: DB Transaction di OrderController::store
---

**Masalah:**
`OrderController::store` membuat Order, OrderItem, Payment, decrement stok, dan increment voucher tanpa database transaction. Jika salah satu gagal di tengah, data akan inkonsisten (stok bocor, voucher hangus, order tanpa payment).

**Yang harus dilakukan:**

1. Buka `app/Http/Controllers/OrderController.php`

2. Tambah import di bagian atas:
   ```php
   use Illuminate\Support\Facades\DB;
   ```

3. Wrap SELURUH isi method `store()` mulai dari cek stok hingga redirect sukses ke dalam:
   ```php
   return DB::transaction(function () use ($request) {
       // seluruh logika store di sini
   });
   ```

4. Di dalam transaction, tambah `lockForUpdate()` saat query produk untuk cek stok:
   ```php
   $product = Product::where('id', $productId)->lockForUpdate()->first();
   ```
   Ini mencegah race condition dua user beli produk terakhir bersamaan.

5. Pindahkan `$voucher->increment('used_count')` ke DALAM transaction (jika belum)

6. Tambah error handling di luar transaction:
   ```php
   try {
       return DB::transaction(function () use ($request) {
           // ...
       });
   } catch (\Throwable $e) {
       \Log::error('Order creation failed: ' . $e->getMessage());
       return back()->withErrors(['error' => 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.']);
   }
   ```

7. Pastikan tidak ada `return` atau `redirect()` di dalam transaction yang bisa menyebabkan commit sebagian — semua redirect harus di luar transaction atau kembalikan nilai dari closure

Setelah selesai, test:
- Buat order normal → harus berhasil
- Coba buat order dengan stok = 1 dari dua tab browser bersamaan → hanya satu yang boleh berhasil
- Coba buat order dengan voucher → voucher used_count harus bertambah HANYA jika order berhasil

Commit:
```
git add -A && git commit -m "fix(K2+K5): wrap OrderController::store dalam DB transaction + lockForUpdate stok"
```

---

---
# PATCH 4 — W3: Idempotency guard di confirmPayment & rejectPayment
---

**Masalah:**
Admin bisa klik "Konfirmasi Pembayaran" dua kali, atau mengkonfirmasi order yang belum upload bukti. Tidak ada pengecekan status sebelum update.

**Yang harus dilakukan:**

1. Buka `AdminController::confirmPayment()`
2. Tambah guard di awal method:
   ```php
   // Cegah konfirmasi ganda
   if ($order->payment && $order->payment->status === 'paid') {
       return back()->with('error', 'Pembayaran ini sudah dikonfirmasi sebelumnya.');
   }

   // Cegah konfirmasi tanpa bukti upload
   if (!$order->payment || !$order->payment->proof_image) {
       return back()->with('error', 'Pelanggan belum mengupload bukti pembayaran.');
   }
   ```

3. Buka `AdminController::rejectPayment()`
4. Tambah guard serupa:
   ```php
   if ($order->payment && $order->payment->status === 'paid') {
       return back()->with('error', 'Tidak bisa menolak pembayaran yang sudah dikonfirmasi.');
   }
   ```

5. Pastikan view admin/orders menampilkan flash message error dengan benar (cek apakah `session('error')` sudah di-render di layout admin)

Commit:
```
git add -A && git commit -m "fix(W3): tambah idempotency guard di confirmPayment dan rejectPayment"
```

---

---
# PATCH 5 — K4: Pindah bukti pembayaran ke private disk
---

**Masalah:**
Bukti transfer pelanggan disimpan di `storage/app/public/payment_proofs/` yang bisa diakses siapapun via URL langsung. Mengandung data sensitif (nomor rekening, nama pemilik).

**Yang harus dilakukan:**

1. Buka `app/Http/Controllers/OrderController.php` method `uploadProof()`
2. Ubah disk penyimpanan dari `'public'` ke `'local'`:
   ```php
   // Sebelum:
   $path = $request->file('proof_image')->store('payment_proofs', 'public');

   // Sesudah:
   $path = $request->file('proof_image')->store('payment_proofs', 'local');
   ```

3. Buat route baru untuk serve file bukti pembayaran secara authorized:
   ```php
   // Di routes/web.php, dalam grup middleware auth
   Route::get('/payment-proof/{payment}', [PaymentController::class, 'showProof'])
        ->name('payment.proof');
   ```

4. Buat method `showProof()` di controller yang sesuai (PaymentController atau OrderController):
   ```php
   public function showProof(Payment $payment)
   {
       // Hanya admin atau pemilik order yang boleh akses
       $isOwner = $payment->order->user_id === auth()->id();
       $isAdmin = auth()->user()->role === 'admin';

       if (!$isOwner && !$isAdmin) {
           abort(403);
       }

       $path = storage_path('app/' . $payment->proof_image);

       if (!file_exists($path)) {
           abort(404);
       }

       return response()->file($path);
   }
   ```

5. Update semua tempat yang menampilkan bukti pembayaran (admin view, order detail):
   - Ganti `asset('storage/' . $payment->proof_image)`
   - Dengan `route('payment.proof', $payment)`

6. **Opsional tapi dianjurkan:** Untuk file lama yang sudah terlanjur di public disk, buat artisan command untuk migrasi atau hapus manual via tinker

Setelah selesai, test:
- Login sebagai pelanggan → upload bukti → cek bahwa file TIDAK bisa diakses via URL langsung `/storage/payment_proofs/...`
- Login sebagai admin → buka order → klik lihat bukti → harus tampil

Commit:
```
git add -A && git commit -m "fix(K4): pindah payment_proofs ke private disk, serve via authorized controller"
```

---

---
# PATCH 6 — K6: Refactor customer views pakai @extends
---

**Masalah:**
Semua halaman customer (home, katalog, cart, orders, dll) adalah HTML document lengkap dengan duplikasi `<head>`, Google Fonts, CSS, dan navbar. Admin side sudah benar pakai `@extends`.

**Yang harus dilakukan:**

1. Pertama, review `resources/views/layouts/app.blade.php`:
   - Pastikan sudah ada `@yield('content')`
   - Pastikan ada `@stack('styles')` di dalam `<head>` sebelum `</head>`
   - Pastikan ada `@stack('scripts')` sebelum `</body>`
   - Pastikan `@include('partials.navbar')` ada di dalamnya
   - Jika ada yang kurang, lengkapi dulu

2. Refactor halaman-halaman berikut satu per satu (mulai dari yang paling sederhana):
   - `resources/views/home/index.blade.php`
   - `resources/views/products/index.blade.php`
   - `resources/views/products/show.blade.php`
   - `resources/views/cart/index.blade.php`
   - `resources/views/orders/create.blade.php`
   - `resources/views/orders/payment.blade.php`
   - `resources/views/orders/index.blade.php`
   - `resources/views/orders/show.blade.php`
   - Semua view customer lainnya yang belum pakai @extends

3. Pola refactor untuk setiap file:
   ```blade
   {{-- SEBELUM: file dimulai dengan <!DOCTYPE html> --}}

   {{-- SESUDAH: --}}
   @extends('layouts.app')

   @push('styles')
   <style>
       /* Pindahkan semua <style> inline dari halaman ini ke sini */
   </style>
   @endpush

   @section('content')
       {{-- Pindahkan semua konten antara <body> dan </body> ke sini --}}
       {{-- HAPUS @include('partials.navbar') karena sudah ada di layout --}}
   @endsection

   @push('scripts')
   <script>
       /* Pindahkan semua <script> inline ke sini */
   </script>
   @endpush
   ```

4. Setelah refactor setiap halaman, buka di browser dan pastikan tampilannya sama persis

5. Setelah semua halaman selesai, cek apakah ada CSS/style yang perlu dipindah ke `public/css/app.css` karena dipakai di lebih dari satu halaman

Setelah selesai, test semua halaman:
- Homepage, katalog, detail produk, cart, form order, payment, riwayat order

Commit:
```
git add -A && git commit -m "refactor(K6): semua customer views pakai @extends layouts.app"
```

---

---
# PATCH 7 — K3: Alur pembayaran DP dua fase
---

**Ini patch paling kompleks. Kerjakan setelah semua patch di atas selesai.**

**Masalah:**
Order dengan DP hanya punya satu flow upload bukti. Tidak ada alur untuk pelunasan (fase kedua). `confirmPayment` langsung mark sebagai `paid` meski baru DP 50%.

**Yang harus dilakukan:**

1. **Update model Payment:**
   - Tambah kolom `phase` enum('dp', 'final', 'full') via migration baru
   - Atau alternatif: ubah relasi `Order hasOne Payment` menjadi `Order hasMany Payments`
   - **Pilih opsi hasMany Payments** — lebih bersih dan fleksibel

2. **Buat migration baru:**
   - Jika pakai hasMany: tidak perlu ubah tabel payments, cukup pastikan foreign key `order_id` sudah ada
   - Tambah kolom `phase` enum('dp','final','full') default 'full' ke tabel payments
   - Tambah kolom `amount` ke payments (berapa yang dibayar di fase ini)

3. **Update OrderController:**
   - `uploadProof()`: tambah parameter phase ('dp' atau 'full'), simpan ke payments dengan phase yang sesuai
   - Untuk order non-DP: phase = 'full'
   - Untuk order DP fase 1: phase = 'dp'

4. **Buat route baru untuk pelunasan:**
   ```php
   // Customer upload bukti pelunasan
   Route::post('/orders/{order}/upload-final-proof', [OrderController::class, 'uploadFinalProof'])
        ->name('orders.uploadFinalProof')
        ->middleware('auth');

   // Admin konfirmasi pelunasan
   Route::post('/admin/orders/{order}/confirm-final-payment', [AdminController::class, 'confirmFinalPayment'])
        ->name('admin.orders.confirmFinalPayment');
   ```

5. **Update AdminController::confirmPayment():**
   - Cek apakah order pakai DP
   - Jika DP dan phase saat ini adalah 'dp': update Order.payment_status = 'dp', Order.status = 'processing', tampilkan notif "DP dikonfirmasi, menunggu pelunasan"
   - Jika DP dan phase saat ini adalah 'final': update Order.payment_status = 'paid', Order.status = 'processing'
   - Jika bukan DP (full): update seperti sekarang

6. **Update view order detail pelanggan:**
   - Tampilkan info DP: "DP Rp X sudah dikonfirmasi. Sisa pelunasan: Rp Y"
   - Tampilkan tombol "Upload Bukti Pelunasan" jika payment_status = 'dp'
   - Tampilkan status "Lunas" jika payment_status = 'paid'

7. **Update view admin order detail:**
   - Tampilkan riwayat semua payment (DP + pelunasan) dengan badge phase
   - Tombol konfirmasi hanya muncul jika ada bukti yang belum dikonfirmasi

8. **Sinkronkan Order.payment_status:**
   - Pastikan setiap kali Payment status berubah, Order.payment_status ikut update
   - Bisa pakai Eloquent Observer: `PaymentObserver::updated()`

Setelah selesai, test full flow DP:
- Buat order dengan produk yang ada DP
- Upload bukti DP → admin konfirmasi → status berubah ke "DP"
- Upload bukti pelunasan → admin konfirmasi → status berubah ke "Paid"

Commit:
```
git add -A && git commit -m "feat(K3): implementasi alur pembayaran DP dua fase (dp + pelunasan)"
```

---

---
# SETELAH SEMUA PATCH SELESAI
---

Jalankan final check:

```
Lakukan pengecekan akhir:
1. php artisan route:list — pastikan tidak ada route conflict
2. php artisan migrate:status — pastikan semua migration sudah jalan
3. Buka setiap halaman utama di browser dan cek tidak ada error
4. Login sebagai customer → tambah ke cart (tanpa login) → checkout → order → upload bukti
5. Login sebagai admin → konfirmasi pembayaran → approve ulasan

Buat final commit:
git add -A && git commit -m "chore: full patch K1-K7 selesai, project ready for demo"
```
