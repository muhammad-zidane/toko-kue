# Issue: Refactor CSS & JS — Pisahkan dari File Blade

## Deskripsi
Saat ini semua CSS dan JavaScript pada project **Toko Kue** masih ditulis secara inline di dalam file Blade. Perlu dipisahkan ke file eksternal agar lebih rapi, mudah di-maintain, dan dapat di-cache oleh browser.

---

## Struktur File yang Harus Dibuat

```
public/
├── css/
│   ├── app.css        ← style global (navbar, footer, typography, variabel warna palette cream/brown/pink)
│   ├── auth.css       ← khusus halaman login & register
│   └── admin.css      ← khusus halaman admin dashboard
└── js/
    ├── app.js         ← script global (toggle navbar, flash message, interaksi umum)
    └── admin.js       ← khusus admin (konfirmasi hapus, interaksi dashboard)
```

---

## Langkah-Langkah yang Harus Dilakukan

### 1. Scan semua file Blade
Temukan semua tag `<style>`, `<script>`, dan inline CSS/JS di dalam folder `resources/views/`.

### 2. Ekstrak dan kelompokkan
- CSS/JS yang dipakai di semua halaman → `app.css` / `app.js`
- CSS/JS yang hanya dipakai di login/register → `auth.css`
- CSS/JS yang hanya dipakai di halaman admin → `admin.css` / `admin.js`

### 3. Update file layout utama
`resources/views/layouts/app.blade.php` (atau sejenisnya):

```html
{{-- Di dalam <head> --}}
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@stack('styles')

{{-- Sebelum </body> --}}
<script src="{{ asset('js/app.js') }}" defer></script>
@stack('scripts')
```

### 4. Update halaman auth
`login.blade.php`, `register.blade.php` — hapus `<style>` inline, ganti dengan:

```blade
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush
```

### 5. Update halaman admin
Semua file di `resources/views/admin/` — hapus `<style>` dan `<script>` inline, ganti dengan:

```blade
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@push('scripts')
  <script src="{{ asset('js/admin.js') }}" defer></script>
@endpush
```

### 6. Verifikasi
Pastikan tidak ada CSS/JS yang hilang — semua style dan script yang diekstrak harus tetap berfungsi sama seperti sebelumnya.

---

## Batasan (Jangan Diubah)
- Jangan ubah logika PHP, struktur HTML, nama class CSS, routing, atau apapun selain pemindahan CSS/JS
- Jika ada CSS yang sama ditulis berulang di beberapa Blade, cukup tulis sekali di `app.css`
- Jika menggunakan CDN (Bootstrap, FontAwesome, dll) — biarkan tetap di layout, jangan dipindah
- Pertahankan semua variabel warna palette project: cream (`#F5F0E8` atau sejenisnya), brown, pink

---

## Output yang Diharapkan
Setelah selesai, tampilkan:
- Daftar file Blade yang diubah
- Ringkasan CSS/JS mana yang dipindahkan ke file mana
- Konfirmasi semua `@push` dan `@stack` sudah terhubung dengan benar
