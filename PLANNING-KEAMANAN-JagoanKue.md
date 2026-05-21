# 🔐 Planning Pengecekan & Implementasi Keamanan — Jagoan Kue

> Dokumen perencanaan keamanan untuk aplikasi e-commerce pemesanan kue **Jagoan Kue**
> (Laravel + Laravel Breeze). Disusun agar bisa dikerjakan langsung oleh **junior programmer**
> atau **AI assistant** tanpa perlu banyak konteks tambahan.

---

## 0. Tentang Dokumen Ini

### 0.1. Konteks Proyek

| Item | Keterangan |
|---|---|
| Nama aplikasi | Jagoan Kue (e-commerce pemesanan kue) |
| Framework | Laravel + Laravel Breeze (auth scaffolding) |
| Database | MySQL (XAMPP) — tabel: `categories`, `products`, `orders`, `order_items`, `payments`, `users` |
| Metode pembayaran | Transfer bank, e-wallet, QRIS, COD — **semua pakai upload bukti manual** (tidak ada payment gateway) |
| Target deployment | **Lokal saja** (tugas kuliah, tidak online ke publik) |
| Tujuan dokumen | Audit keamanan kode yang sudah ada **+** panduan implementasi step-by-step |

### 0.2. Batasan Scope (PENTING — baca dulu)

Karena ini **proyek kuliah lokal** dan **tidak ada payment gateway asli**, prioritas keamanan disesuaikan:

- ✅ **FOKUS UTAMA:** keamanan upload file bukti pembayaran, validasi input, RBAC admin vs user, CSRF, XSS, SQL Injection, mass assignment. Ini yang paling mungkin jadi pertanyaan dosen/penguji.
- 🟡 **DIBAHAS TAPI OPSIONAL:** HTTPS lokal, security headers, audit logging. Bagus untuk nilai tambah, tapi bukan blocker.
- ❌ **TIDAK PERLU:** PCI-DSS, enkripsi data kartu kredit, integrasi gateway aman, IP whitelisting produksi. Tidak relevan untuk proyek ini.

### 0.3. Cara Membaca Dokumen Ini

Setiap modul keamanan punya struktur yang **selalu sama**:

| Bagian | Isi |
|---|---|
| 🎯 **Tujuan** | Kenapa fitur ini penting (1–2 kalimat) |
| 🔍 **Audit** | Checklist untuk mengecek kondisi kode SEKARANG (jawab ✅/❌) |
| 🛠️ **Implementasi** | Langkah konkret + contoh kode yang bisa langsung dicopy |
| ✅ **Verifikasi** | Cara membuktikan fitur sudah berfungsi (cara test manual) |
| ⚠️ **Kesalahan Umum** | Hal yang sering salah, supaya tidak terjebak |

**Legend status** (isi di kolom checklist saat audit):

- `[ ]` = belum dicek / belum dikerjakan
- `[x]` = sudah aman / sudah selesai
- `[!]` = ada masalah, perlu diperbaiki

### 0.4. Catatan untuk yang Mengerjakan (Junior / AI)

1. **Kerjakan berurutan** dari Modul 1 ke Modul 13. Prioritas tinggi ada di depan.
2. **Jangan skip bagian Audit.** Cek dulu kode yang ada sebelum menambah kode baru — bisa jadi sebagian sudah aman dari bawaan Breeze.
3. **Tes setiap selesai 1 modul**, jangan menumpuk sampai akhir.
4. Semua perintah terminal dijalankan di **root folder proyek Laravel** (folder yang ada `artisan`).
5. Kalau ragu, jalankan dulu di lokal dan cek apakah aplikasi masih jalan normal.

---

## 1. Ringkasan Prioritas

| # | Modul | Prioritas | Estimasi Waktu | Sudah ada di Breeze? |
|---|---|---|---|---|
| 1 | Autentikasi & Password | 🔴 Tinggi | 30 mnt (audit) | ✅ Sebagian besar |
| 2 | Otorisasi & RBAC (admin/user) | 🔴 Tinggi | 2–3 jam | ❌ Perlu dibuat |
| 3 | CSRF Protection | 🔴 Tinggi | 30 mnt | ✅ Aktif default |
| 4 | Validasi Input (server-side) | 🔴 Tinggi | 2–3 jam | ❌ Perlu dilengkapi |
| 5 | XSS Prevention | 🔴 Tinggi | 1 jam | ✅ Sebagian (Blade) |
| 6 | SQL Injection Prevention | 🔴 Tinggi | 1 jam | ✅ Jika pakai Eloquent |
| 7 | Mass Assignment Protection | 🔴 Tinggi | 1 jam | ❌ Perlu dicek manual |
| 8 | **Keamanan Upload Bukti Bayar** | 🔴 **Sangat Tinggi** | 2–3 jam | ❌ Perlu dibuat |
| 9 | Session & Cookie Security | 🟡 Sedang | 30 mnt | ✅ Sebagian |
| 10 | Rate Limiting (anti brute-force) | 🟡 Sedang | 1 jam | ✅ Login throttle default |
| 11 | Error Handling | 🟡 Sedang | 30 mnt | ✅ Default (cek config) |
| 12 | Audit Logging (aktivitas admin) | 🟢 Rendah | 2 jam | ❌ Opsional |
| 13 | Environment & Security Headers | 🟢 Rendah | 1 jam | ❌ Opsional |

---

## 2. Modul 1 — Autentikasi & Manajemen Password

### 🎯 Tujuan
Memastikan password disimpan aman (di-hash, bukan teks polos) dan proses login/register tidak bisa dibobol dengan mudah.

### 🔍 Audit

- [ ] Buka `database` (phpMyAdmin) → tabel `users` → kolom `password`. Pastikan isinya berupa hash panjang (diawali `$2y$...`), **bukan** teks asli.
- [ ] Buka model `app/Models/User.php`. Pastikan ada cast `'password' => 'hashed'` ATAU controller meng-hash manual.
- [ ] Cek `RegisteredUserController` (folder `app/Http/Controllers/Auth/`): apakah ada aturan minimal panjang password?
- [ ] Coba register dengan password `123`. Apakah ditolak? (Seharusnya ditolak.)

### 🛠️ Implementasi

**Langkah 1 — Pastikan password di-hash otomatis.** Di `app/Models/User.php`:

```php
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // <- WAJIB ada. Auto-hash saat password di-set.
    ];
}
```

**Langkah 2 — Perketat aturan password saat register.** Di `RegisteredUserController@store`:

```php
use Illuminate\Validation\Rules\Password;

$request->validate([
    'name'     => ['required', 'string', 'max:255'],
    'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
    'password' => [
        'required',
        'confirmed',
        Password::min(8)        // minimal 8 karakter
            ->letters()         // wajib ada huruf
            ->numbers(),        // wajib ada angka
        // ->mixedCase()       // (opsional) wajib huruf besar & kecil
        // ->symbols()         // (opsional) wajib simbol
    ],
]);
```

**Langkah 3 — Pastikan role default user baru = `user` (bukan `admin`).** Di method `store`:

```php
$user = User::create([
    'name'     => $request->name,
    'email'    => $request->email,
    'password' => $request->password, // otomatis di-hash karena cast 'hashed'
    'role'     => 'user',             // <- HARDCODE. Jangan ambil dari $request.
]);
```

> ⚠️ Jangan pernah menulis `'role' => $request->role`. User bisa kirim `role=admin` lewat form/Postman → langsung jadi admin. Ini celah **mass assignment** (lihat Modul 7).

### ✅ Verifikasi

1. Register user baru → cek di phpMyAdmin: kolom `password` berupa hash, kolom `role` = `user`.
2. Coba register password `abc` → harus ditolak dengan pesan validasi.
3. Login dengan password benar → berhasil. Password salah → ditolak.

### ⚠️ Kesalahan Umum
- Meng-hash password 2x (sudah pakai cast `hashed` + masih `Hash::make()` manual di controller) → login selalu gagal. Pilih salah satu saja.
- Mengirim field `role` dari form register.

---

## 3. Modul 2 — Otorisasi & RBAC (Admin vs User)

### 🎯 Tujuan
Memastikan user biasa **tidak bisa** mengakses halaman/aksi admin (dashboard, kelola produk, ubah status pesanan). Ini celah paling sering ditanya penguji.

### 🔍 Audit

- [ ] Apakah ada kolom `role` di tabel `users`? (Memori proyek: ada.)
- [ ] Login sebagai user biasa, lalu ketik manual URL admin di browser (misal `/admin/dashboard`). Apakah bisa masuk? (Seharusnya **TIDAK** bisa.)
- [ ] Apakah route admin dilindungi middleware? Cek `routes/web.php`.

### 🛠️ Implementasi

**Langkah 1 — Buat middleware role.**

```bash
php artisan make:middleware EnsureUserIsAdmin
```

Isi file `app/Http/Middleware/EnsureUserIsAdmin.php`:

```php
public function handle(Request $request, Closure $next): Response
{
    if (! auth()->check() || auth()->user()->role !== 'admin') {
        abort(403, 'Akses ditolak. Halaman khusus admin.');
    }
    return $next($request);
}
```

**Langkah 2 — Daftarkan alias middleware.** Di `bootstrap/app.php` (Laravel 11/12):

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
    ]);
})
```

> Catatan: untuk Laravel 10 ke bawah, daftarkan di `app/Http/Kernel.php` pada array `$middlewareAliases`.

**Langkah 3 — Lindungi semua route admin.** Di `routes/web.php`:

```php
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('products', AdminProductController::class);
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.status');
    // dst...
});
```

**Langkah 4 — Sembunyikan menu admin di tampilan user.** Di file Blade:

```blade
@auth
    @if (auth()->user()->role === 'admin')
        <a href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
    @endif
@endauth
```

> ⚠️ Menyembunyikan tombol **TIDAK CUKUP**. Proteksi route (Langkah 3) tetap WAJIB, karena user bisa akses URL langsung tanpa lewat tombol.

**Langkah 5 — Pastikan user hanya bisa lihat pesanannya sendiri.** Contoh di `OrderController@show`:

```php
public function show(Order $order)
{
    // User biasa hanya boleh lihat order miliknya
    if (auth()->user()->role !== 'admin' && $order->user_id !== auth()->id()) {
        abort(403);
    }
    return view('orders.show', compact('order'));
}
```

### ✅ Verifikasi

1. Login sebagai **user biasa** → ketik `/admin/dashboard` di URL → harus muncul **403 Forbidden**.
2. Login sebagai **admin** → buka `/admin/dashboard` → harus berhasil.
3. Login sebagai user A → coba buka URL detail pesanan milik user B (misal `/orders/15`) → harus **403**.
4. Logout → akses `/admin/dashboard` → harus redirect ke login.

### ⚠️ Kesalahan Umum
- Hanya menyembunyikan tombol di Blade tanpa proteksi route → user tetap bisa akses lewat URL.
- Cek role di controller satu-satu tapi lupa di salah satu method.

---

## 4. Modul 3 — CSRF Protection

### 🎯 Tujuan
Mencegah situs lain mengirim request berbahaya atas nama user yang sedang login (misal mengubah pesanan tanpa sepengetahuan user).

### 🔍 Audit

- [ ] Buka semua form di Blade (login, register, form pemesanan, upload bukti, edit profil). Apakah ada `@csrf` di dalam tiap `<form>`?
- [ ] Coba submit form tanpa token (hapus `@csrf` sementara) → harus muncul error **419 Page Expired**.

### 🛠️ Implementasi

Laravel mengaktifkan CSRF secara **default**. Yang perlu dipastikan: setiap form POST/PUT/PATCH/DELETE punya token.

**Form HTML biasa:**

```blade
<form method="POST" action="{{ route('orders.store') }}" enctype="multipart/form-data">
    @csrf
    <!-- input lain -->
    <button type="submit">Pesan Sekarang</button>
</form>
```

**Form dengan method selain POST (PUT/PATCH/DELETE):**

```blade
<form method="POST" action="{{ route('orders.update', $order) }}">
    @csrf
    @method('PUT')
    <!-- ... -->
</form>
```

**Kalau pakai AJAX/fetch**, kirim token lewat header. Tambahkan di `<head>` layout:

```blade
<meta name="csrf-token" content="{{ csrf_token() }}">
```

Lalu di JavaScript:

```javascript
fetch('/orders/store', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/json',
    },
    body: JSON.stringify(data),
});
```

### ✅ Verifikasi
1. Submit form normal → berhasil.
2. Hapus `@csrf` dari satu form (sementara, untuk tes) → submit → muncul **419**. Kembalikan `@csrf` setelah tes.

### ⚠️ Kesalahan Umum
- Lupa `@csrf` di form upload bukti pembayaran.
- Menonaktifkan CSRF middleware demi "biar gampang" — **jangan**, ini melemahkan keamanan inti.

---

## 5. Modul 4 — Validasi Input (Server-Side)

### 🎯 Tujuan
Memastikan **semua** data dari user divalidasi di server, bukan hanya di browser (HTML `required` / JavaScript bisa di-bypass dengan mudah lewat Postman).

### 🔍 Audit

- [ ] Buka setiap controller yang menerima input (`store`, `update`). Apakah ada `$request->validate([...])` atau Form Request?
- [ ] Coba kirim form pemesanan dengan field kosong lewat browser (matikan JS) atau Postman. Apakah server menolak?
- [ ] Coba kirim `jumlah = -5` atau `jumlah = abc` pada pemesanan. Apakah ditolak?

### 🛠️ Implementasi

**Cara cepat — validasi langsung di controller:**

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'product_id'      => ['required', 'integer', 'exists:products,id'],
        'jumlah'          => ['required', 'integer', 'min:1', 'max:100'],
        'nama_penerima'   => ['required', 'string', 'max:255'],
        'alamat'          => ['required', 'string', 'max:500'],
        'no_hp'           => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
        'metode_bayar'    => ['required', 'in:transfer,ewallet,qris,cod'],
        'catatan'         => ['nullable', 'string', 'max:500'],
    ]);

    // Gunakan $validated, JANGAN $request->all()
    Order::create([
        'user_id'       => auth()->id(),
        'product_id'    => $validated['product_id'],
        'jumlah'        => $validated['jumlah'],
        // ... dst
    ]);
}
```

**Cara rapi — pakai Form Request (disarankan untuk form besar seperti pemesanan):**

```bash
php artisan make:request StoreOrderRequest
```

Isi `app/Http/Requests/StoreOrderRequest.php`:

```php
public function authorize(): bool
{
    return auth()->check(); // hanya user login boleh memesan
}

public function rules(): array
{
    return [
        'product_id'    => ['required', 'integer', 'exists:products,id'],
        'jumlah'        => ['required', 'integer', 'min:1', 'max:100'],
        'nama_penerima' => ['required', 'string', 'max:255'],
        'alamat'        => ['required', 'string', 'max:500'],
        'no_hp'         => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
        'metode_bayar'  => ['required', 'in:transfer,ewallet,qris,cod'],
    ];
}

public function messages(): array
{
    return [
        'jumlah.min'      => 'Jumlah pesanan minimal 1.',
        'no_hp.regex'     => 'Nomor HP harus berupa angka 10–15 digit.',
        'metode_bayar.in' => 'Metode pembayaran tidak valid.',
    ];
}
```

Pakai di controller:

```php
public function store(StoreOrderRequest $request)
{
    $validated = $request->validated(); // sudah pasti aman & tervalidasi
    // ...
}
```

**Aturan validasi penting yang sering dilupakan:**

| Field | Aturan | Alasan |
|---|---|---|
| ID relasi | `exists:tabel,kolom` | Cegah input ID produk/user palsu |
| Angka jumlah | `integer, min:1` | Cegah jumlah negatif / nol / teks |
| Pilihan tetap | `in:a,b,c` | Cegah nilai metode bayar/status liar |
| Email | `email` | Format valid |
| Teks bebas | `max:255` (atau sesuai kolom DB) | Cegah error "data too long" & abuse |
| Harga (jika input) | `numeric, min:0` | Cegah harga negatif |

### ✅ Verifikasi
1. Kirim form pemesanan dengan `jumlah = -1` lewat Postman → ditolak (status 422).
2. Kirim `product_id = 99999` (tidak ada) → ditolak.
3. Kirim `metode_bayar = bitcoin` → ditolak.
4. Form valid → berhasil.

### ⚠️ Kesalahan Umum
- Validasi hanya di JavaScript / atribut HTML `required` → mudah di-bypass via Postman.
- Pakai `$request->all()` saat menyimpan, bukan `$validated` → data tidak tervalidasi ikut tersimpan.

---

## 6. Modul 5 — XSS Prevention (Cross-Site Scripting)

### 🎯 Tujuan
Mencegah penyerang menyisipkan script jahat (misal lewat nama, alamat, atau catatan pesanan) yang akan tereksekusi saat halaman dibuka admin/user lain.

### 🔍 Audit

- [ ] Cari di seluruh Blade penggunaan `{!! ... !!}` (cetak tanpa escape). Setiap kemunculan harus dipastikan aman.
- [ ] Tes: pesan kue dengan nama penerima `<script>alert('XSS')</script>`. Buka halaman riwayat/admin. Apakah muncul popup? (Seharusnya **tidak** — harusnya tampil sebagai teks biasa.)

### 🛠️ Implementasi

**Aturan 1 — SELALU gunakan `{{ }}`, bukan `{!! !!}`.** Blade `{{ }}` otomatis meng-escape HTML:

```blade
{{-- AMAN: script ditampilkan sebagai teks --}}
<p>Nama penerima: {{ $order->nama_penerima }}</p>
<p>Catatan: {{ $order->catatan }}</p>

{{-- BAHAYA: jangan begini untuk data dari user --}}
<p>{!! $order->catatan !!}</p>
```

**Aturan 2 — Jika benar-benar perlu render HTML dari user** (jarang di proyek ini), bersihkan dulu dengan library `mews/purifier`:

```bash
composer require mews/purifier
```

```blade
{!! clean($order->catatan) !!}
```

**Aturan 3 — Hati-hati menaruh data user di dalam atribut / JavaScript:**

```blade
{{-- Untuk atribut, tetap pakai {{ }} --}}
<input type="text" value="{{ old('nama_penerima') }}">

{{-- Untuk JS, gunakan @json --}}
<script>
    let namaUser = @json($user->name);
</script>
```

### ✅ Verifikasi
1. Buat pesanan dengan nama penerima: `<script>alert(1)</script>`.
2. Buka halaman riwayat pesanan & dashboard admin → **tidak boleh** muncul popup. Teks harus tampil apa adanya.
3. `Ctrl+U` (view source) → tag script harus tampil sebagai `&lt;script&gt;`.

### ⚠️ Kesalahan Umum
- Pakai `{!! !!}` untuk menampilkan catatan/alamat dari user.
- Memasukkan data user ke `onclick="..."` atau `<script>var x = '{{ $data }}'</script>` tanpa `@json`.

---

## 7. Modul 6 — SQL Injection Prevention

### 🎯 Tujuan
Mencegah penyerang memanipulasi query database lewat input (misal di kolom pencarian produk).

### 🔍 Audit

- [ ] Cari di seluruh kode penggunaan `DB::raw(`, `DB::select(`, `whereRaw(`, atau string query yang menyambung input langsung dengan `.` atau interpolasi `"$var"`.
- [ ] Khususnya cek fitur **pencarian produk** dan **filter kategori**.

### 🛠️ Implementasi

**Aturan 1 — Pakai Eloquent / Query Builder.** Keduanya otomatis aman (parameter binding):

```php
// AMAN
$products = Product::where('name', 'like', '%' . $request->keyword . '%')->get();

// AMAN
$products = DB::table('products')
    ->where('category_id', $request->category)
    ->get();
```

**Aturan 2 — Kalau TERPAKSA pakai raw query, gunakan binding (tanda `?`):**

```php
// AMAN — pakai binding
$products = DB::select(
    'SELECT * FROM products WHERE name LIKE ?',
    ['%' . $request->keyword . '%']
);

// ❌ BAHAYA — JANGAN PERNAH BEGINI
$products = DB::select(
    "SELECT * FROM products WHERE name LIKE '%" . $request->keyword . "%'"
);
```

**Aturan 3 — Validasi tetap dilakukan** (lihat Modul 4). Misal `category_id` divalidasi `exists:categories,id` supaya tidak bisa diisi sembarang.

### ✅ Verifikasi
1. Di kolom pencarian produk, ketik: `' OR '1'='1`
2. Hasil pencarian harus **kosong / normal**, aplikasi **tidak error**, dan **tidak** menampilkan semua produk.
3. Ketik: `'; DROP TABLE products; --` → aplikasi tetap normal, tabel `products` masih ada (cek phpMyAdmin).

### ⚠️ Kesalahan Umum
- Menyambung input ke string SQL untuk fitur search.
- Mengira validasi saja cukup tanpa binding (gunakan keduanya).

---

## 8. Modul 7 — Mass Assignment Protection

### 🎯 Tujuan
Mencegah user mengisi kolom yang tidak seharusnya (misal `role`, `is_admin`, `status`, `total_harga`) lewat manipulasi form/Postman.

### 🔍 Audit

- [ ] Buka setiap model di `app/Models/`. Apakah ada properti `$fillable` (atau `$guarded`)?
- [ ] Cari penggunaan `Model::create($request->all())` atau `$model->update($request->all())`. Ini berbahaya.
- [ ] Tes: kirim form register/edit profil sambil menambahkan field `role=admin` lewat Postman. Apakah role berubah jadi admin?

### 🛠️ Implementasi

**Langkah 1 — Definisikan `$fillable` di SEMUA model.** Contoh `app/Models/Order.php`:

```php
class Order extends Model
{
    // Hanya kolom ini yang boleh diisi massal
    protected $fillable = [
        'user_id',
        'nama_penerima',
        'alamat',
        'no_hp',
        'metode_bayar',
        'catatan',
        // CATATAN: 'status', 'total_harga' TIDAK dimasukkan!
        // Itu di-set oleh sistem, bukan user.
    ];
}
```

Contoh `app/Models/User.php` — pastikan `role` **TIDAK** ada di `$fillable`:

```php
protected $fillable = [
    'name',
    'email',
    'password',
    // 'role' SENGAJA tidak ada. Di-set manual di controller (lihat Modul 1).
];
```

**Langkah 2 — Set kolom sistem secara eksplisit di controller, bukan dari input user:**

```php
$order = Order::create([
    ...$validated,                  // data dari user (sudah tervalidasi)
    'user_id'     => auth()->id(),  // dari sistem
    'status'      => 'menunggu_pembayaran', // di-set sistem
    'total_harga' => $product->harga * $validated['jumlah'], // dihitung server
]);
```

> ⚠️ **Jangan pernah** percaya `total_harga` atau `status` yang dikirim dari form. Hitung ulang harga di server berdasarkan data produk di database.

### ✅ Verifikasi
1. Lewat Postman, kirim request register dengan tambahan `role=admin`. Cek DB: `role` harus tetap `user`.
2. Lewat Postman, kirim form pesanan dengan tambahan `total_harga=1` dan `status=selesai`. Cek DB: `total_harga` harus hasil hitungan server, `status` harus `menunggu_pembayaran`.

### ⚠️ Kesalahan Umum
- Model tanpa `$fillable` lalu pakai `create($request->all())`.
- Mengambil harga total dari input form (penyerang bisa beli kue Rp 1).

---

## 9. Modul 8 — Keamanan Upload Bukti Pembayaran ⭐ (PALING PENTING)

### 🎯 Tujuan
Karena **semua metode bayar (transfer/e-wallet/QRIS/COD) pakai upload bukti manual**, ini titik paling rawan. Mencegah penyerang upload file berbahaya (misal `.php`) yang bisa dieksekusi server, atau membanjiri server dengan file besar.

### 🔍 Audit

- [ ] Apakah upload bukti divalidasi tipe file & ukuran? Cek `PaymentController`.
- [ ] Coba upload file `.php` atau `.html` di-rename jadi `.jpg`. Apakah lolos?
- [ ] Di mana file disimpan? Apakah di `storage/app/public` (aman) atau langsung di `public/` (kurang aman)?
- [ ] Apakah nama file disimpan apa adanya dari user, atau di-generate ulang?

### 🛠️ Implementasi

**Langkah 1 — Validasi ketat saat upload.** Di `PaymentController@store`:

```php
$request->validate([
    'bukti_bayar' => [
        'required',
        'image',                       // hanya gambar
        'mimes:jpg,jpeg,png',          // ekstensi spesifik
        'mimetypes:image/jpeg,image/png', // cek MIME asli (lebih kuat)
        'max:2048',                    // maks 2 MB (satuan KB)
        'dimensions:max_width=4000,max_height=4000', // batasi resolusi
    ],
], [
    'bukti_bayar.image' => 'File harus berupa gambar (JPG/PNG).',
    'bukti_bayar.max'   => 'Ukuran maksimal 2 MB.',
]);
```

**Langkah 2 — Simpan dengan nama yang di-generate ulang** (jangan pakai nama asli dari user):

```php
use Illuminate\Support\Str;

$file = $request->file('bukti_bayar');

// Nama acak + ekstensi asli — cegah path traversal & overwrite
$namaFile = 'bukti_' . auth()->id() . '_' . time() . '_' . Str::random(8)
            . '.' . $file->getClientOriginalExtension();

// Simpan ke storage/app/public/bukti_pembayaran (di luar root web langsung)
$path = $file->storeAs('bukti_pembayaran', $namaFile, 'public');

// Simpan PATH-nya saja ke DB, bukan file-nya
Payment::create([
    'order_id'    => $order->id,
    'metode'      => $validated['metode_bayar'],
    'bukti_path'  => $path, // contoh: "bukti_pembayaran/bukti_3_1716..._a1b2.jpg"
    'status'      => 'menunggu_verifikasi',
]);
```

**Langkah 3 — Jalankan symlink storage** (sekali saja):

```bash
php artisan storage:link
```

Tampilkan gambar di Blade:

```blade
<img src="{{ asset('storage/' . $payment->bukti_path) }}" alt="Bukti Pembayaran" width="300">
```

**Langkah 4 — (Opsional, nilai plus) Cegah eksekusi file di folder upload.** Buat file `storage/app/public/bukti_pembayaran/.htaccess`:

```apache
# Matikan eksekusi script apapun di folder ini
php_flag engine off
<FilesMatch "\.(php|phtml|phar|cgi|pl|py|sh)$">
    Require all denied
</FilesMatch>
```

**Langkah 5 — Verifikasi bukti hanya boleh dilakukan ADMIN.** Pastikan endpoint "approve/tolak pembayaran" ada di route group `['auth','admin']` (lihat Modul 2). User biasa tidak boleh mengubah status pembayaran sendiri.

### ✅ Verifikasi
1. Upload gambar JPG normal < 2MB → berhasil.
2. Upload file `.php` → ditolak.
3. Rename `virus.php` jadi `virus.jpg` lalu upload → harus tetap ditolak (karena `mimetypes` cek isi asli, bukan cuma ekstensi).
4. Upload file 10 MB → ditolak.
5. Cek folder `storage/app/public/bukti_pembayaran` → nama file acak, bukan nama asli user.
6. Login user biasa → coba akses endpoint approve pembayaran → **403**.

### ⚠️ Kesalahan Umum
- Hanya cek ekstensi (`mimes`) tanpa `mimetypes` → file `.php` di-rename `.jpg` bisa lolos.
- Pakai nama file asli dari user → risiko overwrite & path traversal (`../../`).
- Simpan file langsung ke folder `public/` yang bisa diakses & dieksekusi.
- Lupa membatasi `max:` ukuran → server bisa penuh.

---

## 10. Modul 9 — Session & Cookie Security

### 🎯 Tujuan
Mengamankan cookie session supaya tidak mudah dicuri lewat JavaScript (XSS) dan session lama tidak bisa dipakai ulang setelah login.

### 🔍 Audit

- [ ] Buka `config/session.php`. Cek nilai `http_only`, `same_site`, `lifetime`.
- [ ] Cek apakah `LoginController`/Breeze memanggil `$request->session()->regenerate()` setelah login.

### 🛠️ Implementasi

**Langkah 1 — Atur `config/session.php`:**

```php
'lifetime' => 120,          // session habis setelah 120 menit idle
'expire_on_close' => false, // (true = logout saat browser ditutup)
'http_only' => true,        // cookie TIDAK bisa dibaca JavaScript (anti XSS)
'same_site' => 'lax',       // bantu cegah CSRF
// 'secure' => true,        // <- WAJIB jika sudah HTTPS. Lokal tanpa HTTPS: biarkan false.
```

Atau lewat `.env`:

```env
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=false   # ubah ke true jika nanti pakai HTTPS
```

**Langkah 2 — Regenerate session setelah login** (Breeze sudah melakukan ini di `AuthenticatedSessionController`, pastikan tidak terhapus):

```php
public function store(LoginRequest $request)
{
    $request->authenticate();
    $request->session()->regenerate(); // <- WAJIB ada (cegah session fixation)
    return redirect()->intended('dashboard');
}
```

**Langkah 3 — Invalidate session saat logout** (Breeze default):

```php
Auth::guard('web')->logout();
$request->session()->invalidate();
$request->session()->regenerateToken();
```

### ✅ Verifikasi
1. Buka DevTools → Application → Cookies. Cookie session harus punya flag **HttpOnly** = ✓.
2. Login, lalu di Console ketik `document.cookie` → cookie session **tidak boleh** muncul (karena HttpOnly).
3. Diamkan > waktu lifetime → akses halaman → harus diminta login lagi.

### ⚠️ Kesalahan Umum
- Set `SESSION_SECURE_COOKIE=true` di lokal tanpa HTTPS → tidak bisa login (cookie tidak terkirim).
- Menghapus baris `regenerate()` dari Breeze.

---

## 11. Modul 10 — Rate Limiting (Anti Brute-Force)

### 🎯 Tujuan
Mencegah penyerang mencoba login berkali-kali (menebak password) atau membanjiri server dengan request.

### 🔍 Audit

- [ ] Cek `LoginRequest` (Breeze) — biasanya sudah ada `ensureIsNotRateLimited()`. Pastikan tidak dihapus.
- [ ] Coba salah password 6–7 kali berturut-turut. Apakah muncul pesan "terlalu banyak percobaan, coba lagi nanti"?

### 🛠️ Implementasi

**Login throttle** sudah ada bawaan Breeze (`app/Http/Requests/Auth/LoginRequest.php`):

```php
RateLimiter::hit($this->throttleKey());
// otomatis blokir setelah 5x gagal
```

**Tambahkan throttle pada aksi sensitif lain** (misal upload bukti, submit pesanan) di `routes/web.php`:

```php
Route::middleware(['auth', 'throttle:10,1'])->group(function () {
    Route::post('/payment/upload', [PaymentController::class, 'store']);
    Route::post('/orders', [OrderController::class, 'store']);
});
// 'throttle:10,1' = maks 10 request per 1 menit per user
```

**Batasi register juga** (cegah spam akun):

```php
Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('throttle:5,1');
```

### ✅ Verifikasi
1. Salah password 6x berturut-turut → muncul pesan terkunci sementara + hitung mundur.
2. Submit form pesanan > 10x dalam 1 menit → request ke-11 ditolak (429 Too Many Requests).

### ⚠️ Kesalahan Umum
- Menghapus logika rate limit dari `LoginRequest` Breeze.
- Set angka terlalu kecil (`throttle:2,1`) sampai user normal pun terblokir.

---

## 12. Modul 11 — Error Handling & Information Disclosure

### 🎯 Tujuan
Mencegah pesan error Laravel (yang menampilkan path file, query SQL, isi `.env`) terlihat oleh user/penguji jika terjadi error.

### 🔍 Audit

- [ ] Buka `.env`. Cek nilai `APP_DEBUG` dan `APP_ENV`.
- [ ] Sengaja buat error (misal akses route yang tidak ada). Apakah muncul halaman error detail (stack trace) atau halaman error sederhana?

### 🛠️ Implementasi

**Saat development (sedang ngoding):** biarkan `APP_DEBUG=true` supaya bisa lihat error.

**Saat demo / pengumpulan / presentasi:** ubah `.env`:

```env
APP_ENV=production
APP_DEBUG=false
```

Lalu bersihkan cache config:

```bash
php artisan config:clear
php artisan config:cache
```

Dengan `APP_DEBUG=false`, error akan menampilkan halaman "500 Server Error" sederhana tanpa membocorkan kode/struktur internal.

**(Opsional) Buat halaman error custom.** Buat file `resources/views/errors/404.blade.php` dan `resources/views/errors/403.blade.php` dengan tampilan sesuai tema Jagoan Kue (cokelat/krem/pink).

### ✅ Verifikasi
1. Set `APP_DEBUG=false` → akses URL ngawur (`/abcxyz`) → muncul halaman 404 sederhana, **bukan** stack trace.
2. Pastikan tidak ada bagian aplikasi yang menampilkan isi `.env` atau path absolut file.

### ⚠️ Kesalahan Umum
- Lupa `APP_DEBUG=false` saat demo → penguji lihat seluruh kredensial DB di halaman error.
- Lupa `config:clear` setelah ubah `.env` → perubahan tidak berlaku.

---

## 13. Modul 12 — Audit Logging Aktivitas Admin (Opsional, Nilai Plus)

### 🎯 Tujuan
Mencatat siapa melakukan apa & kapan (misal admin mengubah status pesanan, menghapus produk). Berguna untuk pelacakan dan menambah nilai laporan.

### 🔍 Audit

- [ ] Apakah ada pencatatan saat admin mengubah status pesanan atau menghapus produk?

### 🛠️ Implementasi

**Cara sederhana — pakai Log Laravel.** Di controller admin, setelah aksi penting:

```php
use Illuminate\Support\Facades\Log;

public function updateStatus(Request $request, Order $order)
{
    $statusLama = $order->status;
    $order->update(['status' => $request->status]);

    Log::channel('daily')->info('Admin ubah status pesanan', [
        'admin_id'  => auth()->id(),
        'admin'     => auth()->user()->name,
        'order_id'  => $order->id,
        'dari'      => $statusLama,
        'ke'        => $request->status,
        'waktu'     => now()->toDateTimeString(),
    ]);
}
```

Log tersimpan di `storage/logs/laravel-YYYY-MM-DD.log`.

**(Opsional lanjutan)** Buat tabel `activity_logs` (kolom: `user_id`, `aksi`, `keterangan`, `created_at`) lalu simpan record di sana supaya bisa ditampilkan di dashboard admin.

### ✅ Verifikasi
1. Admin ubah status sebuah pesanan.
2. Buka `storage/logs/laravel-*.log` → ada catatan berisi admin_id, order_id, status lama → baru.

### ⚠️ Kesalahan Umum
- Mencatat data sensitif (password) ke log. Jangan pernah log password atau token.

---

## 14. Modul 13 — Environment & Security Headers (Opsional, Nilai Plus)

### 🎯 Tujuan
Mengamankan konfigurasi rahasia dan menambahkan header HTTP yang mempersulit serangan tertentu.

### 🔍 Audit

- [ ] Apakah file `.env` masuk ke `.gitignore`? (Cek file `.gitignore`.)
- [ ] Apakah `APP_KEY` di `.env` sudah terisi?

### 🛠️ Implementasi

**Langkah 1 — Pastikan rahasia tidak ikut ter-commit.** Cek `.gitignore` mengandung:

```
/vendor
/node_modules
.env
.env.backup
/storage/*.key
```

**Langkah 2 — Pastikan `APP_KEY` terisi:**

```bash
php artisan key:generate
```

**Langkah 3 — (Opsional) Tambahkan security headers.** Buat middleware:

```bash
php artisan make:middleware SecurityHeaders
```

Isi `handle()`:

```php
public function handle(Request $request, Closure $next): Response
{
    $response = $next($request);

    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-Frame-Options', 'SAMEORIGIN'); // cegah clickjacking
    $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    $response->headers->set('X-XSS-Protection', '1; mode=block');

    return $response;
}
```

Daftarkan sebagai middleware global di `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
})
```

**Langkah 4 — (Opsional) HTTPS lokal.** Untuk proyek kuliah lokal umumnya **tidak wajib**. Jika ingin coba: gunakan `php artisan serve` + tool seperti `mkcert`, atau cukup jelaskan di laporan bahwa pada produksi nyata HTTPS wajib (set `SESSION_SECURE_COOKIE=true`).

### ✅ Verifikasi
1. Jalankan `git status` → file `.env` **tidak** muncul sebagai tracked file.
2. Buka DevTools → Network → klik request halaman → tab Headers → muncul `X-Frame-Options`, `X-Content-Type-Options`.

### ⚠️ Kesalahan Umum
- `.env` ter-commit ke GitHub (kredensial DB bocor publik).
- Lupa `key:generate` → error "No application encryption key".

---

## 15. Master Checklist (Untuk Tracking Progress)

Centang setelah modul **selesai dikerjakan + diverifikasi**.

| ✓ | Modul | Status Audit | Status Implementasi | Status Verifikasi |
|---|---|---|---|---|
| ☐ | 1. Autentikasi & Password | ☐ | ☐ | ☐ |
| ☐ | 2. Otorisasi & RBAC | ☐ | ☐ | ☐ |
| ☐ | 3. CSRF Protection | ☐ | ☐ | ☐ |
| ☐ | 4. Validasi Input | ☐ | ☐ | ☐ |
| ☐ | 5. XSS Prevention | ☐ | ☐ | ☐ |
| ☐ | 6. SQL Injection | ☐ | ☐ | ☐ |
| ☐ | 7. Mass Assignment | ☐ | ☐ | ☐ |
| ☐ | 8. Upload Bukti Bayar ⭐ | ☐ | ☐ | ☐ |
| ☐ | 9. Session & Cookie | ☐ | ☐ | ☐ |
| ☐ | 10. Rate Limiting | ☐ | ☐ | ☐ |
| ☐ | 11. Error Handling | ☐ | ☐ | ☐ |
| ☐ | 12. Audit Logging (opsional) | ☐ | ☐ | ☐ |
| ☐ | 13. Env & Headers (opsional) | ☐ | ☐ | ☐ |

---

## 16. Skenario Testing Manual (Simulasi Serangan Sederhana)

Lakukan semua tes ini setelah implementasi selesai. Tidak perlu tool khusus, cukup browser + Postman.

| # | Skenario | Cara | Hasil yang Benar |
|---|---|---|---|
| 1 | Akses admin sebagai user | Login user, buka `/admin/dashboard` | 403 Forbidden |
| 2 | Lihat pesanan orang lain | Login user A, buka URL order user B | 403 Forbidden |
| 3 | XSS via nama penerima | Pesan dengan nama `<script>alert(1)</script>` | Tampil sebagai teks, tidak ada popup |
| 4 | SQL Injection di search | Cari produk: `' OR '1'='1` | Hasil normal/kosong, tidak error |
| 5 | Upload file `.php` | Upload bukti berupa file PHP | Ditolak |
| 6 | File PHP rename `.jpg` | Rename `x.php`→`x.jpg`, upload | Ditolak (cek mimetypes) |
| 7 | Mass assignment role | Register + field `role=admin` (Postman) | role tetap `user` |
| 8 | Manipulasi harga total | Submit pesanan + `total_harga=1` | Harga dihitung server, bukan 1 |
| 9 | Brute force login | Salah password 6x | Akun terkunci sementara |
| 10 | Form tanpa CSRF | Hapus `@csrf`, submit | 419 Page Expired |
| 11 | Jumlah negatif | Pesan dengan `jumlah=-5` | Ditolak validasi |
| 12 | Error disclosure | `APP_DEBUG=false`, akses URL ngawur | Halaman 404 sederhana, bukan stack trace |

---

## 17. Glosarium (Istilah Singkat)

| Istilah | Arti Sederhana |
|---|---|
| **Hash** | Mengacak password jadi kode yang tidak bisa dibalik |
| **CSRF** | Serangan: situs lain "menyamar" jadi user untuk kirim request |
| **XSS** | Serangan: menyisipkan script jahat lewat input yang ditampilkan kembali |
| **SQL Injection** | Serangan: memanipulasi query database lewat input |
| **Mass Assignment** | Mengisi kolom DB yang tidak seharusnya lewat input form |
| **RBAC** | Pengaturan hak akses berdasarkan peran (admin vs user) |
| **Rate Limiting** | Membatasi jumlah request dalam waktu tertentu |
| **Middleware** | "Penjaga pintu" yang mengecek request sebelum masuk controller |
| **Session Fixation** | Serangan memakai ulang ID session lama |
| **Path Traversal** | Trik nama file `../../` untuk menyimpan file ke lokasi terlarang |

---

## 18. Urutan Pengerjaan yang Disarankan

Kalau waktu terbatas (proyek kuliah), kerjakan dengan urutan ini:

1. **Hari 1 (wajib):** Modul 2 (RBAC) → Modul 8 (Upload Bukti) → Modul 4 (Validasi Input)
   *(3 ini paling sering jadi pertanyaan penguji)*
2. **Hari 2 (wajib):** Modul 1, 3, 5, 6, 7 (audit + perbaikan kecil, kebanyakan sudah aman dari Breeze/Eloquent)
3. **Hari 3 (pelengkap):** Modul 9, 10, 11 → jalankan seluruh skenario testing di Bagian 16
4. **Jika ada waktu lebih (nilai plus):** Modul 12 & 13

---

*Dokumen ini adalah perencanaan. Sesuaikan nama controller/route/kolom dengan struktur asli kode Jagoan Kue jika berbeda. Selalu tes di lokal sebelum dianggap selesai.*
