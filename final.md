# 🍰 Jagoan Kue — Final Review & Implementation Plan

> **Dokumen ini adalah panduan kerja 4 minggu untuk menyelesaikan, merapikan, dan mendeploy final project mata kuliah Pemrograman Web.**
> Pembaca: junior developer & AI coding agent (model murah).
> Penulis aslinya: Zidane (Informatika UNP, NIM 25343071).

---

## 0. Konteks Proyek

| Item | Nilai |
|---|---|
| Nama proyek | **Jagoan Kue** — e-commerce toko kue (cake ordering) |
| Framework | Laravel 11+ (Blade templating) |
| Auth | Laravel Breeze |
| Database | MySQL |
| Frontend | Blade + (target akhir) CSS yang ter-organize, bukan inline |
| Palet warna | Cream `#FBF6EE` / Brown `#7B4B2A` / Pink `#F5C6C6` (warm, lembut) |
| Status user-facing | ~90% selesai (7 halaman dari Figma sudah di-Laravel-kan) |
| Status admin | **0% — harus dibangun dari nol** |
| Deadline kerja | 4 minggu |

---

## 1. Aturan Main untuk Eksekutor (WAJIB DIBACA)

Aturan ini berlaku untuk **siapa pun** yang mengerjakan task di dokumen ini — manusia maupun AI agent.

### 1.1 Prinsip umum

1. **Jangan kerja di branch `main`.** Buat branch `feature/<nama-singkat>` untuk tiap task besar. Merge ke `main` hanya setelah testing manual lulus.
2. **Satu task = satu commit kecil.** Pesan commit format: `type(scope): pesan singkat`. Contoh: `feat(admin): tambah CRUD produk`, `fix(checkout): perbaiki validasi alamat`, `refactor(view): pindah inline CSS ke layout`.
3. **Jangan ubah file yang tidak diminta task.** Kalau menemukan bug di luar scope task, **catat** di section `BUGS_DITEMUKAN` di akhir dokumen ini, jangan langsung benerin.
4. **Selalu jalankan `php artisan route:list` setelah menambah route**, pastikan tidak ada konflik.
5. **Selalu jalankan `php artisan migrate:fresh --seed` di env lokal** setelah ubah migration. **JANGAN PERNAH** jalankan di production.
6. **Jangan hapus migration yang sudah ada di repo.** Kalau perlu ubah skema, buat migration baru (`php artisan make:migration ...`).
7. **Jangan commit file `.env`, folder `vendor/`, `node_modules/`, atau `storage/app/public/*` (kecuali `.gitignore`).**

### 1.2 Definition of Done (DoD) — berlaku untuk SEMUA task

Sebuah task dianggap selesai HANYA JIKA:

- [ ] Fitur jalan tanpa error di browser (cek dengan `php artisan serve`)
- [ ] Tidak ada error di console browser (F12 → Console)
- [ ] Tidak ada error di `storage/logs/laravel.log`
- [ ] Sudah ada validasi input (untuk form)
- [ ] Sudah ada flash message sukses/error (untuk action create/update/delete)
- [ ] Sudah dicommit dengan pesan yang benar
- [ ] Sudah dicheck di `route:list` (kalau task ada route)

### 1.3 Anti-pattern yang HARUS DIHINDARI

| ❌ Jangan | ✅ Lakukan |
|---|---|
| `DB::table('produk')->insert(...)` di controller | `Produk::create($validated)` dengan `$fillable` jelas |
| `$_POST['nama']` | `$request->validated()['nama']` |
| `<input value="<?= $nama ?>">` | `<input value="{{ $nama }}">` (auto-escape XSS) |
| Logic bisnis di Blade | Logic bisnis di controller/service, view hanya tampil |
| Hapus data langsung tanpa konfirmasi | Pakai modal konfirmasi + soft delete |
| Inline CSS `style="..."` di tiap halaman | CSS terpusat di `resources/css/` atau Tailwind |
| `Model::all()` lalu di-loop | Gunakan eager loading: `Model::with('relasi')->get()` |
| Lupa `php artisan storage:link` setelah upload gambar | Selalu jalankan setelah clone repo baru |

---

## 2. Status Awal — Audit Cepat

Sebelum mulai Minggu 1, eksekutor **wajib** melakukan audit ini dan mengisi checklist:

```bash
# Jalankan urut di root project
git status                                     # pastikan working tree clean
composer install
npm install
cp .env.example .env                           # kalau belum ada .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve                              # buka http://127.0.0.1:8000
```

Setelah itu, isi checklist berikut di issue/note pribadi:

- [ ] Semua halaman user-facing (home, katalog, detail produk, cart, checkout, profil, login/register) bisa diakses tanpa error?
- [ ] Auth (login, register, logout) jalan?
- [ ] Database punya tabel: `users`, `produk`/`products`, `kategori`/`categories`, `pesanan`/`orders`, `detail_pesanan`/`order_items`, `cart_items`?  *Catat nama tabel aktual ke section APPENDIX.*
- [ ] Sudah ada seeder dengan data dummy minimal 10 produk?
- [ ] Sudah ada akun admin di seeder? (kalau belum → ini task pertama di Minggu 2)

---

## 3. Roadmap 4 Minggu

| Minggu | Tema | Output Utama |
|---|---|---|
| **1** | Audit & Refactor Foundation | Kode rapi, inline CSS hilang, struktur konsisten, dokumentasi awal |
| **2** | Admin Dashboard Build | CRUD produk, kategori, pesanan, user, voucher (semua jalan) |
| **3** | Laporan, Polish, & Validasi | Laporan penjualan, validasi lengkap, error handling, UX polish |
| **4** | QA, Testing, & Deployment | Test manual menyeluruh, seeder demo, README, deploy |

---

# 📅 MINGGU 1 — Audit & Refactor Foundation

**Tujuan minggu ini:** Bersihkan kode existing supaya layak dipresentasikan dan jadi fondasi yang kuat untuk admin dashboard. Tidak ada fitur baru, fokus **rapikan**.

## Task 1.1 — Audit kode & buat dokumen `AUDIT.md`

**Estimasi:** 1 hari
**Tujuan:** Tahu kondisi awal kode sebelum diutak-atik.

### Langkah:

1. Buat file `docs/AUDIT.md` di root project.
2. Isi dengan template berikut, lalu lengkapi:

```markdown
# Audit Kode Jagoan Kue — [tanggal]

## Struktur Database
- Tabel apa saja yang ada? (list dari `php artisan db:show`)
- Relasi antar tabel? (gambarkan singkat)

## Routes
- Hasil dari `php artisan route:list --except-vendor` (copy ke sini)

## Controllers
- List semua controller di `app/Http/Controllers/`
- Catat controller mana yang sudah "gemuk" (>200 baris) → kandidat refactor

## Views (Blade)
- List semua file di `resources/views/`
- Tandai mana yang masih pakai inline CSS

## Models
- List semua model di `app/Models/`
- Catat yang BELUM punya `$fillable` → bahaya mass assignment

## Bug / Issue yang ditemukan
- (list bebas)
```

### DoD:
- [ ] File `docs/AUDIT.md` ada dan terisi
- [ ] Dicommit: `docs(audit): tambah hasil audit awal`

---

## Task 1.2 — Standardisasi naming & struktur folder

**Estimasi:** 1 hari
**Tujuan:** Pakai konvensi konsisten supaya tidak bingung.

### Konvensi WAJIB:

| Item | Pola | Contoh |
|---|---|---|
| Nama tabel | `snake_case`, **plural** | `produks`, `kategoris`, `pesanans` *(boleh Indonesia, tapi KONSISTEN)* |
| Nama model | `PascalCase`, **singular** | `Produk`, `Kategori`, `Pesanan` |
| Nama controller | `PascalCase` + `Controller` | `ProdukController`, `Admin/ProdukController` |
| Nama route | `kebab-case` | `/admin/produk-baru`, bukan `/admin/produkBaru` |
| Nama view | `snake_case` di folder berstruktur | `admin/produk/index.blade.php` |
| Method controller | `camelCase` | `store()`, `tambahKeKeranjang()` |
| Kolom DB | `snake_case` | `harga_satuan`, `tanggal_pesan` |
| Variabel | `camelCase` di PHP, `snake_case` di Blade boleh | `$produkBaru` |

**ATURAN PENTING:** Kalau di project sudah ada satu konvensi (misal model pakai bahasa Indonesia `Produk`), **JANGAN diubah ke `Product`**. Konsistensi > preferensi. Kalau campur aduk, pilih yang paling banyak dipakai, dan migrate yang lain pelan-pelan.

### Langkah:

1. Buka `docs/AUDIT.md`, identifikasi inkonsistensi naming.
2. Pilih satu standar (utamakan yang sudah dominan).
3. Refactor yang melenceng. Untuk rename tabel: buat migration baru, JANGAN ubah migration lama yang sudah pernah dijalankan teman/dosen.
4. Update dokumentasi di `AUDIT.md`.

### DoD:
- [ ] Tidak ada lagi campur `kategori` dan `categories` di project
- [ ] Semua controller punya nama yang konsisten
- [ ] `php artisan migrate:fresh --seed` masih jalan tanpa error

---

## Task 1.3 — Buat layout utama & component Blade

**Estimasi:** 2 hari
**Tujuan:** Hilangkan duplikasi HTML antar halaman. Inline CSS dipindah ke satu tempat.

### Langkah:

1. Buat file `resources/views/layouts/app.blade.php` (kalau belum ada dari Breeze):

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Jagoan Kue' }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    @include('partials.navbar')

    <main>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>

    @include('partials.footer')
    @stack('scripts')
</body>
</html>
```

2. Pisahkan navbar dan footer ke `resources/views/partials/navbar.blade.php` dan `partials/footer.blade.php`.

3. Buat file CSS terpusat di `public/css/app.css` (atau pakai Vite kalau sudah set up). Pindahkan SEMUA inline CSS dari setiap halaman ke sini.

4. Update setiap halaman user-facing pakai pola:

```blade
@extends('layouts.app')

@section('content')
    {{-- isi halaman --}}
@endsection
```

5. Variabel CSS untuk palet warna — taruh di paling atas `app.css`:

```css
:root {
    --color-cream: #FBF6EE;
    --color-brown: #7B4B2A;
    --color-brown-dark: #5A3620;
    --color-pink: #F5C6C6;
    --color-pink-soft: #FCE4E4;
    --color-text: #2D2D2D;
    --color-muted: #8A8A8A;
}

body {
    background-color: var(--color-cream);
    color: var(--color-text);
    font-family: 'Poppins', sans-serif;
}

.btn-primary {
    background-color: var(--color-brown);
    color: white;
    padding: 0.6rem 1.2rem;
    border-radius: 0.5rem;
    border: none;
    cursor: pointer;
}
.btn-primary:hover { background-color: var(--color-brown-dark); }
```

### DoD:
- [ ] Semua halaman user-facing pakai `@extends('layouts.app')`
- [ ] Tidak ada lagi tag `<style>` inline di halaman (kecuali untuk hal yang sangat spesifik, dengan komentar alasan)
- [ ] File `public/css/app.css` ada dan terisi
- [ ] Flash message muncul ketika `session('success')` di-set

---

## Task 1.4 — Pastikan semua model punya `$fillable` dan relasi

**Estimasi:** 0.5 hari
**Tujuan:** Cegah bug mass assignment & gampang pakai relasi.

### Contoh template untuk `app/Models/Produk.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produk extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama', 'slug', 'deskripsi', 'harga',
        'stok', 'gambar', 'kategori_id', 'is_active',
    ];

    protected $casts = [
        'harga'     => 'integer',
        'stok'      => 'integer',
        'is_active' => 'boolean',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class);
    }
}
```

### DoD:
- [ ] Semua model punya `$fillable`
- [ ] Semua model punya relasi yang dibutuhkan
- [ ] Tambahkan `use SoftDeletes` untuk `Produk`, `Kategori`, `Pesanan` (kalau belum)
- [ ] Buat migration untuk tambah kolom `deleted_at` kalau belum ada:
  ```bash
  php artisan make:migration add_soft_delete_to_produks --table=produks
  ```

---

## Task 1.5 — Setup Tailwind atau finalisasi CSS terpusat

**Estimasi:** 1 hari (opsional kalau sudah pakai CSS terpusat dari Task 1.3)
**Tujuan:** Bikin styling konsisten & cepat di-extend untuk admin.

**Pilihan A — Stay pakai CSS murni** (lebih simpel, kalau waktu mepet)
→ Pastikan `public/css/app.css` sudah lengkap dari Task 1.3. SKIP task ini.

**Pilihan B — Pakai Tailwind** (lebih modern, lebih cepat untuk admin)

```bash
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
```

Edit `tailwind.config.js`:

```js
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        cream: '#FBF6EE',
        brown: { DEFAULT: '#7B4B2A', dark: '#5A3620' },
        pink:  { DEFAULT: '#F5C6C6', soft: '#FCE4E4' },
      },
    },
  },
  plugins: [],
}
```

Edit `resources/css/app.css`:
```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

Lalu di layout pakai `@vite('resources/css/app.css')`. Jalankan `npm run dev` saat develop.

### DoD:
- [ ] Pilihan sudah dijatuhkan (A atau B), dicatat di `AUDIT.md`
- [ ] Halaman home masih tampil normal setelah perubahan

---

# 📅 MINGGU 2 — Admin Dashboard Build

**Tujuan minggu ini:** Bangun admin dashboard lengkap. Pakai pola yang sama untuk tiap entitas (CRUD) supaya konsisten.

## Task 2.1 — Setup role & middleware admin

**Estimasi:** 0.5 hari

### Langkah:

1. Tambah kolom `role` di tabel `users`:

```bash
php artisan make:migration add_role_to_users_table --table=users
```

Isi migration:

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->enum('role', ['admin', 'customer'])->default('customer')->after('email');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('role');
    });
}
```

2. Tambah ke `$fillable` di `User.php`: tambahkan `'role'`.

3. Buat middleware:

```bash
php artisan make:middleware IsAdmin
```

Isi `app/Http/Middleware/IsAdmin.php`:

```php
public function handle(Request $request, Closure $next): Response
{
    if (!auth()->check() || auth()->user()->role !== 'admin') {
        abort(403, 'Akses ditolak. Hanya untuk admin.');
    }
    return $next($request);
}
```

4. Register di `bootstrap/app.php` (Laravel 11+):

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\IsAdmin::class,
    ]);
})
```

5. Tambah seeder admin di `database/seeders/UserSeeder.php`:

```php
User::create([
    'name'     => 'Admin Jagoan Kue',
    'email'    => 'admin@jagoankue.test',
    'password' => Hash::make('password'),
    'role'     => 'admin',
]);
```

### DoD:
- [ ] Bisa login pakai `admin@jagoankue.test` / `password`
- [ ] Akses ke `/admin/*` (yang akan dibuat) ditolak untuk non-admin

---

## Task 2.2 — Layout admin

**Estimasi:** 0.5 hari

Buat `resources/views/layouts/admin.blade.php` dengan sidebar berisi link: Dashboard, Produk, Kategori, Pesanan, Voucher, User, Laporan, Logout.

Struktur folder view admin:

```
resources/views/admin/
├── dashboard.blade.php
├── produk/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
├── kategori/
│   ├── index.blade.php
│   └── ...
├── pesanan/
├── voucher/
├── user/
└── laporan/
```

### DoD:
- [ ] Layout admin punya sidebar konsisten
- [ ] Active link di sidebar di-highlight
- [ ] Halaman `/admin/dashboard` bisa diakses, menampilkan card jumlah produk, jumlah pesanan, total pendapatan bulan ini

---

## Task 2.3 — CRUD Kategori

**Estimasi:** 0.5 hari (template untuk CRUD lain)

### Route (`routes/web.php`):

```php
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('kategori', \App\Http\Controllers\Admin\KategoriController::class);
});
```

### Buat controller:

```bash
php artisan make:controller Admin/KategoriController --resource --model=Kategori
```

### Buat Form Request:

```bash
php artisan make:request KategoriRequest
```

Isi `app/Http/Requests/KategoriRequest.php`:

```php
public function authorize(): bool { return true; }

public function rules(): array
{
    $kategoriId = $this->route('kategori')?->id;
    return [
        'nama' => ['required', 'string', 'max:100', 'unique:kategoris,nama,' . $kategoriId],
        'slug' => ['nullable', 'string', 'max:120'],
    ];
}
```

### Method controller (contoh `store`):

```php
public function store(KategoriRequest $request)
{
    $data = $request->validated();
    $data['slug'] = Str::slug($data['nama']);
    Kategori::create($data);

    return redirect()
        ->route('admin.kategori.index')
        ->with('success', 'Kategori berhasil ditambahkan.');
}
```

### DoD:
- [ ] List kategori dengan pagination (10 per halaman)
- [ ] Form tambah & edit jalan dengan validasi
- [ ] Hapus pakai modal konfirmasi
- [ ] Flash message muncul setelah create/update/delete

---

## Task 2.4 — CRUD Produk + Upload Gambar

**Estimasi:** 1.5 hari

**Pola sama dengan Task 2.3, plus:**

### Handling upload gambar:

```php
public function store(ProdukRequest $request)
{
    $data = $request->validated();

    if ($request->hasFile('gambar')) {
        $data['gambar'] = $request->file('gambar')->store('produk', 'public');
        // hasil: storage/app/public/produk/abc123.jpg
        // akses via: asset('storage/' . $data['gambar'])
    }

    $data['slug'] = Str::slug($data['nama']);
    Produk::create($data);

    return redirect()->route('admin.produk.index')->with('success', 'Produk ditambahkan.');
}

public function update(ProdukRequest $request, Produk $produk)
{
    $data = $request->validated();

    if ($request->hasFile('gambar')) {
        // hapus gambar lama
        if ($produk->gambar) {
            Storage::disk('public')->delete($produk->gambar);
        }
        $data['gambar'] = $request->file('gambar')->store('produk', 'public');
    }

    $produk->update($data);
    return redirect()->route('admin.produk.index')->with('success', 'Produk diupdate.');
}
```

### Validasi gambar (`ProdukRequest.php`):

```php
'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // 2MB
```

### Form Blade — penting:

```blade
<form action="{{ route('admin.produk.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    {{-- field lain --}}
    <input type="file" name="gambar" accept="image/*">
</form>
```

**JANGAN LUPA `enctype="multipart/form-data"`** — ini bug klasik kalau upload gambar gagal.

### DoD:
- [ ] Bisa tambah produk dengan gambar
- [ ] Bisa edit (termasuk ganti gambar — gambar lama terhapus)
- [ ] Bisa hapus (soft delete) — gambar di storage TIDAK perlu dihapus, untuk audit
- [ ] List produk pakai eager loading: `Produk::with('kategori')->paginate(10)`
- [ ] Gambar tampil benar di halaman user-facing

---

## Task 2.5 — Manajemen Pesanan & Status

**Estimasi:** 1.5 hari

### Skema status pesanan (enum):

```
pending   → baru, belum dibayar
diproses  → admin konfirmasi, sedang dibuat
dikirim   → sudah dikirim
selesai   → diterima customer
dibatalkan → dibatalkan
```

### Halaman yang harus ada:

- `/admin/pesanan` → list semua pesanan, bisa filter by status & cari by nomor pesanan
- `/admin/pesanan/{id}` → detail pesanan (lihat item, alamat, status history, tombol update status)

### Action: update status

Route:

```php
Route::patch('pesanan/{pesanan}/status', [PesananController::class, 'updateStatus'])
     ->name('pesanan.status');
```

Controller:

```php
public function updateStatus(Request $request, Pesanan $pesanan)
{
    $request->validate([
        'status' => ['required', 'in:pending,diproses,dikirim,selesai,dibatalkan'],
    ]);

    $pesanan->update(['status' => $request->status]);

    return back()->with('success', "Status pesanan #{$pesanan->kode} diubah ke {$request->status}.");
}
```

### DoD:
- [ ] List pesanan dengan filter status & search kode
- [ ] Detail pesanan menampilkan: customer, item-item, subtotal, ongkir, total, alamat
- [ ] Bisa ubah status via dropdown (form submit, BUKAN AJAX dulu — biar simpel)
- [ ] Pesanan tidak bisa "mundur" status (opsional: validasi di backend)

---

## Task 2.6 — Manajemen User

**Estimasi:** 0.5 hari

### Fungsi minimum:

- `/admin/user` → list user, kolom: nama, email, role, jumlah pesanan, tanggal daftar
- Bisa ubah role (customer ↔ admin) dengan konfirmasi
- Tidak bisa hapus user secara langsung (alasannya: integritas FK ke pesanan). Cukup soft delete atau toggle "is_active".

### DoD:
- [ ] List user dengan pagination & search
- [ ] Tidak bisa demote diri sendiri (cek di controller: `if ($user->id === auth()->id())`)
- [ ] Counter "jumlah pesanan" pakai `withCount('pesanan')`

---

## Task 2.7 — Manajemen Voucher / Diskon

**Estimasi:** 1 hari

### Skema tabel `vouchers`:

```php
Schema::create('vouchers', function (Blueprint $table) {
    $table->id();
    $table->string('kode', 30)->unique();             // contoh: KUEMANIS10
    $table->enum('tipe', ['persen', 'nominal']);
    $table->integer('nilai');                          // 10 (persen) atau 25000 (rupiah)
    $table->integer('minimum_belanja')->default(0);
    $table->date('berlaku_sampai');
    $table->integer('kuota')->nullable();              // null = unlimited
    $table->integer('terpakai')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### CRUD voucher sama dengan pola sebelumnya. Plus, di sisi user (checkout):

- Field input kode voucher di halaman checkout
- Tombol "Pakai" → AJAX/form submit cek voucher valid, lalu update total

### DoD:
- [ ] CRUD voucher di admin jalan
- [ ] Di checkout: kode voucher bisa diaplikasikan
- [ ] Validasi: kode harus aktif, belum expired, kuota belum habis, belanja >= minimum
- [ ] Setelah pesanan dibuat, kolom `terpakai` voucher bertambah 1

---

# 📅 MINGGU 3 — Laporan, Polish & Validasi

## Task 3.1 — Laporan Penjualan

**Estimasi:** 1.5 hari

### Halaman `/admin/laporan`:

Filter: dari tanggal, sampai tanggal.

Tampilkan:

1. **Card summary**: total pesanan, total pendapatan, jumlah produk terjual, rata-rata nilai pesanan
2. **Grafik penjualan per hari** (pakai Chart.js — CDN cukup)
3. **Tabel produk terlaris** (top 10)
4. **Tombol Export Excel** atau **Export PDF**

### Query untuk grafik per hari:

```php
$penjualan = Pesanan::where('status', 'selesai')
    ->whereBetween('created_at', [$dari, $sampai])
    ->selectRaw('DATE(created_at) as tanggal, SUM(total) as total')
    ->groupBy('tanggal')
    ->orderBy('tanggal')
    ->get();
```

### Export Excel — install:

```bash
composer require maatwebsite/excel
```

### DoD:
- [ ] Filter tanggal jalan
- [ ] Grafik tampil dengan Chart.js
- [ ] Top 10 produk terlaris tampil (urutan benar)
- [ ] Export Excel menghasilkan file `.xlsx` valid

---

## Task 3.2 — Validasi & Error Handling menyeluruh

**Estimasi:** 1.5 hari

### Checklist per form:

- [ ] Setiap form punya `Form Request` (bukan validasi langsung di controller)
- [ ] Pesan error tampil di bawah field (`@error('nama_field') ... @enderror`)
- [ ] Field yang gagal validasi tetap mempertahankan input lama (`{{ old('nama') }}`)
- [ ] Halaman error 404, 403, 500 punya tampilan custom (di `resources/views/errors/`)

### Buat custom error pages:

```bash
mkdir -p resources/views/errors
```

Contoh `resources/views/errors/404.blade.php`:

```blade
@extends('layouts.app')
@section('content')
<div style="text-align:center; padding:4rem;">
    <h1 style="font-size:4rem; color:var(--color-brown);">404</h1>
    <p>Halaman yang kamu cari tidak ada.</p>
    <a href="{{ route('home') }}" class="btn-primary">Kembali ke Home</a>
</div>
@endsection
```

Lakukan juga untuk `403.blade.php` dan `500.blade.php`.

### DoD:
- [ ] Submit form kosong → error message tampil dengan rapi
- [ ] Akses URL random `/asdf` → halaman 404 custom
- [ ] Akses `/admin/*` sebagai customer → halaman 403 custom

---

## Task 3.3 — UX Polish & Konsistensi Visual

**Estimasi:** 1 hari

### Checklist:

- [ ] Loading state pada tombol submit (`<button disabled>` saat submit, opsional pakai Alpine.js)
- [ ] Konfirmasi delete pakai modal/SweetAlert (bukan `confirm()` browser default)
- [ ] Empty state di tiap list (kalau belum ada data: tampilkan ilustrasi + CTA)
- [ ] Responsive di mobile (cek di Chrome DevTools, mode device toolbar)
- [ ] Konsistensi spacing, font size, warna tombol di seluruh halaman
- [ ] Favicon `.ico` ada di `public/`
- [ ] Title tab per halaman descriptive (`<title>Detail Produk - Jagoan Kue</title>`)

---

## Task 3.4 — N+1 Query Hunt

**Estimasi:** 0.5 hari

### Install debug tool:

```bash
composer require barryvdh/laravel-debugbar --dev
```

### Langkah:

1. Buka tiap halaman penting (home, katalog, admin produk, admin pesanan)
2. Lihat panel "Queries" di debugbar
3. Kalau ada query yang sama berulang dengan WHERE id yang berbeda → itu N+1
4. Perbaiki dengan `->with('relasi')`

### Contoh kasus:

```php
// ❌ N+1
$produks = Produk::all();
foreach ($produks as $p) { echo $p->kategori->nama; } // 1 + N query

// ✅ Eager loading
$produks = Produk::with('kategori')->get();          // 2 query total
```

### DoD:
- [ ] Total query per halaman < 15 untuk halaman normal (cek di debugbar)
- [ ] Tidak ada N+1 di halaman list (admin/produk, katalog, dll)

---

# 📅 MINGGU 4 — QA, Testing, Deployment & Submission

## Task 4.1 — QA Manual: Skenario End-to-End

**Estimasi:** 1.5 hari

Lakukan SEMUA skenario di bawah. Tandai ✅ kalau lulus, ❌ kalau gagal (dan catat ke `BUGS_DITEMUKAN`).

### Skenario A — Customer journey

- [ ] Register akun baru dengan email valid
- [ ] Login dengan akun yang baru dibuat
- [ ] Browse katalog, filter by kategori
- [ ] Buka detail produk, klik "Tambah ke Keranjang"
- [ ] Buka keranjang, ubah qty, hapus item
- [ ] Checkout: isi alamat, pilih metode pembayaran (kalau ada), terapkan voucher valid
- [ ] Coba terapkan voucher invalid/expired → harus tertolak dengan pesan jelas
- [ ] Submit pesanan → masuk ke halaman sukses
- [ ] Cek di "Pesanan Saya" → pesanan muncul dengan status "pending"
- [ ] Logout

### Skenario B — Admin journey

- [ ] Login sebagai admin
- [ ] Dashboard: angka di card sesuai database
- [ ] Tambah kategori baru "Cake Ulang Tahun"
- [ ] Tambah produk baru dengan gambar di kategori tersebut
- [ ] Edit produk: ganti gambar → gambar lama hilang dari storage
- [ ] Buka pesanan customer dari Skenario A → ubah status `pending → diproses → dikirim → selesai`
- [ ] Cek laporan: filter tanggal hari ini → pesanan tadi muncul
- [ ] Export laporan ke Excel → buka, datanya benar
- [ ] Tambah voucher baru → coba pakai di checkout (login customer lain)

### Skenario C — Edge cases & security

- [ ] Submit form tanpa CSRF token → harus error 419
- [ ] Coba akses `/admin/dashboard` tanpa login → redirect ke login
- [ ] Coba akses `/admin/dashboard` sebagai customer → halaman 403
- [ ] Upload file non-image di field gambar produk → tertolak
- [ ] Upload gambar > 2MB → tertolak dengan pesan jelas
- [ ] Cari di search dengan input `<script>alert(1)</script>` → harus ter-escape, tidak eksekusi
- [ ] URL dengan ID produk yang tidak ada `/produk/9999` → 404
- [ ] Coba submit qty negatif/0 di keranjang → tertolak

### DoD Task 4.1:
- [ ] Semua skenario A, B, C lulus ✅
- [ ] Bug yang ditemukan sudah dicatat di `BUGS_DITEMUKAN` dan minimal yang severity HIGH sudah diperbaiki

---

## Task 4.2 — Seeder Data Demo

**Estimasi:** 0.5 hari

Pastikan `php artisan migrate:fresh --seed` menghasilkan data yang **layak demo**:

- [ ] 1 akun admin (`admin@jagoankue.test` / `password`)
- [ ] 3 akun customer dengan data variatif
- [ ] Minimal 5 kategori
- [ ] Minimal 15 produk dengan gambar (boleh placeholder dari picsum/unsplash)
- [ ] Minimal 10 pesanan dummy dengan status bervariasi (untuk laporan terisi)
- [ ] 3 voucher (aktif, expired, habis kuota — untuk demo edge case)

### Tip: Gambar dummy

Pakai `https://picsum.photos/seed/{seed}/400/400` di seeder, simpan ke storage:

```php
$contents = file_get_contents("https://picsum.photos/seed/{$i}/400/400");
$path = "produk/dummy-{$i}.jpg";
Storage::disk('public')->put($path, $contents);
Produk::create([...'gambar' => $path]);
```

---

## Task 4.3 — Dokumentasi `README.md`

**Estimasi:** 0.5 hari

Buat `README.md` di root dengan struktur:

```markdown
# Jagoan Kue 🍰

E-commerce toko kue berbasis Laravel. Final project mata kuliah Pemrograman Web,
Informatika UNP 2025.

## Tech Stack
- Laravel 11
- MySQL 8
- Blade + Tailwind (atau CSS murni)
- Laravel Breeze (auth)

## Fitur Utama
### Customer
- Browse & cari produk kue
- Keranjang & checkout
- Riwayat pesanan
- Voucher diskon

### Admin
- Dashboard ringkasan
- CRUD produk, kategori, voucher
- Manajemen pesanan & status
- Manajemen user
- Laporan penjualan + export Excel

## Cara Menjalankan

```bash
git clone <repo-url>
cd jagoan-kue
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate

# setup database di .env, lalu:
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

Buka `http://127.0.0.1:8000`.

## Akun Demo
| Role | Email | Password |
|---|---|---|
| Admin | admin@jagoankue.test | password |
| Customer | budi@mail.test | password |

## Struktur Folder Penting
- `app/Http/Controllers/Admin/` → controller admin
- `resources/views/admin/` → view admin
- `database/seeders/` → data dummy

## ERD
[ERD.png di sini, kalau ada]

## Screenshots
[Tampilkan 4-5 screenshot halaman utama]
```

### DoD:
- [ ] README lengkap dengan langkah setup
- [ ] Akun demo dicantumkan
- [ ] Minimal 4 screenshot disertakan di folder `docs/screenshots/`

---

## Task 4.4 — Persiapan Deployment

**Estimasi:** 1 hari

### Pre-deploy checklist:

- [ ] `.env.example` lengkap dan terbaru (cek semua key yang dipakai)
- [ ] `APP_DEBUG=false` di production
- [ ] `APP_ENV=production`
- [ ] `APP_URL` di-set ke domain production
- [ ] Database production di-set
- [ ] `php artisan key:generate` sudah dijalankan
- [ ] `php artisan storage:link`
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] `npm run build` (compile asset production)
- [ ] Pastikan folder `storage/` dan `bootstrap/cache/` writable (777 atau owned by web user)

### Pilihan hosting:

| Hosting | Cocok untuk | Harga | Catatan |
|---|---|---|---|
| Localhost + ngrok | Demo saja | Gratis | Cukup untuk presentasi tugas |
| 000webhost / InfinityFree | Latihan | Gratis | Suka error PHP version |
| Niagahoster / IDCloudHost | Production | Rp 30k–100k/bln | Indonesia, support PHP 8.2 |
| Hostinger | Production | Rp 25k/bln+ | Mudah |
| Railway / Render | Modern dev | Gratis tier | Butuh paham Docker/CLI |
| VPS (DigitalOcean, Contabo) | Belajar lebih | $5/bln+ | Setup nginx sendiri |

**Rekomendasi untuk tugas kuliah: Niagahoster student / IDCloudHost** atau cukup demo via `php artisan serve` + screen recording.

### DoD:
- [ ] Aplikasi bisa diakses dari URL publik ATAU
- [ ] Screen recording demo 5-10 menit (kalau dosen tidak mensyaratkan deploy)

---

## Task 4.5 — Final Submission Checklist

**H-1 sebelum dikumpulkan:**

- [ ] Repo Git push terakhir, branch `main` up-to-date
- [ ] Tag release: `git tag v1.0.0 && git push --tags`
- [ ] README lengkap dengan info NIM, nama, kelas
- [ ] Laporan/dokumen pendukung (sistem informasi report) sudah final, di folder `docs/`
- [ ] File ERD (gambar) ada di `docs/`
- [ ] Folder ZIP siap (kalau dosen minta upload, bukan link Git): hapus `node_modules/`, `vendor/`, `.git/` sebelum di-ZIP
- [ ] Demo video 5-10 menit (rekam pakai OBS / Loom) — covering customer journey + admin journey
- [ ] Slide presentasi (kalau ada presentasi): 10-15 slide max, sesuaikan dengan palet warna proyek
- [ ] Backup database `mysqldump > jagoan_kue.sql`, simpan di `docs/`

---

# 📎 APPENDIX

## A. Naming Convention Final Project

| Item | Pola | Wajib? |
|---|---|---|
| Branch Git | `feature/nama`, `fix/nama`, `refactor/nama` | Ya |
| Commit message | `type(scope): pesan` (lihat 1.1.2) | Ya |
| File CSS class | `kebab-case` | Ya |
| Function/method name | `camelCase` | Ya |
| Constant | `UPPER_SNAKE_CASE` | Ya |

## B. Daftar Tabel & Relasi (isi setelah Task 1.1)

```
users
├── id, name, email, password, role, ...
├── hasMany → pesanans
└── hasMany → cart_items

kategoris
├── id, nama, slug
└── hasMany → produks

produks
├── id, kategori_id, nama, slug, harga, stok, gambar, ...
├── belongsTo → kategori
└── hasMany → detail_pesanans

pesanans
├── id, user_id, kode, status, total, alamat, ...
├── belongsTo → user
└── hasMany → detail_pesanans

detail_pesanans
├── id, pesanan_id, produk_id, qty, harga_satuan
├── belongsTo → pesanan
└── belongsTo → produk

vouchers
└── id, kode, tipe, nilai, ...
```

*(Sesuaikan dengan kondisi aktual project setelah audit Task 1.1.)*

## C. BUGS_DITEMUKAN

> Isi dengan format: `[severity] deskripsi — ditemukan di task X.Y`
> Severity: HIGH (blocker), MEDIUM (kelihatan tapi bisa lanjut), LOW (cosmetic).

- [ ] _(kosong dulu, isi sambil jalan)_

## D. CATATAN_KEPUTUSAN_TEKNIS

> Setiap keputusan teknis yang menyimpang dari plan ini, catat di sini biar tim tahu.

Contoh:
- 2026-MM-DD: Memilih pakai CSS murni (bukan Tailwind) karena waktu sempit. Konsekuensi: maintain `app.css` manual.

## E. Quick Reference Command

```bash
# Development
php artisan serve                                  # jalankan dev server
php artisan migrate:fresh --seed                   # reset DB + isi dummy
php artisan tinker                                 # REPL untuk test query
php artisan route:list --except-vendor             # cek route
php artisan make:controller Admin/XxxController --resource --model=Xxx
php artisan make:request XxxRequest
php artisan make:migration nama_migrasi
php artisan make:seeder NamaSeeder

# Storage
php artisan storage:link                           # WAJIB setelah clone

# Cache
php artisan optimize:clear                         # clear semua cache
php artisan config:cache                           # cache config (production)

# Production build
npm run build                                      # compile asset
```

## F. Pesan untuk AI Agent

> Jika kamu adalah AI coding agent yang mengerjakan task dari dokumen ini:
>
> 1. **Baca task lengkap sebelum mulai**, termasuk DoD-nya.
> 2. **Jangan asumsi**. Kalau ada yang ambigu, tanya ke manusia (Zidane) atau catat asumsi di `CATATAN_KEPUTUSAN_TEKNIS`.
> 3. **Jangan refactor di luar scope**. Kalau lihat kode jelek di file lain, catat di `BUGS_DITEMUKAN`, jangan langsung benerin.
> 4. **Jalankan kode setelah ditulis**. `php artisan serve` lalu buka browser. Kalau ada error di `storage/logs/laravel.log`, baca dan fix.
> 5. **Konfirmasi DoD per task** sebelum lanjut ke task berikutnya.
> 6. **Commit per task**, jangan numpuk perubahan.

---

**Selamat mengerjakan! 🚀**
*— Disusun oleh: Senior dev (Claude). Pemilik proyek: Zidane.*
