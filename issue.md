# Perencanaan Implementasi Animasi UI/UX

## Ringkasan Tugas
Tugas ini bertujuan untuk menambahkan animasi pada web untuk meningkatkan pengalaman pengguna (UX) agar terasa lebih dinamis, modern, dan profesional. Seluruh animasi **harus** dibuat menggunakan native CSS dan JavaScript (tanpa library eksternal seperti GSAP, jQuery, atau Animate.css). Animasi harus berjalan dengan mulus (smooth) dan tidak mengganggu navigasi, fungsionalitas, maupun waktu muat halaman.

## Skenario Animasi

### 1. Animasi Loading & Fade-in (Saat Halaman Terbuka)
*   **Target Halaman:** Semua halaman utama (Beranda, Daftar Produk, Detail Produk, Keranjang, dll).
*   **Skenario:**
    *   Saat halaman sedang memuat aset (sebelum DOM Ready), tampilkan indikator loading yang simpel dan tidak mencolok di tengah layar (misalnya spinner kecil atau logo yang berdenyut/pulse).
    *   Setelah halaman selesai dimuat, hilangkan elemen loading tersebut dengan efek fade-out yang halus.
    *   Segera setelah loading hilang, konten utama halaman harus muncul menggunakan efek *fade-in* (transisi *opacity* dari 0 ke 1) yang dikombinasikan dengan sedikit *slide-up* (bergeser naik dari bawah) agar terasa dinamis.
*   **Catatan:** Durasi animasi masuk ini harus cukup cepat agar pengunjung tidak merasa menunggu.

### 2. Scroll Animation (Muncul Saat Di-scroll)
*   **Target Halaman:** Beranda, Halaman Produk, dan halaman dengan konten panjang.
*   **Skenario:**
    *   Gunakan JavaScript `IntersectionObserver` untuk mendeteksi elemen mana saja yang mulai masuk ke dalam area pandang (*viewport*) saat pengguna melakukan scroll.
    *   Elemen-elemen di bawah lipatan (*below the fold*) seperti banner promosi, daftar kategori, atau baris produk harus dalam keadaan tersembunyi (opacity 0 dan posisinya sedikit lebih rendah).
    *   Saat elemen tersebut di-scroll dan masuk ke layar, picu kelas CSS untuk memberikan animasi muncul (misalnya *fade-in up* atau *fade-in* dari samping).
    *   Untuk elemen yang berjejer dalam satu *grid* (misalnya daftar produk), gunakan efek *staggering* (jeda waktu kemunculan per elemen) sehingga elemen tidak muncul serentak, melainkan bergantian secara berurutan.

### 3. Hover Effect (Efek Saat Kursor Diarahkan)
*   **Target Halaman:** Semua halaman yang memiliki elemen interaktif (tombol, link, kartu).
*   **Skenario:**
    *   **Tombol (Buttons):** Berikan umpan balik visual saat kursor berada di atas tombol. Misalnya, transisi warna latar belakang, penambahan *box-shadow* yang membuatnya seolah terangkat, atau efek pergeseran skala membesar sedikit.
    *   **Card Produk:** Saat kartu produk di-hover, buat kartu seolah-olah mendekat ke pengguna (menggunakan transform *scale* ringan) dan tambahkan bayangan yang lebih tegas. Bisa juga dengan memunculkan tombol "Tambah" atau ikon aksi yang awalnya tersembunyi.
    *   **Navigasi/Link:** Pada tautan navbar, berikan efek kemunculan garis bawah (*underline*) yang dianimasikan dari arah tengah, kiri, atau kanan secara mulus.
*   **Catatan:** Pada perangkat *mobile* (layar sentuh), pastikan efek ini diadaptasi agar tidak menimbulkan *bug* klik ganda (*double-tap issue*).

### 4. Page Transition (Transisi Antar Halaman)
*   **Target Halaman:** Seluruh perpindahan halaman di dalam aplikasi.
*   **Skenario:**
    *   Saat pengguna mengklik tautan untuk berpindah halaman, tunda sedikit proses navigasi default browser.
    *   Jalankan animasi *fade-out* singkat pada keseluruhan konten halaman yang sedang aktif.
    *   Setelah halaman aktif benar-benar *fade-out*, lanjutkan proses perpindahan halaman standar. (Animasi masuk untuk halaman berikutnya akan di-handle oleh poin nomor 1 di atas).
*   **Catatan:** Skenario ini harus sangat optimal dan berdurasi pendek agar tidak membuat transisi halaman terasa memakan waktu atau lemot.

## Kriteria Penerimaan (Acceptance Criteria)
1.  **Teknologi:** Hanya menggunakan properti CSS (seperti `transition`, `animation`, `@keyframes`) dan native API JavaScript (`IntersectionObserver`, `Event Listener`).
2.  **Performa:** Animasi dioptimalkan menggunakan *hardware acceleration*. Utamakan untuk menganimasi properti `transform` dan `opacity` guna menghindari *repaint* dan *reflow* berlebih pada *rendering* browser.
3.  **Kenyamanan Pengguna:** Animasi terasa konsisten, tidak berlebihan, dan tidak mengganggu alur berbelanja pengguna. 
4.  **Responsivitas:** Berjalan baik di desktop maupun perangkat mobile.
