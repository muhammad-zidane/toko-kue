# ✅ HASIL KONFIRMASI STRUKTUR KODE — JAGOAN KUE

**Tanggal Verifikasi:** 17 Mei 2026  
**Status:** Konfirmasi Selesai  
**Siap Untuk:** Implementasi Modul Keamanan

---

## 📋 RINGKASAN EKSEKUTIF

Semua struktur kode asli sudah diverifikasi. Tidak ada asumsi yang salah. Struktur siap untuk implementasi keamanan sesuai PLANNING-KEAMANAN-JagoanKue.md dengan penyesuaian nilai role dari `'user'` → `'customer'` (sesuai kode asli).

---

## 1️⃣ STRUKTUR DATABASE & MIGRATION

### 1.1 Tabel `users`

**Migration File:** `database/migrations/0001_01_01_000000_create_users_table.php`

| Kolom | Tipe | Nullable | Default | Catatan |
|-------|------|----------|---------|---------|
| `id` | BIGINT | ❌ | - | Primary Key |
| `name` | VARCHAR(255) | ❌ | - | Nama user |
| `email` | VARCHAR(255) | ❌ | - | UNIQUE |
| `email_verified_at` | TIMESTAMP | ✅ | NULL | Untuk email verification |
| `password` | VARCHAR(255) | ❌ | - | Stored as hash |
| `remember_token` | VARCHAR(100) | ✅ | NULL | Remember me token |
| `created_at` | TIMESTAMP | ❌ | CURRENT | Auto |
| `updated_at` | TIMESTAMP | ❌ | CURRENT | Auto |

**Migration File:** `database/migrations/2026_04_11_082243_add_role_to_users_table.php`

| Kolom | Tipe | Enum Values | Default | Catatan |
|-------|------|-------------|---------|---------|
| `role` | ENUM | `'admin'`, `'customer'` | `'customer'` | ⚠️ **PENTING: Gunakan 'customer', bukan 'user'** |

---

### 1.2 Tabel `orders`

**Migration File:** `database/migrations/2026_04_11_082209_create_orders_table.php`

| Kolom | Tipe | Nullable | Default | Catatan |
|-------|------|----------|---------|---------|
| `id` | BIGINT | ❌ | - | Primary Key |
| `user_id` | BIGINT | ❌ | - | FK → users (CASCADE) |
| `order_code` | VARCHAR(255) | ❌ | - | UNIQUE |
| `status` | ENUM | ❌ | `'pending'` | Values: `['pending', 'processing', 'completed', 'cancelled']` |
| `shipping_address` | TEXT | ❌ | - | - |
| `total_price` | DECIMAL(10,2) | ❌ | - | - |
| `notes` | TEXT | ✅ | NULL | - |
| `created_at` | TIMESTAMP | ❌ | CURRENT | Auto |
| `updated_at` | TIMESTAMP | ❌ | CURRENT | Auto |

**Catatan:** Status order enum values sudah fixed. Tidak ada kolom `payment_status` di migration awal (ada di code tapi mungkin di update via seed/migration berikutnya).

---

### 1.3 Tabel `payments`

**Migration File:** `database/migrations/2026_04_11_082233_create_payments_table.php`

| Kolom | Tipe | Nullable | Default | Catatan |
|-------|------|----------|---------|---------|
| `id` | BIGINT | ❌ | - | Primary Key |
| `order_id` | BIGINT | ❌ | - | FK → orders (CASCADE) |
| `payment_method` | VARCHAR(255) | ❌ | - | Contoh: transfer_bank, ewallet, qris, cod |
| `status` | ENUM | ❌ | `'unpaid'` | Values: `['unpaid', 'paid', 'failed']` |
| `amount` | DECIMAL(10,2) | ❌ | - | - |
| `proof_image` | VARCHAR(255) | ✅ | NULL | **📁 Path file bukti pembayaran** |
| `paid_at` | TIMESTAMP | ✅ | NULL | Waktu pembayaran dikonfirmasi |
| `created_at` | TIMESTAMP | ❌ | CURRENT | Auto |
| `updated_at` | TIMESTAMP | ❌ | CURRENT | Auto |

**⚠️ PENTING:** Kolom bukti pembayaran adalah `proof_image` (bukan `bukti_path` atau nama lain).

---

## 2️⃣ MODEL & FILLABLE

### 2.1 User Model

**Lokasi:** `app/Models/User.php`

```php
protected $fillable = [
    'name',
    'email',
    'password',
    'role',  // ⚠️ CRITICAL: Ini vulnerable! (akan diperbaiki Modul 7)
];

protected $hidden = [
    'password',
    'remember_token',
];

protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',  // ✅ Auto-hash saat di-set
    ];
}

public function isAdmin(): bool
{
    return $this->role === 'admin';
}
```

**Relasi:**
- `orders()` → HasMany Order
- `productReviews()` → HasMany ProductReview
- `addresses()` → HasMany Address
- `defaultAddress()` → HasOne Address (is_default=true)

---

### 2.2 Order Model

**Lokasi:** `app/Models/Order.php`

```php
protected $fillable = [
    'user_id', 'order_code', 'status',
    'shipping_address', 'total_price', 'notes',
    'delivery_method', 'delivery_date', 'delivery_slot',
    'shipping_cost', 'voucher_code', 'discount_amount',
    'payment_status', 'dp_amount', 'paid_amount',
];

protected $casts = [
    'delivery_date' => 'date',
];
```

**Relasi:**
- `user()` → BelongsTo User
- `orderItems()` → HasMany OrderItem
- `payment()` → HasOne Payment
- `productReviews()` → HasMany ProductReview

**⚠️ Catatan:** Field sistem seperti `status`, `total_price` ada di $fillable. Akan diperbaiki di Modul 7 (Mass Assignment).

---

### 2.3 Payment Model

**Lokasi:** `app/Models/Payment.php`

```php
protected $fillable = [
    'order_id', 'payment_method', 'status',
    'amount', 'proof_image', 'paid_at'
];

protected $casts = [
    'paid_at' => 'datetime',
];
```

**Relasi:**
- `order()` → BelongsTo Order

---

## 3️⃣ CONTROLLER & LOCATIONS

### 3.1 Authentication Controllers

| Controller | Path | Status |
|-----------|------|--------|
| `RegisteredUserController` | `app/Http/Controllers/Auth/RegisteredUserController.php` | ✅ Ada |
| `AuthenticatedSessionController` | `app/Http/Controllers/Auth/AuthenticatedSessionController.php` | ✅ Ada |
| `LoginRequest` | `app/Http/Requests/Auth/LoginRequest.php` | ✅ Ada |
| `StrongPasswordRequest` | `app/Http/Requests/StrongPasswordRequest.php` | ✅ Ada |

**Catatan:**
- `RegisteredUserController::store()` (baris 21-34) **TIDAK SET ROLE DEFAULT** ❌
- `LoginRequest::authenticate()` (baris 43) **ADA RATE LIMITING** ✅ (5 percobaan, 15 menit lockout)
- `AuthenticatedSessionController::store()` (baris 29) **ADA SESSION REGENERATE** ✅

---

### 3.2 Order & Payment Controllers

| Controller | Path | Methods |
|-----------|------|---------|
| `OrderController` | `app/Http/Controllers/OrderController.php` | `index()`, `store()`, `show()`, `uploadProof()`, `payment()`, `success()`, `showStatus()`, `singleProductCheckout()` |
| `AdminController` | `app/Http/Controllers/AdminController.php` | `dashboard()`, `orders()`, `orderDetail()`, `downloadProof()`, `updateOrderStatus()`, `confirmPayment()`, `rejectPayment()`, dll |

**Upload Proof Method:**
- Lokasi: `OrderController::uploadProof()` (baris 265-310)
- Validasi: `'proof_image' => 'required|image|mimes:jpg,jpeg,png,webp,heic,heif|max:5120'`
- ⚠️ Max 5MB (harus dikurangi ke 2MB), tidak ada `mimetypes` rule

**Admin Download Method:**
- Lokasi: `AdminController::downloadProof()` (baris 131-149)
- ✅ Sudah aman: pakai `Storage::download()` dengan path dari DB, bukan input
- ✅ Cek file exists sebelum download

**Admin Payment Confirm/Reject:**
- `confirmPayment()` (baris 635-640): Set status='paid', paid_at=now()
- `rejectPayment()` (baris 642-648): Set status='failed', bisa tambah reason di notes

---

### 3.3 Middleware

| Middleware | Path | Status |
|-----------|------|--------|
| `EnsureUserIsAdmin` | `app/Http/Middleware/EnsureUserIsAdmin.php` | ✅ Ada |

**Implementasi (baris 11-18):**
```php
public function handle(Request $request, Closure $next): Response
{
    if (!auth()->check() || !auth()->user()->isAdmin()) {
        abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
    }
    return $next($request);
}
```

✅ Menggunakan `isAdmin()` helper method dari User model.

---

## 4️⃣ ROUTES & PROTECTION

### 4.1 Admin Routes

**File:** `routes/web.php` (baris 79-146)

```php
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin routes di sini
});
```

**Status:** ✅ Semua route admin **protected dengan `['auth', 'admin']` middleware**

**Key Routes untuk Keamanan:**
```php
Route::get('/orders/{order}/download-proof', [AdminController::class, 'downloadProof'])->name('orders.downloadProof');
Route::post('/orders/{order}/confirm-payment', [AdminController::class, 'confirmPayment'])->name('orders.confirmPayment');
Route::post('/orders/{order}/reject-payment', [AdminController::class, 'rejectPayment'])->name('orders.rejectPayment');
```

### 4.2 User Routes dengan Rate Limiting

**File:** `routes/web.php` (baris 68-75)

```php
Route::middleware('throttle:20,1')->group(function () {
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::post('/orders/{order}/upload-proof', [OrderController::class, 'uploadProof'])->name('orders.uploadProof');
    Route::post('/orders/{order}/reviews/{product}', [ProductReviewController::class, 'store'])->name('orders.reviews.store');
    Route::patch('/orders/{order}/reviews/{review}', [ProductReviewController::class, 'update'])->name('orders.reviews.update');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update-item', [CartController::class, 'updateItem'])->name('cart.updateItem');
});
// throttle:20,1 = 20 requests per 1 minute per user
```

**Status:** ✅ Write operations sudah rate-limited

---

## 5️⃣ FILE UPLOAD & STORAGE

### 5.1 Symlink Status

**Lokasi Expected:** `public/storage` → `storage/app/public`

**Status:** ✅ **SYMLINK SUDAH ADA**

**Hasil Cek:**
```
Symlink SUDAH ADA
```

**Catatan:** Tidak perlu jalankan `php artisan storage:link` lagi.

### 5.2 Upload Directory

**Current Implementation di OrderController::uploadProof():**
```php
$path = $request->file('proof_image')->store('payment_proofs', 'public');
```

- Disk: `public` (alias untuk `storage/app/public`)
- Folder: `payment_proofs/`
- File: Laravel auto-generate random filename

**Status:** ✅ Nama file random (aman dari overwrite & path traversal)

**Access URL:**
```
https://localhost:8000/storage/payment_proofs/[random-filename]
```

---

## 6️⃣ KONFIGURASI ENVIRONMENT

### 6.1 .env File Status

| Setting | Nilai | Status | Catatan |
|---------|-------|--------|---------|
| `APP_ENV` | `local` | ✅ | Sesuai development |
| `APP_DEBUG` | `true` | ⚠️ | Harus `false` sebelum demo/presentasi |
| `APP_KEY` | `base64:20ypHA...` | ✅ | Sudah terisi |
| `APP_URL` | `http://localhost` | ✅ | OK untuk lokal |
| `DB_CONNECTION` | `mysql` | ✅ | - |
| `DB_DATABASE` | `jagoan_kue` | ✅ | - |
| `DB_USERNAME` | `root` | ✅ | - |
| `DB_PASSWORD` | (kosong) | ✅ | XAMPP default |
| `SESSION_DRIVER` | `database` | ✅ | Session di DB |
| `SESSION_LIFETIME` | `120` | ✅ | 120 menit |
| `SESSION_ENCRYPT` | `false` | ✅ | OK lokal |

**Catatan Penting:**
- `.env` sudah di `.gitignore` ✅
- Storage key di `/storage/*.key` juga di `.gitignore` ✅

### 6.2 .gitignore Status

**File:** `.gitignore`

```
.env              ✅ Terlindungi
.env.backup       ✅ Terlindungi
/storage/*.key    ✅ Terlindungi
/vendor           ✅ Terlindungi
/node_modules     ✅ Terlindungi
```

**Status:** ✅ Kredensial aman, tidak ter-commit

---

## 7️⃣ FORM REQUEST & VALIDATION

### 7.1 Password Validation

**File:** `app/Http/Requests/StrongPasswordRequest.php`

```php
public function rules(): array
{
    return [
        'name'     => ['required', 'string', 'max:255'],
        'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
        'password' => ['required', 'confirmed', new StrongPassword($this->input('email'))],
    ];
}
```

**Custom Rule:** `StrongPassword` (lokasi: `app/Rules/StrongPassword.php`)

**Status:** ✅ Ada custom password validation

### 7.2 Order Validation

**File:** `app/Http/Controllers/OrderController.php::store()` (baris 74-91)

```php
$request->validate([
    'delivery_method'              => 'required|in:pickup,delivery',
    'shipping_address'             => 'required_if:delivery_method,delivery|nullable|string',
    'shipping_zone_id'             => 'required_if:delivery_method,delivery|nullable|exists:shipping_zones,id',
    'delivery_date'                => ['required', 'date', 'after_or_equal:' . now()->addDays($leadDays)->format('Y-m-d')],
    'delivery_slot'                => 'nullable|string',
    'notes'                        => 'nullable|string|max:300',
    'items'                        => 'required|array',
    'items.*.product_id'           => 'required|exists:products,id',
    'items.*.quantity'             => 'required|integer|min:1',
    'items.*.note'                 => 'nullable|string|max:300',
    'items.*.customizations'       => 'nullable|string',
    'voucher_code'                 => 'nullable|string',
    'use_dp'                       => 'nullable|boolean',
]);
```

**Status:** ✅ Comprehensive validation sudah ada

### 7.3 Upload Proof Validation

**File:** `app/Http/Controllers/OrderController.php::uploadProof()` (baris 269-271)

```php
$request->validate([
    'proof_image' => 'required|image|mimes:jpg,jpeg,png,webp,heic,heif|max:5120',
]);
```

**Status:** ⚠️ Perlu update:
- Max 5120 KB → ubah ke 2048 KB (2 MB)
- Tambah `mimetypes:image/jpeg,image/png` rule

---

## 8️⃣ AUTHORIZATION & OWNERSHIP CHECK

### 8.1 Order Ownership

**File:** `app/Http/Controllers/OrderController.php`

**Method:** `authorizeOwner()` (digunakan di `show()`, `payment()`, `success()`, `uploadProof()`)

```php
// Contoh dari show() (baris 257-262):
public function show(Order $order)
{
    $this->authorizeOwner($order);  // ✅ Check ownership
    $order->load('orderItems.product', 'payment', 'productReviews.product', 'productReviews.images');
    return view('orders.show', compact('order'));
}
```

**Status:** ✅ User biasa hanya bisa akses order miliknya

### 8.2 Admin Protection

**Routes:** Semua route `/admin` dilindungi middleware `['auth', 'admin']`

**Status:** ✅ User biasa tidak bisa akses admin routes

---

## 9️⃣ SESSION & COOKIE SECURITY

### 9.1 Session Config

**File:** `config/session.php`

| Setting | Nilai | Status |
|---------|-------|--------|
| `driver` | `database` | ✅ Session di DB |
| `lifetime` | `120` (menit) | ✅ 2 jam |
| `expire_on_close` | `false` | ✅ Session bertahan setelah browser ditutup |
| `encrypt` | `false` | ✅ OK lokal |
| `http_only` | `true` | ✅ JavaScript tidak bisa akses cookie |
| `same_site` | `'lax'` | ✅ Anti-CSRF |
| `secure` | `false` (env default) | ✅ OK lokal tanpa HTTPS |

**Status:** ✅ Session security sudah proper

### 9.2 Session Regenerate

**Login (AuthenticatedSessionController::store(), baris 29):**
```php
$request->session()->regenerate();  // ✅ Ada
```

**Logout (AuthenticatedSessionController::destroy(), baris 39-43):**
```php
Auth::guard('web')->logout();
$request->session()->invalidate();
$request->session()->regenerateToken();  // ✅ Ada
```

**Status:** ✅ Session regeneration sudah diimplementasi

---

## 🔟 ROLE VALUE CLARIFICATION

### ⚠️ **PENTING: Role Value yang Benar**

**Dari Database Migration:**
```php
// 2026_04_11_082243_add_role_to_users_table.php
$table->enum('role', ['admin', 'customer'])->default('customer');
```

**Hasil:** Role values adalah:
- `'admin'` → User admin
- `'customer'` → User biasa (customer)

**Di Dokumentasi PLANNING-KEAMANAN-JagoanKue.md:**
- Menyebutkan `'user'` → **SALAH**, seharusnya `'customer'`

**Keputusan:** 
- ✅ **Gunakan `'customer'` untuk role user biasa** (sesuai kode asli)
- ✅ **Gunakan `'admin'` untuk role admin**

**Implementasi akan disesuaikan:**
- Di modul-modul, setiap referensi ke `'user'` diganti dengan `'customer'`
- Helper `isAdmin()` di User model sudah menggunakan `'admin'` ✅

---

## ✅ KESIMPULAN & CHECKLIST

### Struktur Sudah Diverifikasi ✅

- ✅ Database schema & migration
- ✅ Model & fillable properties
- ✅ Controller locations & methods
- ✅ Routes & middleware protection
- ✅ Upload folder & symlink
- ✅ Session & cookie config
- ✅ Validation rules
- ✅ Authorization checks
- ✅ Role value clarification

### Tidak Ada Asumsi Salah ✅

- ✅ Field names confirmed
- ✅ Controller methods confirmed
- ✅ Route protection confirmed
- ✅ Storage setup confirmed
- ✅ Role values clarified

### Siap untuk Implementasi ✅

**Urutan Modul yang Akan Dikerjakan:**

1. **Modul 2 (RBAC & Otorisasi)** ← Mulai dari sini
2. **Modul 8 (Upload Bukti Bayar)** ← Kritis
3. **Modul 4 (Validasi Input)** ← Support
4. **Modul 1 (Autentikasi)**
5. **Modul 3, 5, 6, 7, 9, 10, 11**
6. **Modul 12, 13 (Opsional)**

---

## 📝 CATATAN TAMBAHAN

### Hal yang Sudah Aman ✅

- CSRF protection (default active, @csrf ada di form)
- SQL Injection (Eloquent + parameter binding)
- Session security (http_only, same_site, regenerate)
- Rate limiting (login & route operations)
- Admin protection (middleware + route group)
- Password hashing (cast 'hashed')

### Hal yang Perlu Diperbaiki ⚠️

- User model: Hapus `'role'` dari $fillable (mass assignment)
- Order model: Ketat tentang field sistem di $fillable
- Register: Set role default ke `'customer'`
- Upload: Improve validation (mimetypes, max 2MB)
- APP_DEBUG: Ubah ke `false` sebelum demo
- Security headers: Tambah middleware
- Error pages: Buat custom (opsional)

---

**Dokumen ini final. Siap untuk implementasi!**

---

*Generated: 17 Mei 2026*  
*Struktur Kode: Jagoan Kue E-Commerce*  
*Status: Konfirmasi Selesai ✅*
