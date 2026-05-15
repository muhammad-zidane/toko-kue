# 📋 Planning Implementasi — Toko Kue (Issue Tracker)

> Dokumen ini berisi daftar issue yang perlu diimplementasikan oleh AI agent.
> Setiap issue memiliki konteks, acceptance criteria, dan catatan teknis yang diperlukan.

---

## ISSUE #1 — Tambah ke Keranjang Tanpa Redirect + Badge Indikator Navbar

**Label:** `enhancement` `UX` `cart`
**Prioritas:** High

### Deskripsi
Saat ini, ketika user menambahkan produk ke keranjang dari halaman detail produk, user diarahkan ke halaman keranjang. Perilaku ini perlu diubah agar user tetap berada di halaman detail produk. Sebagai gantinya, tampilkan badge angka (jumlah item) pada ikon keranjang di navbar.

### Acceptance Criteria
- [ ] Tombol "Tambah ke Keranjang" pada halaman detail produk **tidak** melakukan redirect ke halaman keranjang.
- [ ] Setelah produk ditambahkan, user tetap berada di halaman detail produk.
- [ ] Ikon keranjang di navbar menampilkan **badge angka** yang menunjukkan total item di keranjang.
- [ ] Badge diperbarui secara real-time setiap kali item ditambahkan atau dihapus dari keranjang.
- [ ] Badge tidak muncul (atau menampilkan 0) jika keranjang kosong.

### Catatan Teknis
- Gunakan state global keranjang (misalnya context atau zustand) yang sudah ada untuk membaca jumlah item.
- Badge dapat menggunakan komponen kecil dengan posisi `absolute` di atas ikon keranjang di navbar.
- Pastikan state keranjang ter-sinkronisasi antara halaman detail produk dan navbar.

---

## ISSUE #2 — Tanggal Jadwal Pengiriman Otomatis (H+2) + Tombol Kalender + Validasi

**Label:** `enhancement` `form` `validation`
**Prioritas:** High

### Deskripsi
Pada bagian jadwal pengiriman/pengambilan, field tanggal perlu diisi otomatis dengan tanggal 2 hari setelah tanggal pemesanan (hari ini + 2). Tambahkan juga tombol kalender (date picker) dan validasi agar user tidak bisa memilih tanggal kurang dari H+2.

### Acceptance Criteria
- [ ] Field tanggal pengiriman/pengambilan otomatis terisi dengan **tanggal hari ini + 2 hari** saat halaman dimuat.
- [ ] Terdapat tombol/ikon kalender yang membuka date picker saat diklik.
- [ ] User **tidak bisa** memilih tanggal sebelum H+2 (tanggal sebelumnya di-disable).
- [ ] Jika user mengubah tanggal ke tanggal yang tidak valid (kurang dari H+2), tampilkan pesan error: *"Tanggal pengiriman minimal 2 hari setelah tanggal pemesanan."*
- [ ] Validasi berjalan sebelum user bisa melanjutkan ke langkah berikutnya.

### Catatan Teknis
- Gunakan library date picker yang sudah ada dalam project (misalnya `react-datepicker` atau native `<input type="date">`).
- Hitung tanggal minimum: `new Date(Date.now() + 2 * 24 * 60 * 60 * 1000)`.
- Set atribut `min` pada input date ke tanggal tersebut.

---

## ISSUE #3 — Fitur DP 50% + Sisa Pembayaran di Detail Pesanan + Pengingat di Kartu Pesanan

**Label:** `enhancement` `payment` `orders`
**Prioritas:** High

### Deskripsi
Tambahkan fitur opsi pembayaran DP 50%. Jika user memilih opsi ini, harga yang harus dibayar sekarang terpotong 50%. Sisa pembayaran harus ditampilkan di halaman "Pesanan Saya" pada bagian detail pesanan. Selain itu, tambahkan pengingat pembayaran sisa pada kartu pesanan di halaman "Pesanan Saya".

### Acceptance Criteria
- [ ] Terdapat checkbox atau toggle bertuliskan *"Bayar 50% DP Sekarang"* pada halaman pembayaran/checkout.
- [ ] Jika dicentang, total yang ditampilkan berubah menjadi **50% dari total harga**.
- [ ] Data pilihan DP tersimpan bersama data pesanan.
- [ ] Di halaman **Pesanan Saya → Detail Pesanan**, terdapat baris *"Sisa Pembayaran"* yang menampilkan jumlah 50% yang belum dibayar (jika user memilih DP).
- [ ] Pada **kartu pesanan** di halaman Pesanan Saya, terdapat indikator/pengingat *"Sisa pembayaran: Rp X"* jika pesanan menggunakan DP.
- [ ] Jika pesanan dibayar penuh, tidak ada pengingat sisa pembayaran.

### Catatan Teknis
- Simpan field `is_dp: boolean` dan `dp_amount: number` pada model pesanan.
- Sisa pembayaran = `total_harga - dp_amount`.
- Pengingat pada kartu pesanan bisa berupa badge atau teks berwarna oranye/merah.

---

## ISSUE #4 — Ubah Kode Unik dari Rp 22 menjadi Rp 1.000 + Tampilkan di Ringkasan Pesanan

**Label:** `bug` `payment`
**Prioritas:** Medium

### Deskripsi
Nilai kode unik saat ini adalah Rp 22. Nilai ini perlu diubah menjadi Rp 1.000. Selain itu, kode unik belum ditampilkan pada ringkasan pesanan di halaman pembayaran — perlu ditambahkan.

### Acceptance Criteria
- [ ] Nilai kode unik diubah dari **Rp 22** menjadi **Rp 1.000** di seluruh bagian aplikasi.
- [ ] Kode unik ditampilkan secara eksplisit pada **ringkasan pesanan di halaman pembayaran**, lengkap dengan label dan nilainya.
- [ ] Total akhir pada ringkasan pesanan sudah menyertakan kode unik (subtotal + ongkir + biaya layanan + kode unik).

### Catatan Teknis
- Cari dan ganti semua konstanta/nilai kode unik (misalnya `UNIQUE_CODE = 22`) menjadi `1000`.
- Pastikan kode unik muncul sebagai baris tersendiri di komponen ringkasan pesanan, bukan tersembunyi dalam total.

---

## ISSUE #5 — Catatan Pesanan Tidak Muncul di Ringkasan Pembayaran

**Label:** `bug` `checkout`
**Prioritas:** Medium

### Deskripsi
Catatan per pesanan yang diisi oleh user pada halaman sebelumnya tidak muncul pada bagian ringkasan pesanan di halaman pembayaran.

### Acceptance Criteria
- [ ] Catatan yang diisi user pada langkah sebelumnya **ditampilkan** di ringkasan pesanan pada halaman pembayaran.
- [ ] Jika tidak ada catatan, bagian catatan tidak perlu ditampilkan (opsional/kondisional).

### Catatan Teknis
- Periksa apakah data catatan sudah diteruskan ke state/context pembayaran.
- Pastikan komponen ringkasan pesanan membaca dan merender field catatan dari state.

---

## ISSUE #6 — Default Ongkir: Rp 25.000 (Diantar) / Gratis (Ambil di Toko)

**Label:** `enhancement` `shipping`
**Prioritas:** Medium

### Deskripsi
Ongkos kirim perlu dibedakan berdasarkan metode pengambilan:
- Jika **diantar**: ongkir default Rp 25.000
- Jika **ambil di toko**: ongkir Gratis (Rp 0)

### Acceptance Criteria
- [ ] Saat user memilih metode **"Diantar"**, ongkir otomatis terisi **Rp 25.000**.
- [ ] Saat user memilih metode **"Ambil di Toko"**, ongkir otomatis menjadi **Rp 0 / Gratis**.
- [ ] Nilai ongkir diperbarui secara real-time saat user mengganti metode pengiriman.
- [ ] Ongkir yang sesuai tercermin pada ringkasan pesanan dan total harga.

### Catatan Teknis
- Gunakan kondisional: `shippingCost = deliveryMethod === 'diantar' ? 25000 : 0`.
- Pastikan perubahan metode pengiriman memicu re-kalkulasi total.

---

## ISSUE #7 — Validasi Upload Gambar (JPG/PNG, Maks 2MB)

**Label:** `enhancement` `validation` `upload`
**Prioritas:** Medium

### Deskripsi
Upload gambar saat ini tidak memiliki validasi format dan ukuran file. Perlu ditambahkan validasi bahwa hanya file JPG dan PNG yang diterima, dengan ukuran maksimal 2MB.

### Acceptance Criteria
- [ ] Hanya file berformat **`.jpg` / `.jpeg`** dan **`.png`** yang diterima.
- [ ] Ukuran file maksimal adalah **2MB**. File lebih besar dari 2MB ditolak.
- [ ] Jika format tidak valid, tampilkan pesan error: *"Format file tidak didukung. Gunakan JPG atau PNG."*
- [ ] Jika ukuran melebihi batas, tampilkan pesan error: *"Ukuran file maksimal 2MB."*
- [ ] Validasi terjadi **sebelum** file dikirim ke server (validasi sisi klien).

### Catatan Teknis
- Validasi format: cek `file.type === 'image/jpeg' || file.type === 'image/png'`.
- Validasi ukuran: cek `file.size <= 2 * 1024 * 1024`.
- Tambahkan atribut `accept="image/jpeg,image/png"` pada input file sebagai petunjuk awal.

---

## ISSUE #8 — Ubah Warna Kartu di Halaman Sukses + Ikon Ceklis

**Label:** `UI` `styling`
**Prioritas:** Low

### Deskripsi
Kartu-kartu berwarna cream di halaman sukses perlu diubah warnanya menjadi putih. Ikon ceklis juga perlu disesuaikan: ikon berwarna cream dengan latar lingkaran berwarna pink.

### Acceptance Criteria
- [ ] Semua kartu di halaman sukses yang sebelumnya berwarna **cream** diubah menjadi **putih** (`#FFFFFF` atau `bg-white`).
- [ ] Ikon ceklis menggunakan warna **cream** (misal `#F5F0E8` atau sesuai design token).
- [ ] Latar ikon ceklis berupa **lingkaran berwarna pink** (misal `#F9A8D4` atau sesuai design token).

### Catatan Teknis
- Cari komponen kartu di halaman sukses dan ganti class warna background.
- Bungkus ikon ceklis dengan `<div>` berbentuk lingkaran (rounded-full) dengan background pink.

---

## ISSUE #9 — Detail Pesanan: Tampilkan Rincian Harga Lengkap

**Label:** `enhancement` `orders`
**Prioritas:** High

### Deskripsi
Setiap bagian detail pesanan (baik di halaman Pesanan Saya maupun konfirmasi) harus menampilkan rincian harga secara lengkap dan konsisten.

### Acceptance Criteria
Setiap tampilan detail pesanan wajib mencantumkan baris-baris berikut secara eksplisit:
- [ ] **Harga Produk** (harga satuan × jumlah per item)
- [ ] **Ongkos Kirim**
- [ ] **Sub Total** (jumlah harga produk sebelum biaya tambahan)
- [ ] **Biaya Layanan** (kode unik / biaya admin)
- [ ] **Total Harga** (grand total keseluruhan)

### Catatan Teknis
- Pastikan komponen detail pesanan menerima semua field ini dari data pesanan.
- Jika ada field yang belum tersimpan di database, tambahkan ke schema pesanan.

---

## ISSUE #10 — Sesuaikan Teks Status Bayar dengan Ukuran Latar

**Label:** `UI` `styling`
**Prioritas:** Low

### Deskripsi
Teks pada badge/label status pembayaran tidak sesuai dengan ukuran container/latar belakangnya (terlalu besar, terlalu kecil, atau overflow).

### Acceptance Criteria
- [ ] Teks status bayar (misalnya "Lunas", "DP", "Belum Bayar") sesuai dan tidak overflow dari latar/badge-nya.
- [ ] Ukuran font, padding, dan lebar badge disesuaikan agar proporsional di semua kondisi teks.
- [ ] Tampilan konsisten di semua tempat badge status bayar muncul (kartu pesanan, detail pesanan, dll).

### Catatan Teknis
- Gunakan `whitespace-nowrap` dan `px-2 py-1 text-xs` atau sesuaikan dengan design system yang ada.
- Periksa apakah badge menggunakan lebar fixed (`w-20`, dll) yang menyebabkan overflow — ganti ke `w-fit` atau padding-based sizing.

---

## Ringkasan Issue

| No | Judul | Label | Prioritas |
|----|-------|-------|-----------|
| #1 | Tambah ke Keranjang Tanpa Redirect + Badge Navbar | enhancement, UX, cart | High |
| #2 | Tanggal Pengiriman Otomatis H+2 + Kalender + Validasi | enhancement, form, validation | High |
| #3 | Fitur DP 50% + Sisa Bayar + Pengingat Kartu Pesanan | enhancement, payment, orders | High |
| #4 | Kode Unik Rp 22 → Rp 1.000 + Tampil di Ringkasan | bug, payment | Medium |
| #5 | Catatan Pesanan Tidak Muncul di Ringkasan Bayar | bug, checkout | Medium |
| #6 | Default Ongkir Rp 25.000 / Gratis (Ambil di Toko) | enhancement, shipping | Medium |
| #7 | Validasi Upload Gambar JPG/PNG Maks 2MB | enhancement, validation, upload | Medium |
| #8 | Kartu Halaman Sukses: Putih + Ikon Ceklis Pink | UI, styling | Low |
| #9 | Detail Pesanan: Rincian Harga Lengkap | enhancement, orders | High |
| #10 | Sesuaikan Teks Status Bayar dengan Latar | UI, styling | Low |
