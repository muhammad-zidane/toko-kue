Kamu adalah Laravel developer berpengalaman.
Ikuti instruksi dengan KETAT. Jangan berasumsi di luar yang tertulis.

---

## Planning

## Context
Proyek  : Jagoan Kue (E-commerce pemesanan kue)
Stack   : Laravel 11, MySQL, Blade, Tailwind CSS
Fitur   : Seksi Testimoni di halaman detail produk (`products.show`)

---

## Yang Akan Dibuat
Sistem testimoni yang memungkinkan user yang sudah membeli
suatu produk memberikan ulasan berupa rating dan komentar.
Testimoni ditampilkan dalam bentuk carousel geser kanan ke kiri.

---

## Functional Requirements
- [ ] Hanya user yang memiliki riwayat pembelian produk tersebut
        (status order = 'selesai') yang bisa submit testimoni
- [ ] Satu user hanya boleh memiliki satu testimoni per produk;
        form tidak ditampilkan jika sudah pernah memberi testimoni
- [ ] Form testimoni berisi: rating bintang (1–5) dan komentar teks
- [ ] Tanggal pembelian diambil otomatis dari data order, bukan diisi user
- [ ] Testimoni langsung tampil tanpa perlu persetujuan admin
- [ ] User bisa mengedit testimoninya sendiri
- [ ] User bisa menghapus testimoninya sendiri
- [ ] Testimoni ditampilkan sebagai carousel auto-scroll kanan ke kiri

---

## Technical Specification

### Database
Buat migration tabel baru `testimonials`:
- id               (bigint, primary key)
- user_id          (FK → users.id)
- product_id       (FK → products.id)
- order_id         (FK → orders.id)
- rating           (tinyint, 1–5)
- comment          (text)
- created_at, updated_at

Unique constraint: (user_id, product_id) — mencegah duplikasi

### Model
- Buat Model `Testimonial`
- Relasi: belongsTo User, belongsTo Product, belongsTo Order

### Routes
- POST   /testimonials              → store
- PUT    /testimonials/{id}         → update
- DELETE /testimonials/{id}         → destroy
Semua route pakai middleware `auth`

### Controller
Buat `TestimonialController` dengan method:
- store()   → validasi + cek kepemilikan order + cek duplikasi
- update()  → validasi + cek ownership (hanya milik sendiri)
- destroy() → cek ownership lalu hapus

### View
Di `products/show.blade.php`, tambahkan seksi baru:
1. Komponen carousel (HTML + CSS Tailwind + JS vanilla)
   menggunakan auto-scroll marquee dari kanan ke kiri
2. Form testimoni (tampil hanya jika user login DAN pernah beli
   DAN belum pernah beri testimoni)
3. Tombol Edit dan Hapus (tampil hanya pada testimoni milik
   user yang sedang login)

---

## Constraints
- Jangan ubah struktur tabel orders, products, atau users
- Jangan install package JS/CSS baru; gunakan Tailwind + vanilla JS
- Semua view extend layout yang sudah ada (`layouts.app`)
- Validasi server-side wajib ada di Controller
- Cek kepemilikan order wajib dilakukan sebelum izinkan submit

---

## Definition of Done
- [ ] Migration berhasil dijalankan tanpa error
- [ ] User yang belum beli tidak bisa submit (form tidak muncul)
- [ ] User yang sudah beri testimoni tidak bisa submit lagi
- [ ] Edit dan hapus hanya bisa dilakukan pada testimoni milik sendiri
- [ ] Carousel berjalan otomatis geser kanan ke kiri
- [ ] Tidak ada error di console browser maupun Laravel log

---

## Tugasmu
Implementasi SELURUH planning di atas secara lengkap dan berurutan.

Sebelum mulai coding, tampilkan dulu checklist file yang akan
kamu buat/ubah beserta urutannya, lalu minta konfirmasiku.
Setelah aku konfirmasi, baru mulai implementasi satu per satu.

---

## Aturan Wajib
- Tampilkan satu file per respons, tunggu konfirmasi "lanjut" dariku
- Jika ada yang ambigu di planning, TANYAKAN SEKARANG sebelum mulai
- Jangan install package baru
- Jangan ubah file yang tidak disebutkan di planning
- Jangan skip langkah meskipun terlihat sederhana

---

## Struktur Proyek Saat Ini