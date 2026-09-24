# Audit Report - toko-kue

## Executive Summary

Laporan ini mencatat kondisi setelah verifikasi ulang seluruh 12 temuan awal Critical/High terhadap working tree repository. Tidak ada temuan baru ditambahkan. Pembaruan hanya mengedit AUDIT_REPORT.md; tidak mengubah source code, memperbaiki bug, melakukan refactor, membuat branch, commit, atau Pull Request.

| Klasifikasi terbaru | Jumlah |
| --- | ---: |
| Confirmed Critical | 2 |
| Confirmed High | 8 |
| Confirmed Medium | 16 |
| Confirmed Low | 1 |
| Needs Further Verification | 2 |
| False Positives | 1 subklaim PAY-004; 0 temuan induk sepenuhnya dibantah |

**Rekonsiliasi:** Tetap ada 29 temuan induk: 27 Confirmed dan 2 Needs Further Verification. Satu subklaim PAY-004 disimpan sebagai histori False Positive, bukan temuan induk ke-30. ORDER-003 dan TEST-002 turun dari High ke Medium; PAY-004 tetap High dengan execution path terkoreksi; ORDER-004 dibatasi pada pembatalan sebelum produksi.

Confirmed berarti jalur kegagalan terbukti dari kode/schema repository, bukan insiden production atau seluruh test reproduksi sudah dijalankan. Medium/Low yang sebelumnya Terbukti mempertahankan dasar audit awal, bukan diklaim menjalani verifikasi ulang 12 temuan Critical/High. UPLOAD-001 dan SEC-001 sebelumnya Risiko nyata dan belum memperoleh pembuktian tambahan, sehingga kini Needs Further Verification. Tidak ada dari 12 temuan yang diverifikasi ulang terakhir dipindahkan seluruhnya ke kategori tersebut.

Masalah utama tetap berupa kehilangan histori melalui cascade delete, stok negatif akibat ID produk berulang, COD dianggap lunas sebelum penerimaan uang, pelunasan DP terblokir, dan state Order/Payment tidak konsisten.

Batas verifikasi:

- Route, middleware, controller/service, model, migration/constraint, provider/observer, view, dan test relevan diperiksa. Suite Pest/PHPUnit/Bun, migrasi, dan request mutasi tidak dijalankan.
- Pemeriksaan PHP tanpa database mengonfirmasi Carbon, parsing customization, dan dirty tracking Payment. Percobaan SQL dry-run tidak selesai karena koneksi diblokir dan tidak digunakan sebagai bukti.
- Schema/FK/engine production aktual belum diperiksa; penilaian mengacu pada migration repository dalam konteks MySQL/InnoDB.
- Test yang disarankan merupakan rencana pembuktian/regresi, bukan hasil test lulus. Jalankan hanya pada lingkungan disposable terisolasi.
- Tidak ditemukan bukti auth bypass, privilege escalation, atau RCE pada jalur yang diperiksa; ini bukan jaminan menyeluruh.
- Referensi baris mengacu pada working tree saat audit/verifikasi dan dapat bergeser setelah pengembangan berikutnya.

## Confirmed Critical Findings

### DB-001 — Hapus akun menghapus histori order dan pembayaran

- **ID:** DB-001
- **Judul:** Hapus akun menghapus histori order dan pembayaran.
- **Severity:** Critical
- **Status:** Confirmed
- **File:** `app/Http/Controllers/ProfileController.php:52`; `database/migrations/2026_04_11_082209_create_orders_table.php:13`; `database/migrations/2026_04_11_082233_create_payments_table.php:13`.
- **Function/Class:** `ProfileController::destroy()` dan migration foreign keys.
- **Execution Path:** Customer menghapus akun → user hard-delete → orders cascade → payments, order_items, dan reviews cascade.
- **Bukti kode:** Controller menjalankan `$user->delete()`. FK `orders.user_id` memakai `onDelete('cascade')`; payment dan item juga mengikuti penghapusan order. Tidak ada soft delete atau pemeriksaan histori transaksi pada jalur tersebut.
- **Dampak:** Customer dengan order lunas atau aktif dapat menghapus histori pembayaran, invoice, dan rincian produksi milik toko; laporan revenue historis ikut berubah.
- **Cara reproduksi:** Pada database disposable, buat customer dengan order/payment, kemudian DELETE `/profile` menggunakan password valid. Periksa record transaksi setelah akun dihapus.
- **Rekomendasi perbaikan minimal:** Pertahankan transaksi melalui anonimisasi/soft delete atau FK nullable/restrict sesuai kebijakan retensi.
- **Test yang disarankan:** Account deletion harus mempertahankan record transaksi dan total laporan, termasuk untuk order aktif dan lunas.
- **Hasil verifikasi terakhir:** Confirmed berdasarkan schema repository. Auth dan current_password melindungi identitas pelaku, bukan retensi pembukuan. Tidak ada SoftDeletes, observer penghalang, archival service, atau migration pengganti FK. ProfileTest hanya menghapus User tanpa transaksi. FK deployment aktual belum diperiksa.

### DB-002 — Penghapusan katalog menghapus detail transaksi historis

- **ID:** DB-002
- **Judul:** Penghapusan katalog menghapus detail transaksi historis.
- **Severity:** Critical
- **Status:** Confirmed
- **File:** `app/Http/Controllers/ProductController.php:162`; `app/Http/Controllers/Admin/CategoryController.php:78`; `app/Http/Controllers/Admin/CustomizationController.php:79`; `database/migrations/2026_04_11_082220_create_order_items_table.php:14`; `database/migrations/2026_05_15_134403_create_order_item_customizations_table.php:17`.
- **Function/Class:** Product/Category/Customization `destroy()` dan migration foreign keys.
- **Execution Path:** Admin menghapus produk/kategori → products terhapus → order_items cascade. Penghapusan opsi → order_item_customizations cascade.
- **Bukti kode:** FK `products.category_id`, `order_items.product_id`, dan `order_item_customizations.customization_option_id` menggunakan cascade. Endpoint delete tidak memeriksa penggunaan historis. Nama produk/opsi tidak disnapshot pada transaksi.
- **Dampak:** Order dan totalnya dapat tetap ada ketika rincian pembentuk total hilang. Invoice, histori penjualan, dan instruksi produksi menjadi tidak lengkap.
- **Cara reproduksi:** Buat order dengan customization, lalu hapus produk, kategori, atau opsi yang direferensikan. Bandingkan rincian order sebelum dan sesudah.
- **Rekomendasi perbaikan minimal:** Larang hard-delete entitas terpakai atau gunakan soft delete; simpan snapshot identitas produk/opsi pada transaksi.
- **Test yang disarankan:** Rincian historis dan rekonsiliasi subtotal tetap utuh setelah perubahan atau penghapusan katalog.
- **Hasil verifikasi terakhir:** Critical terutama untuk penghapusan produk/kategori. Penghapusan opsi saja lebih terbatas. Order/Payment induk tidak ikut terhapus saat produk dihapus; rincian pembentuk total yang hilang. Auth + admin sudah benar, tetapi tidak menjaga histori. ProductAdminTest menghapus produk tanpa order item; tidak ditemukan soft delete, observer penghalang, atau migration pengganti FK.

## Confirmed High Findings

### ORDER-001 — Duplikasi product_id membuat stok negatif

- **ID:** ORDER-001
- **Judul:** Duplikasi product_id membuat stok negatif.
- **Severity:** High
- **Status:** Confirmed
- **File:** `app/Services/OrderService.php:49`; `app/Http/Controllers/OrderController.php:66`; `database/migrations/2026_04_11_082202_create_products_table.php:18`.
- **Function/Class:** `OrderService::placeOrder()`, `OrderController::store()`.
- **Execution Path:** POST `/orders` → validation → lock produk → pemeriksaan seluruh item → pembuatan item dan pengurangan stok.
- **Bukti kode:** Loop baris 55–65 memeriksa setiap quantity terhadap stok awal. Pengurangan baru dilakukan pada loop baris 111–135. `items.*.product_id` tidak menggunakan `distinct`; stok berupa integer tanpa constraint nonnegatif. Lock antartransaksi tidak memperbaiki validasi agregat dalam satu transaksi.
- **Dampak:** Stok 5 dengan dua baris produk sama masing-masing quantity 4 menghasilkan stok −3 dan overselling.
- **Cara reproduksi:** Checkout valid dengan `items=[{product_id:P,quantity:4},{product_id:P,quantity:4}]` saat stok P=5.
- **Rekomendasi perbaikan minimal:** Agregasikan quantity per produk sebelum validasi stok; tetap dukung pemisahan item berdasarkan konfigurasi bila diperlukan.
- **Test yang disarankan:** Request ditolak, tidak ada order/payment baru, dan stok tetap 5 ketika total quantity produk melebihi stok.
- **Hasil verifikasi terakhir:** Cart normal di-key product_id, tetapi POST menggunakan items langsung. keyBy hasil query bukan deduplikasi payload. Lock dan transaction tidak memperbaiki validasi agregat dalam satu request; schema stok signed integer tanpa constraint nonnegatif. Belum ada test duplicate product ID.

### ORDER-002 — Produk unavailable dapat dipesan melalui POST langsung

- **ID:** ORDER-002
- **Judul:** Produk unavailable dapat dipesan melalui POST langsung.
- **Severity:** High
- **Status:** Confirmed
- **File:** `app/Services/OrderService.php:55`; `app/Http/Controllers/OrderController.php:67`; `app/Http/Controllers/CartController.php:149`.
- **Function/Class:** `OrderService::placeOrder()`, `OrderController::store()`, `CartController::checkout()`.
- **Execution Path:** Customer → POST `/orders` → `exists:products,id` → pemeriksaan stok → order dibuat.
- **Bukti kode:** `is_available` diperiksa pada GET checkout cart, tetapi tidak pada `store()` atau `placeOrder()`. Model tidak memiliki global scope yang mengecualikan produk unavailable.
- **Dampak:** Produk yang dinonaktifkan admin masih dapat dijual, termasuk ketika availability berubah setelah halaman checkout dibuka.
- **Cara reproduksi:** POST checkout produk `is_available=false`, stok 10, quantity 1.
- **Rekomendasi perbaikan minimal:** Periksa availability pada produk yang sudah dikunci di dalam transaksi.
- **Test yang disarankan:** Assert validation error dan tidak ada perubahan order, payment, atau stok untuk produk unavailable.
- **Hasil verifikasi terakhir:** Tidak ada global scope availability pada Product, constraint, atau middleware pelengkap. Test katalog unavailable hanya memeriksa visibility, bukan penolakan POST checkout.

### ORDER-004 — Pembatalan sebelum produksi tidak mengembalikan stok

- **ID:** ORDER-004
- **Judul:** Pembatalan sebelum produksi tidak mengembalikan stok.
- **Severity:** High
- **Status:** Confirmed
- **File:** `app/Services/OrderService.php:135`; `app/Http/Controllers/Admin/OrderController.php:55`; `routes/console.php`.
- **Function/Class:** `OrderService::placeOrder()`, admin `OrderController::updateStatus()`.
- **Execution Path:** Checkout unpaid/pending → stok dikurangi → admin langsung membatalkan sebelum produksi → hanya status berubah.
- **Bukti kode:** Tidak ada pengembalian stok pada `cancelled`, observer, atau scheduled cleanup yang ditemukan di `app/` dan `routes/`.
- **Dampak:** Order yang dibatalkan sebelum produksi tetap menghabiskan stok. Stok database berbeda dari stok yang tersedia dan order terbengkalai dapat menghabiskan kapasitas penjualan.
- **Cara reproduksi:** Checkout quantity 1 dari stok 1, lalu sebagai admin PATCH cancelled saat order masih pending dan belum diproduksi; stok tetap 0.
- **Rekomendasi perbaikan minimal:** Tambahkan pelepasan stok transactional dan idempotent untuk pembatalan yang memenuhi kebijakan bisnis.
- **Test yang disarankan:** Stok dikembalikan tepat satu kali; pembatalan ulang tidak menambah stok lagi. Bedakan pembatalan sebelum produksi dari kasus yang stoknya memang tidak dapat dipulihkan.
- **Hasil verifikasi terakhir:** High dipertahankan hanya untuk pembatalan sebelum produksi. Tidak semua cancellation harus mengembalikan stok: kue yang sudah diproduksi/rusak memerlukan kebijakan berbeda. Tombol batal tersedia pada pending; tidak ditemukan observer/service/scheduler restock. OrderManagementTest hanya menguji transisi processing.

### CUSTOM-001 — Eligibility dan kombinasi customization tidak divalidasi

- **ID:** CUSTOM-001
- **Judul:** Eligibility dan kombinasi customization tidak divalidasi.
- **Severity:** High
- **Status:** Confirmed
- **File:** `app/Services/OrderService.php:28`; `app/Http/Controllers/ProductController.php:62`; `resources/views/products/show.blade.php:54`.
- **Function/Class:** `OrderService::placeOrder()`, `ProductController::show()`.
- **Execution Path:** JSON customization → lookup global berdasarkan ID → penjumlahan harga → penyimpanan pilihan.
- **Bukti kode:** Lookup hanya `whereIn('id', $optionIds)`, tanpa validasi kategori, active, keunikan per item, batas jumlah, atau eksklusivitas tipe. UI memakai radio untuk rasa/ukuran/lainnya. `unique()` hanya diterapkan pada query lookup, bukan daftar yang dijumlahkan/disimpan. ID tidak ditemukan diabaikan dengan harga nol.
- **Dampak:** Opsi kategori lain/inactive, dua ukuran sekaligus, dan duplikasi opsi diterima. Konfigurasi dapat tidak bisa diproduksi atau biaya tidak sesuai pilihan yang seharusnya tersedia. Nominal opsi tetap berasal dari database; manipulasi harga frontend langsung tidak terbukti.
- **Cara reproduksi:** Kirim checkout dengan ID opsi kategori lain, inactive, duplikat, nonexistent, atau dua opsi bertipe ukuran.
- **Rekomendasi perbaikan minimal:** Validasi array ID, kategori/active, keunikan, dan cardinality per tipe sebelum menghitung harga.
- **Test yang disarankan:** Dataset terpisah untuk seluruh kondisi tersebut, termasuk jumlah opsi berlebihan dan perhitungan harga opsi valid.
- **Hasil verifikasi terakhir:** FK hanya membuktikan ID ada, bukan kategori/active/cardinality. ID nonexistent diabaikan, bukan disimpan melanggar FK. Tidak ada observer/model validation pelengkap; test checkout customization tidak ditemukan. Manipulasi nominal frontend langsung tidak terbukti.

### PAY-001 — COD langsung ditandai lunas

- **ID:** PAY-001
- **Judul:** COD langsung ditandai lunas.
- **Severity:** High
- **Status:** Confirmed
- **File:** `app/Services/OrderService.php:138`.
- **Function/Class:** `OrderService::placeOrder()`.
- **Execution Path:** Customer memilih COD → Payment paid → Order paid dan paid_amount penuh.
- **Bukti kode:** Payment.status dan paid_at ditetapkan paid/now saat checkout. Order.payment_status menjadi paid dan paid_amount menjadi total_price tanpa event penerimaan uang. Kombinasi `cod + use_dp` tidak dilarang; Payment.amount dapat hanya sebesar DP.
- **Dampak:** Revenue diakui sebelum uang diterima. Pada total Rp300.000 dan DP 50%, Payment.amount Rp150.000 dapat berbeda dari Order.paid_amount Rp300.000.
- **Cara reproduksi:** Checkout COD biasa, kemudian checkout COD dengan `use_dp=1` di atas threshold.
- **Rekomendasi perbaikan minimal:** Pisahkan persetujuan pemrosesan COD dari penerimaan uang dan tetapkan aturan kombinasi COD/DP.
- **Test yang disarankan:** COD belum dianggap menerima uang sebelum konfirmasi penerimaan; nominal dan status harus konsisten, termasuk kombinasi DP.
- **Hasil verifikasi terakhir:** Payment paid dilabeli Lunas oleh model dan masuk revenue di AdminController::finance() baris 221–227; bukan sekadar izin memproses COD. Tidak ada event penerimaan tunai sebagai syarat atau test COD/DP. Contoh nominal memakai threshold Rp200.000 dan DP 50%.

### PAY-002 — Pelunasan DP tidak memiliki jalur yang dapat bekerja

- **ID:** PAY-002
- **Judul:** Pelunasan DP tidak memiliki jalur yang dapat bekerja.
- **Severity:** High
- **Status:** Confirmed
- **File:** `app/Http/Controllers/Admin/OrderController.php:85`; `app/Http/Controllers/OrderController.php:127`; `resources/views/orders/payment.blade.php:9`.
- **Function/Class:** `confirmPayment()`, `uploadProof()`, view pembayaran.
- **Execution Path:** Checkout DP → upload → admin confirm → customer mencoba pelunasan.
- **Bukti kode:** Confirm mengubah Payment.status menjadi paid. Upload menolak seluruh payment paid. View menghitung sisa pelunasan, tetapi tidak ditemukan endpoint pembentukan tagihan sisa atau akumulasi pembayaran.
- **Dampak:** Setelah DP Rp150.000 dari total Rp300.000 dikonfirmasi, bukti pembayaran kedua ditolak; order tidak dapat dilunasi melalui flow aplikasi.
- **Cara reproduksi:** Jalankan checkout DP, upload, konfirmasi admin, lalu upload bukti pelunasan.
- **Rekomendasi perbaikan minimal:** Tambahkan tagihan/pembayaran pelunasan dengan nominal sisa dan akumulasi pembayaran idempotent.
- **Test yang disarankan:** Dua pembayaran terkonfirmasi menghasilkan paid_amount sama dengan total dan status lunas; konfirmasi ulang tidak menggandakan pembayaran.
- **Hasil verifikasi terakhir:** Order.payment adalah hasOne; confirm ulang tetap memakai nominal DP, bukan sisa pembayaran. Test paid_payment_blocks_reupload hanya membuktikan guard sequential dan tidak membedakan DP dari pelunasan penuh. Tidak ditemukan endpoint tagihan sisa/akumulasi paid_amount.

### PAY-003 — Transisi admin menghasilkan state pembayaran bertentangan

- **ID:** PAY-003
- **Judul:** Transisi admin menghasilkan state pembayaran bertentangan.
- **Severity:** High
- **Status:** Confirmed
- **File:** `app/Http/Controllers/Admin/OrderController.php:55`; `app/Http/Controllers/ProductReviewController.php:37`.
- **Function/Class:** `updateStatus()`, `confirmPayment()`, `rejectPayment()`.
- **Execution Path:** Admin menyelesaikan order unpaid atau mengonfirmasi/menolak ulang order terminal.
- **Bukti kode:** Completed hanya mengubah Payment menjadi paid tanpa memperbarui Order.payment_status/paid_amount. Confirm selalu mengubah order ke processing. Reject selalu mengubahnya ke pending, paid_amount nol, tetapi tidak membersihkan paid_at. Tidak ada guard transisi status sebelumnya.
- **Dampak:** Order dapat completed + Payment paid + Order unpaid/paid_amount nol. Confirm ulang dapat mengembalikan completed ke processing. Review juga dapat lolos melalui kondisi completed + Payment paid yang tidak membuktikan pelunasan.
- **Cara reproduksi:** Selesaikan order unpaid; confirm ulang order completed; reject payment yang sudah dikonfirmasi. Bandingkan seluruh field state.
- **Rekomendasi perbaikan minimal:** Terapkan guard transisi dan pembaruan field konsisten; penyelesaian order tidak otomatis menjadi bukti penerimaan uang.
- **Test yang disarankan:** Matriks transisi legal/ilegal dengan assertion status Order/Payment, paid_amount, paid_at, dan eligibility review.
- **Hasil verifikasi terakhir:** Enum membatasi nilai individual, bukan kombinasi state. Tidak ada observer sinkronisasi. Tombol Selesai tersedia untuk unpaid. Tombol confirm/reject disembunyikan setelah status bukan unpaid, tetapi endpoint tetap menerima request ulang admin. Ini bukan admin bypass oleh customer; test konfirmasi belum mengassert invariant nominal/status lengkap.

### PAY-004 — Mutasi pembayaran tidak atomic; race valid dengan state awal yang dikoreksi

- **ID:** PAY-004
- **Judul:** Mutasi pembayaran tidak atomic; race valid dengan state awal yang dikoreksi.
- **Severity:** High
- **Status:** Confirmed
- **File:** `app/Http/Controllers/Admin/OrderController.php:85`; `app/Http/Controllers/OrderController.php:123`; `vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php:1313`.
- **Function/Class:** `confirmPayment()`, `rejectPayment()`, `updateStatus()`, `uploadProof()`, Eloquent `performUpdate()`.
- **Execution Path:** Partial failure: confirm memperbarui Payment menjadi paid → update Order gagal → Payment tetap paid dan Order masih unpaid. Race: upload membaca Payment failed → admin confirm → upload melanjutkan perubahan failed menjadi unpaid.
- **Bukti kode:** Update Payment/Order terpisah tanpa transaction; tidak ditemukan middleware transaction atau observer kompensasi. Guard upload terjadi sebelum operasi file tanpa lock/recheck saat menyimpan. Confirm tidak menolak payment failed. Eloquent hanya menulis field dirty: getDirty model Payment asli menghasilkan hanya proof_image untuk unpaid → unpaid, tetapi status=unpaid dan proof_image untuk failed → unpaid.
- **Dampak:** Partial failure meninggalkan Payment paid dan Order unpaid. Race dari failed dapat meninggalkan Order paid tetapi Payment unpaid. Bukti juga dapat berubah setelah guard. Skenario awal unpaid → confirm → upload bukan bukti penimpaan status; subklaim tersebut dipindah ke False Positives.
- **Cara reproduksi:** Pada database disposable, gagalkan update Order setelah Payment disimpan oleh confirm. Untuk race, mulai dari Payment failed, tahan upload setelah membaca Payment, confirm dari request admin, lalu lanjutkan upload menggunakan dua koneksi/request dan barrier.
- **Rekomendasi perbaikan minimal:** Transaction untuk perubahan Order/Payment, guard transisi, locking/conditional update, dan pemeriksaan ulang state saat commit.
- **Test yang disarankan:** Fault injection pada event updating Order setelah Payment disimpan; setelah perbaikan tidak boleh ada partial commit. Uji race dari failed dengan dua koneksi MySQL. Pertahankan test pembantahan unpaid → unpaid agar tidak mengasumsikan semua field update selalu ditulis.
- **Hasil verifikasi terakhir:** High tetap valid untuk partial failure dan interleaving yang dikoreksi. Ownership/admin middleware benar, tetapi tidak menyerialisasi request customer/admin. getDirty diuji tanpa database; integration/concurrency belum dijalankan. Percobaan SQL dry-run tidak selesai karena koneksi diblokir dan bukan bukti.

## Confirmed Medium Findings

### ORDER-003 — Checkout tidak memiliki perlindungan replay/idempotency

- **ID:** ORDER-003
- **Judul:** Checkout tidak memiliki perlindungan replay/idempotency.
- **Severity:** Medium
- **Status:** Confirmed
- **File:** `app/Http/Controllers/OrderController.php:79`; `app/Services/OrderService.php:93`; `routes/web.php:82`.
- **Function/Class:** `OrderController::store()`, `OrderService::placeOrder()`.
- **Execution Path:** POST berhasil → response hilang/retry → POST identik → order baru.
- **Bukti kode:** Setiap request membuat `order_code` acak. Tidak ada idempotency key atau constraint identitas checkout. Session cart dihapus setelah checkout, tetapi POST tetap memproses payload `items` langsung. Throttle 20/menit bukan deduplikasi.
- **Dampak:** Double click atau retry dapat membuat dua order, dua alokasi stok, dan dua penggunaan voucher jika stok/kuota cukup.
- **Cara reproduksi:** Kirim payload checkout sama dua kali dengan stok mencukupi, termasuk setelah cart dikosongkan oleh checkout pertama.
- **Rekomendasi perbaikan minimal:** Simpan idempotency key berconstraint unik yang terikat customer dan payload checkout.
- **Test yang disarankan:** Pembuktian kondisi sekarang: replay payload valid dengan stok cukup menghasilkan dua Order/Payment dan pengurangan stok dua kali. Setelah identitas intent ditetapkan, regression test harus menghasilkan satu transaksi untuk key sama, termasuk concurrent, tetapi mengizinkan pembelian identik dengan intent berbeda.
- **Hasil verifikasi terakhir:** Downgrade High → Medium. Dampak yang terbukti adalah order/alokasi stok/voucher ganda, bukan debit uang otomatis dua kali. Pembelian identik bisa disengaja; perbaikan memerlukan identitas intent. Unique order_code, throttle, dan penghapusan cart bukan idempotency. Test unique code tidak membuktikan deduplikasi.

### TEST-002 — Helper Bun dapat menghapus database konfigurasi aktif

- **ID:** TEST-002
- **Judul:** Helper Bun dapat menghapus database konfigurasi aktif.
- **Severity:** Medium
- **Status:** Confirmed
- **File:** `tests/helpers.ts:47`; `package.json`; `phpunit.xml`; `vendor/laravel/framework/src/Illuminate/Database/Console/Migrations/FreshCommand.php:58`; `vendor/laravel/framework/src/Illuminate/Console/ConfirmableTrait.php:50`.
- **Function/Class:** `resetDatabase()`.
- **Execution Path:** `npm test` → beforeEach → `php artisan migrate:fresh --seed`.
- **Bukti kode:** Helper menjalankan migrate:fresh --seed tanpa target database/environment test dan melanjutkan setelah error. phpunit.xml tidak mengatur proses Bun/Artisan ini. FreshCommand memanggil confirmToProceed sebelum wipe; callback bawaan meminta konfirmasi pada environment production. Helper tidak memberi --force. AGENTS.md mendokumentasikan database disposable.
- **Dampak:** Database non-disposable pada environment non-production dapat terhapus saat developer menjalankan test. Reset gagal meninggalkan sisa state. Ini bukan kerentanan remote atau bukti bahwa production berkonfigurasi benar otomatis terhapus.
- **Cara reproduksi:** Mock child process dan environment untuk memeriksa command serta kelanjutan setelah error. Pembuktian wipe aktual hanya pada database disposable dengan marker, bukan database penting.
- **Rekomendasi perbaikan minimal:** Paksa environment/koneksi test, tambahkan guard target database, dan fail-fast saat reset gagal.
- **Test yang disarankan:** Mock eksekusi child: buktikan command tidak memaksa target test dan helper melanjutkan setelah error. Regression test harus menolak target non-test sebelum operasi destruktif dan menghentikan suite saat reset gagal; verifikasi wipe hanya pada database disposable.
- **Hasil verifikasi terakhir:** Downgrade High → Medium. Risiko operasional terjadi bila test lokal/non-production memakai database non-disposable. AGENTS.md sudah menyatakan kewajiban database disposable dan Laravel memiliki konfirmasi production. Tidak ada bukti kehilangan data aktual atau penghapusan otomatis production berkonfigurasi benar.

### SHIP-001 — Zona pengiriman unavailable masih diterima

- **ID:** SHIP-001
- **Judul:** Zona pengiriman unavailable masih diterima.
- **Severity:** Medium
- **Status:** Confirmed
- **File:** `app/Http/Controllers/OrderController.php:62`; `app/Services/OrderService.php:43`; `resources/views/orders/create.blade.php:20`.
- **Function/Class:** `OrderController::store()`, `OrderService::placeOrder()`.
- **Execution Path:** Delivery checkout → exists shipping zone → find zona → order.
- **Bukti kode:** Backend tidak memeriksa `is_available`; filter availability hanya terdapat pada view checkout.
- **Dampak:** Order diterima untuk wilayah yang tidak lagi dilayani.
- **Cara reproduksi:** Submit delivery dengan alamat valid dan zona `is_available=false`.
- **Rekomendasi perbaikan minimal:** Batasi zona ke yang tersedia dan validasi kembali saat checkout.
- **Test yang disarankan:** Zona unavailable ditolak tanpa perubahan order, stok, voucher, atau payment.

### CART-001 — Varian produk sama menimpa customization sebelumnya

- **ID:** CART-001
- **Judul:** Varian produk sama menimpa customization sebelumnya.
- **Severity:** Medium
- **Status:** Confirmed
- **File:** `app/Http/Controllers/CartController.php:35`.
- **Function/Class:** `CartController::add()`.
- **Execution Path:** Tambah produk varian A → tambah produk sama varian B → checkout.
- **Bukti kode:** Cart di-key hanya oleh product_id. Quantity dijumlahkan, tetapi note dan customizations ditimpa oleh request berikutnya.
- **Dampak:** Satu kue coklat bertuliskan Ani dan satu vanilla bertuliskan Budi dapat berubah menjadi dua kue vanilla bertuliskan Budi; detail produksi dan harga berubah.
- **Cara reproduksi:** Tambahkan produk sama dua kali dengan note/customization berbeda.
- **Rekomendasi perbaikan minimal:** Gunakan identitas cart item yang mencakup konfigurasi atau pisahkan item berbeda.
- **Test yang disarankan:** Kedua konfigurasi tetap terpisah dari cart sampai OrderItem disimpan.

### CART-002 — Beli Sekarang kehilangan pilihan customer

- **ID:** CART-002
- **Judul:** Beli Sekarang kehilangan pilihan customer.
- **Severity:** Medium
- **Status:** Confirmed
- **File:** `resources/views/products/show.blade.php:124`; `app/Http/Controllers/OrderController.php:30`; `resources/views/orders/create.blade.php:9`.
- **Function/Class:** `singleProductCheckout()` dan template checkout.
- **Execution Path:** Pilih quantity/note/customization → klik Beli Sekarang → GET checkout produk.
- **Bukti kode:** Tombol berupa link tanpa payload pilihan. Checkout membentuk item quantity 1, note kosong, dan tanpa customization.
- **Dampak:** Pilihan customer tidak diteruskan dan dapat menimbulkan salah pemesanan.
- **Cara reproduksi:** Pilih quantity 3, topping, dan note, kemudian klik Beli Sekarang; periksa halaman checkout.
- **Rekomendasi perbaikan minimal:** Teruskan pilihan melalui flow tervalidasi yang sama dengan cart.
- **Test yang disarankan:** Browser test membandingkan pilihan di halaman produk dengan item checkout.

### VAL-001 — JSON customization scalar menyebabkan error server

- **ID:** VAL-001
- **Judul:** JSON customization scalar menyebabkan error server.
- **Severity:** Medium
- **Status:** Confirmed
- **File:** `app/Http/Controllers/CartController.php:32`; `app/Services/CartService.php:52`.
- **Function/Class:** `CartController::add()`, `CartService::normalizeCustomizationIds()`.
- **Execution Path:** POST `/cart/add` → string lolos validation → json_decode → parameter array.
- **Bukti kode:** String `1` didecode menjadi integer dan diteruskan ke method dengan parameter `array $rawCustomizations`. Pemanggilan read-only mengonfirmasi TypeError.
- **Dampak:** Input malformed yang dapat dikirim guest menghasilkan error server, bukan validation error.
- **Cara reproduksi:** Kirim `customizations_json=1` bersama product_id valid.
- **Rekomendasi perbaikan minimal:** Validasi JSON dan bentuk array integer sebelum normalisasi.
- **Test yang disarankan:** Scalar, object invalid, nested array, dan JSON malformed menghasilkan validation error yang terkontrol.

### VAL-002 — Slot pengiriman wajib di UI tetapi bebas di backend

- **ID:** VAL-002
- **Judul:** Slot pengiriman wajib di UI tetapi bebas di backend.
- **Severity:** Medium
- **Status:** Confirmed
- **File:** `app/Http/Controllers/OrderController.php:64`; `resources/views/orders/create.blade.php:496`.
- **Function/Class:** `store()`, JavaScript `validateCheckout()`.
- **Execution Path:** POST langsung → `delivery_slot: nullable|string` → penyimpanan order.
- **Bukti kode:** UI menolak slot kosong; backend tidak memiliki requirement, whitelist, atau batas panjang.
- **Dampak:** Order tanpa slot atau dengan slot di luar jam operasional diterima; input terlalu panjang dapat gagal di database.
- **Cara reproduksi:** Hilangkan delivery_slot atau kirim teks sembarang pada checkout valid.
- **Rekomendasi perbaikan minimal:** Terapkan required dan whitelist dari sumber pilihan yang sama dengan UI.
- **Test yang disarankan:** Dataset slot kosong, invalid, terlalu panjang, dan seluruh slot resmi.

### VOUCHER-001 — Voucher invalid dihapus diam-diam dari perhitungan checkout

- **ID:** VOUCHER-001
- **Judul:** Voucher invalid dihapus diam-diam dari perhitungan checkout.
- **Severity:** Medium
- **Status:** Confirmed
- **File:** `app/Services/OrderService.php:71`.
- **Function/Class:** `OrderService::placeOrder()`.
- **Execution Path:** Preview voucher berhasil → voucher habis/expired → submit → order tanpa diskon.
- **Bukti kode:** Jika voucher tidak ditemukan atau isValid false, eksekusi melanjutkan checkout dengan diskon nol tanpa validation error.
- **Dampak:** Harga order lebih tinggi daripada preview yang disetujui customer; dapat terjadi selisih transfer atau sengketa nominal.
- **Cara reproduksi:** Setelah preview berhasil, habiskan kuota atau nonaktifkan voucher, lalu submit checkout.
- **Rekomendasi perbaikan minimal:** Kembalikan validation error dan total terbaru saat voucher eksplisit yang diajukan tidak lagi valid.
- **Test yang disarankan:** Order tidak dibuat ketika voucher yang diminta invalid; uji perubahan eligibility antara preview dan submit.

### PAY-005 — payment_method tidak memakai whitelist

- **ID:** PAY-005
- **Judul:** payment_method tidak memakai whitelist.
- **Severity:** Medium
- **Status:** Confirmed
- **File:** `app/Http/Controllers/OrderController.php:73`; `app/Services/OrderService.php:138`; `database/migrations/2026_04_11_082233_create_payments_table.php:14`.
- **Function/Class:** `store()`, `placeOrder()`.
- **Execution Path:** Request string bebas → Payment.payment_method.
- **Bukti kode:** Validation hanya nullable|string; kolom database string bebas. Nilai UI adalah transfer_bank, ewallet, qris, dan cod.
- **Dampak:** Metode seperti abc diterima meskipun tidak didukung operasional/UI.
- **Cara reproduksi:** Checkout valid dengan `payment_method=abc`.
- **Rekomendasi perbaikan minimal:** Tetapkan whitelist aplikasi dan batas panjang input.
- **Test yang disarankan:** Tolak metode di luar daftar. Sesuaikan fixture checkout yang saat audit memakai transfer, berbeda dari transfer_bank pada UI.

### REVIEW-001 — Moderasi tidak konsisten dan approval bertahan setelah edit

- **ID:** REVIEW-001
- **Judul:** Moderasi tidak konsisten dan approval bertahan setelah edit.
- **Severity:** Medium
- **Status:** Confirmed
- **File:** `app/Http/Controllers/ProductController.php:58`; `app/Http/Controllers/ProductReviewController.php:96`; `app/Http/Controllers/HomeController.php:17`; `resources/views/products/show.blade.php:144`.
- **Function/Class:** `ProductController::show()`, `ProductReviewController::update()`, `HomeController::index()`.
- **Execution Path:** Review pending/rejected → detail produk; review approved → edit customer → homepage.
- **Bukti kode:** Detail produk mengambil seluruh review; homepage memfilter is_approved. Update customer tidak mereset approval. Admin menyediakan aksi Tolak/Setujui.
- **Dampak:** Review yang ditolak tetap tampil pada detail produk; komentar baru dapat dipublikasikan sebagai testimonial menggunakan approval konten sebelumnya.
- **Cara reproduksi:** Approve review, edit komentarnya, periksa homepage; reject review dan periksa detail produk.
- **Rekomendasi perbaikan minimal:** Konsistenkan kebijakan publikasi dan invalidasi approval ketika konten berubah.
- **Test yang disarankan:** Uji visibility pending/rejected dan reset approval setelah perubahan komentar/rating/konten terkait.

### PERF-001 — Production calendar menjalankan query user per order

- **ID:** PERF-001
- **Judul:** Production calendar menjalankan query user per order.
- **Severity:** Medium
- **Status:** Confirmed
- **File:** `app/Http/Controllers/AdminController.php:253`; `resources/views/admin/production-calendar.blade.php:84`.
- **Function/Class:** `AdminController::productionCalendar()` dan template kalender.
- **Execution Path:** Ambil order sebulan → loop view → akses relasi user.
- **Bukti kode:** Eager loading hanya orderItems.product, tetapi view mengakses `$order->user` untuk setiap order.
- **Dampak:** Jumlah lookup user tumbuh mengikuti order; kalender bulan sibuk menambah beban database dan latensi. Besaran dampak production belum diukur.
- **Cara reproduksi:** Render kalender dengan banyak order sambil menghitung SELECT user.
- **Rekomendasi perbaikan minimal:** Eager-load user dan pilih kolom yang diperlukan.
- **Test yang disarankan:** Query count tetap hampir konstan ketika jumlah order bertambah.

### ANALYTICS-001 — Terjual menghitung baris dan memasukkan order invalid

- **ID:** ANALYTICS-001
- **Judul:** Terjual menghitung baris dan memasukkan order invalid.
- **Severity:** Medium
- **Status:** Confirmed
- **File:** `app/Http/Controllers/AdminController.php:71`; `app/Http/Controllers/AdminController.php:170`; `resources/views/admin/dashboard.blade.php:219`.
- **Function/Class:** `dashboard()`, `analytics()`.
- **Execution Path:** withCount orderItems → ranking → label terjual.
- **Bukti kode:** Top product menghitung baris tanpa filter order. Total items memakai sum quantity, tetapi juga tanpa filter cancelled/payment.
- **Dampak:** Produk 100 unit dalam satu baris dapat kalah dari produk dua baris quantity 1. Order cancelled menambah metrik penjualan.
- **Cara reproduksi:** Buat fixture dua produk tersebut dan tambahkan cancelled order, kemudian bandingkan ranking dan total unit.
- **Rekomendasi perbaikan minimal:** Gunakan sum quantity dan scope transaksi valid yang konsisten dengan definisi penjualan.
- **Test yang disarankan:** Assert ranking dan total unit berdasarkan quantity, termasuk pengecualian status yang tidak dianggap penjualan.

### ANALYTICS-002 — Revenue, grafik, dan AOV menggunakan cohort berbeda

- **ID:** ANALYTICS-002
- **Judul:** Revenue, grafik, dan AOV menggunakan cohort berbeda.
- **Severity:** Medium
- **Status:** Confirmed
- **File:** `app/Http/Controllers/AdminController.php:158`; `app/Http/Controllers/AdminController.php:274`; `resources/views/admin/analytics.blade.php:145`.
- **Function/Class:** `analytics()`, `dashboard()`, `finance()`, `buildDailyRevenue()`.
- **Execution Path:** Payment paid aggregates → kartu revenue/AOV; seluruh Order totals → grafik Pendapatan.
- **Bukti kode:** Revenue memakai Payment.amount dan created_at, bukan paid_at. Grafik menjumlahkan seluruh Order.total_price. AOV membagi revenue paid dengan seluruh order, termasuk unpaid/cancelled.
- **Dampak:** Order Januari yang dibayar Februari dialokasikan ke Januari. Order unpaid menaikkan grafik tanpa menaikkan kartu revenue. AOV tidak mewakili cohort transaksi yang sama.
- **Cara reproduksi:** Siapkan paid_at lintas bulan, unpaid, cancelled, serta DP; bandingkan kartu, grafik, dan AOV.
- **Rekomendasi perbaikan minimal:** Pisahkan nilai pesanan dari uang diterima dan gunakan timestamp/cohort yang sesuai untuk setiap metrik.
- **Test yang disarankan:** Assert cash received, nilai order, dan periode masing-masing berdasarkan fixture deterministik.

### ANALYTICS-003 — Bulan sebelumnya salah pada akhir bulan

- **ID:** ANALYTICS-003
- **Judul:** Bulan sebelumnya salah pada akhir bulan.
- **Severity:** Medium
- **Status:** Confirmed
- **File:** `app/Http/Controllers/AdminController.php:263`.
- **Function/Class:** `monthBoundaries()`.
- **Execution Path:** now → subMonth → start/end month → statistik pembanding.
- **Bukti kode:** Bulan dikurangi sebelum tanggal dinormalkan. Verifikasi dengan Carbon terpasang pada 31 Maret 2026 menghasilkan previous start 2026-03-01 dan previous end 2026-03-31.
- **Dampak:** Dashboard dapat membandingkan bulan berjalan dengan bulan yang sama; growth revenue/order/customer salah.
- **Cara reproduksi:** Jalankan perhitungan batas bulan dengan waktu 31 Maret 2026.
- **Rekomendasi perbaikan minimal:** Normalisasi startOfMonth sebelum pengurangan atau gunakan pengurangan bulan tanpa overflow.
- **Test yang disarankan:** Freeze waktu pada akhir bulan panjang; assert rentang sebelumnya 1–28 Februari 2026 dan uji tahun kabisat.

### REPORT-001 — Export, kalender, dan invoice membaca atribut yang salah

- **ID:** REPORT-001
- **Judul:** Export, kalender, dan invoice membaca atribut yang salah.
- **Severity:** Medium
- **Status:** Confirmed
- **File:** `app/Exports/LaporanPenjualanExport.php:29`; `resources/views/admin/production-calendar.blade.php:85`; `resources/views/pdf/invoice.blade.php:364`.
- **Function/Class:** `LaporanPenjualanExport::collection()` dan template kalender/invoice.
- **Execution Path:** Model transaksi → export/render → fallback karena atribut tidak tersedia.
- **Bukti kode:** Export membaca order_number, bukan order_code, dan Order.payment_method, bukan relasi Payment. Kalender membaca total_amount, bukan total_price. Invoice membaca OrderItem.notes, bukan note. Tidak ada accessor yang menjembatani nama tersebut.
- **Dampak:** Export menampilkan ID internal dan metode '-', kalender menampilkan Rp0, dan note per item tidak muncul pada invoice.
- **Cara reproduksi:** Export/render order dengan order_code, total positif, payment, dan item note.
- **Rekomendasi perbaikan minimal:** Gunakan atribut yang benar dan eager-load payment pada export.
- **Test yang disarankan:** Assert nilai sel export serta isi kalender/template invoice sesuai fixture.

### TEST-001 — Sejumlah test tidak membuktikan behavior yang dinyatakan

- **ID:** TEST-001
- **Judul:** Sejumlah test tidak membuktikan behavior yang dinyatakan.
- **Severity:** Medium
- **Status:** Confirmed
- **File:** `tests/order.test.ts:8`; `tests/Unit/Helpers/PriceCalculatorTest.php:5`; `tests/Feature/Security/AuthorizationTest.php:44`; `tests/Feature/Order/CheckoutTest.php:88`.
- **Function/Class:** Test checkout Bun, arithmetic tests, test CSRF, dan test unique checkout code.
- **Execution Path:** Request/fixture tidak representatif → assertion status atau aritmetika lokal → klaim behavior aplikasi.
- **Bukti kode:** Test Bun checkout dan stok kurang tidak mengirim delivery_method/date wajib. Helper memakai Accept JSON, tetapi test mengharapkan 302. Tiga test harga awal hanya menghitung angka lokal. Test CSRF mengassert login error ketika CSRF dilewati environment testing. Test unique checkout code tidak mengassert bahwa dua order benar-benar dibuat.
- **Dampak:** Test dapat gagal karena alasan lain atau tetap lolos ketika behavior target rusak. Arithmetic test tetap hijau meskipun perhitungan checkout salah.
- **Cara reproduksi:** Bandingkan payload dengan validator; lakukan mutation testing hanya pada lingkungan disposable terpisah.
- **Rekomendasi perbaikan minimal:** Perkuat assertion terhadap execution path aplikasi, bukan hanya status response atau perhitungan ulang di test.
- **Test yang disarankan:** Assert record count, error field spesifik, nominal, stok, relasi, serta rollback. Untuk CSRF, gunakan pengujian yang benar-benar mengaktifkan pemeriksaannya.

## Confirmed Low Findings

### ERROR-001 — Kegagalan enqueue email/notifikasi ditelan tanpa logging

- **ID:** ERROR-001
- **Judul:** Kegagalan enqueue email/notifikasi ditelan tanpa logging.
- **Severity:** Low
- **Status:** Confirmed
- **File:** `app/Http/Controllers/OrderController.php:90`; `app/Http/Controllers/Admin/OrderController.php:66`.
- **Function/Class:** `OrderController::store()`, admin `OrderController::updateStatus()`.
- **Execution Path:** Commit order → queue email → notify admin → exception → catch kosong.
- **Bukti kode:** `catch (\Throwable) {}` tidak mencatat exception. Email dan notifikasi seluruh admin berada dalam satu blok pada pembuatan order.
- **Dampak:** Order tetap berhasil, tetapi kegagalan dispatch tidak terlihat; kegagalan email dapat melewati notifikasi admin berikutnya dan menyebabkan keterlambatan operasional.
- **Cara reproduksi:** Simulasikan queue dispatch melempar exception.
- **Rekomendasi perbaikan minimal:** Tetap pertahankan order yang sudah committed, log konteks kegagalan, dan pisahkan dispatch yang perlu berjalan independen.
- **Test yang disarankan:** Order tetap committed, kegagalan dicatat, dan kebijakan retry/notifikasi dapat diamati.

## Needs Further Verification

Dua temuan berikut sebelumnya Risiko nyata dan belum mendapat pembuktian tambahan. Severity Medium adalah estimasi dampak, tidak dihitung sebagai Confirmed Medium.

### UPLOAD-001 — Bukti lama dihapus sebelum penggantinya aman

- **ID:** UPLOAD-001
- **Judul:** Bukti lama dihapus sebelum penggantinya aman.
- **Severity:** Medium
- **Status:** Needs Further Verification
- **File:** `app/Http/Controllers/OrderController.php:136`; `config/filesystems.php:41`.
- **Function/Class:** `OrderController::uploadProof()`.
- **Execution Path:** Hapus bukti lama → store file baru → update database.
- **Bukti kode:** Delete dilakukan lebih dahulu; public disk menggunakan throw=false dan hasil kegagalan store tidak diperiksa eksplisit. Tidak ada mekanisme kompensasi filesystem/database.
- **Dampak:** Disk penuh atau kegagalan update Payment dapat menghilangkan bukti sebelumnya, menyisakan path tidak valid, atau menciptakan file orphan.
- **Cara reproduksi:** Simulasikan kegagalan write storage atau update Payment saat mengganti bukti.
- **Rekomendasi perbaikan minimal:** Simpan dan validasi file baru, persist referensi, lalu hapus file lama setelah berhasil.
- **Test yang disarankan:** Bukti lama tetap tersedia ketika replacement gagal; file baru yang tidak terpakai dibersihkan dengan aman.
- **Hasil verifikasi terakhir:** Status awal Risiko nyata; tidak termasuk 12 temuan Critical/High yang diverifikasi ulang. Urutan operasi terlihat dari kode, tetapi dampak partial failure belum diuji melalui fault injection storage/database. Dipertahankan sebagai Needs Further Verification, bukan dianggap sudah direproduksi atau dibantah.

### SEC-001 — Bukti pembayaran tersedia melalui URL tanpa authorization

- **ID:** SEC-001
- **Judul:** Bukti pembayaran tersedia melalui URL tanpa authorization.
- **Severity:** Medium
- **Status:** Needs Further Verification
- **File:** `app/Http/Controllers/OrderController.php:140`; `config/filesystems.php:41`; `docker/nginx/default.conf:1`; `resources/views/orders/payment.blade.php:293`.
- **Function/Class:** `uploadProof()` dan static-file serving.
- **Execution Path:** Upload → public disk → `/storage/payment_proofs/...` → web server.
- **Bukti kode:** File disimpan pada public disk dan dirujuk melalui asset storage. Symlink public/storage tersedia saat audit. Nginx melayani file statis sebelum Laravel.
- **Dampak:** URL bukti yang tersebar dapat dibuka tanpa session. Filename acak mengurangi penebakan, tetapi tidak menegakkan ownership.
- **Cara reproduksi:** Pada deployment sesuai konfigurasi repository, salin URL bukti milik sendiri dan buka tanpa login.
- **Rekomendasi perbaikan minimal:** Simpan bukti secara privat dan sajikan melalui endpoint berotorisasi.
- **Test yang disarankan:** Guest/customer lain ditolak ketika mengambil file, termasuk akses langsung melalui web server, bukan hanya endpoint Laravel.
- **Hasil verifikasi terakhir:** Status awal Risiko nyata; tidak termasuk verifikasi ulang Critical/High. Public disk/symlink/static serving menunjukkan risiko, tetapi akses guest pada deployment aktual belum diuji. Perlu GET tanpa session pada URL bukti uji milik sendiri; jangan gunakan bukti customer lain.

## False Positives

### PAY-004 — Subklaim penimpaan status dari snapshot awal unpaid

- **ID temuan awal:** PAY-004, subklaim; temuan induk tetap Confirmed High.
- **Status:** False Positive
- **Dugaan awal:** Upload membaca unpaid → admin confirm menjadi paid → upload menyimpan unpaid dan membatalkan konfirmasi.
- **Alasan tidak valid:** Assignment unpaid pada model dengan original unpaid tidak menjadikan status dirty. Eloquent tidak selalu menulis seluruh field yang diberikan pada update.
- **Bagian kode yang membantah:** `vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php:1313` memakai getDirtyForUpdate dan mengirim hanya atribut dirty. Payment tidak mengubah perilaku ini. Assignment yang diaudit berada di `app/Http/Controllers/OrderController.php:152`.
- **Bukti verifikasi:** Model Payment dengan raw original unpaid, kemudian fill proof baru dan status unpaid, menghasilkan getDirty hanya proof_image. Original failed menghasilkan status=unpaid dan proof_image. Pemeriksaan ini tidak memakai database.
- **Dampak pada laporan:** Klaim penimpaan status dari snapshot unpaid ditarik. Partial failure, perubahan proof, dan race dari failed tidak dibantah; lihat temuan induk PAY-004.
- **Test pembantahan:** Hydrate Payment unpaid dan sync original, fill proof baru/status unpaid; assert status tidak terdapat pada getDirty. Integration test harus memastikan upload snapshot unpaid tidak menurunkan paid ke unpaid. Uji failed secara terpisah.
- **Histori:** Dugaan berasal dari audit awal dan dibantah pada verifikasi ulang Critical/High. Entri dipertahankan untuk ketertelusuran koreksi, tanpa menambah temuan induk baru.

## Testing Gaps

Matriks ini berdasarkan inspeksi suite, bukan hasil eksekusi test.

| Flow | Kondisi test saat audit |
| --- | --- |
| Checkout sukses | Ada; assertion utama hanya keberadaan order |
| Stok kurang / stock zero | Bun tidak mengisolasi validasi stok; unit memeriksa nilai model |
| Produk unavailable pada POST | Tidak ditemukan |
| Concurrent purchase stok terakhir | Tidak ditemukan |
| Product ID berulang | Tidak ditemukan |
| Customization invalid / beda kategori / inactive / duplikat | Tidak ditemukan |
| Harga dan total frontend dimanipulasi | Tidak ditemukan |
| Payment method invalid | Tidak ditemukan |
| Shipping zone invalid / unavailable | Tidak ditemukan |
| Voucher expired / limit / inactive / minimum | Tidak ditemukan test eligibility checkout |
| Diskon fixed / percent | Ada test nominal dasar |
| Voucher concurrency | Tidak ditemukan |
| Rollback seluruh checkout | Tidak ditemukan |
| Duplicate checkout / retry | Tidak ditemukan |
| DP dan pelunasan | Tidak ditemukan |
| COD dan kombinasi COD + DP | Tidak ditemukan |
| Full payment | Ada konfirmasi dasar; paid_amount/payment_status belum diassert lengkap |
| Payment rejection / transisi terminal | Tidak ditemukan |
| Upload valid / PHP rejection / ownership | Ada |
| Reupload setelah paid | Ada sequential; tidak membedakan DP/lunas dan tidak ada race test |
| Invoice ownership | Tidak ditemukan |
| Order ownership / admin boundary | Ada sebagian |
| Review ownership / eligibility / duplicate / moderation | Tidak ditemukan |
| Cascade histori | Test delete user/produk ada tanpa transaksi historis; test retensi belum ada |
| Akurasi analytics / export | Bun hanya memeriksa halaman dapat dibuka |

Seluruh `PaymentProofUploadTest` diberi requirement ekstensi GD. Akibatnya, test otorisasi dan konfirmasi di kelas itu juga terlewati bila GD tidak tersedia.

Prioritas pengujian adalah concurrency dengan beberapa koneksi MySQL/InnoDB, fault injection untuk rollback, state matrix pembayaran, dan assertion lintas Order/OrderItem/stock/voucher/Payment. Setiap perbaikan perlu regression test pada batch yang sama, tidak menunggu seluruh perbaikan selesai.

Pembaruan dari verifikasi terakhir:

- PAY-004: pisahkan snapshot unpaid (status tidak dirty) dan failed (status dirty). Tambahkan fault injection setelah update Payment sebelum Order, serta barrier dua koneksi untuk race yang benar.
- ORDER-004: test restock khusus pending sebelum produksi dan cancel berulang; jangan menyamakan seluruh cancellation.
- ORDER-003: buktikan replay menghasilkan dua order saat ini. Regression test harus membedakan key/intent sama dan pembelian identik dengan intent berbeda.
- TEST-002: phpunit.xml tidak mengisolasi Bun/Artisan. Mock child process/environment dan pastikan guard target disposable serta fail-fast sebelum suite dijalankan.
- PAY-001/PAY-002/PAY-003: assert paid_amount, payment_status, Payment.amount/status/paid_at, serta Order.status bersama-sama; tetapkan konfigurasi DP eksplisit.
- UPLOAD-001 membutuhkan fault injection storage/database; SEC-001 membutuhkan GET static URL tanpa session pada deployment uji. Keduanya belum Confirmed.

## Architecture Observations

Observasi ini dipisahkan dari bug Confirmed. Struktur class/service atau usulan constraint tidak otomatis menjadi bug tanpa execution path yang membuktikannya.

### Improvement yang tidak dihitung sebagai bug tambahan

- `payments.order_id` belum unique, sementara relasi menggunakan hasOne. Checkout normal membuat satu payment; constraint dapat memperkuat invariant dan fallback upload.
- Quantity, jumlah item, dan panjang string belum dibatasi merata. Batas perlu mengikuti kapasitas bisnis, bukan angka arbitrer.
- Decimal `(10,2)` membatasi nilai maksimum transaksi; belum ada bukti kebutuhan bisnis yang melampauinya.
- Loading seluruh review pada detail produk, seluruh koleksi export, dan seluruh order customer untuk statistik perlu profiling sebelum diklaim sebagai bottleneck production.
- Nama produk dan opsi historis masih bergantung pada katalog hidup; snapshot dapat memperkuat histori transaksi.

### Preferensi arsitektur

Memusatkan transisi pembayaran atau memisahkan payment attempts dapat membantu, tetapi bentuk class/service tertentu bukan syarat kebenaran. Prioritas adalah invariant transaksi, transisi legal, dan pemulihan kegagalan yang konsisten.

### Aturan bisnis yang belum dapat disimpulkan

- Voucher sekali per user tidak didefinisikan. Penggunaan berulang oleh user sama tidak dinyatakan sebagai bug.
- Pengembalian kuota voucher setelah cancellation memerlukan kebijakan eksplisit. used_count saat ini menghitung checkout berhasil, bukan pembayaran.
- Tidak ada atribut expiry/batch produk untuk menentukan kelayakan jual selain availability dan stok.
- Tidak ada pemetaan wilayah terstruktur yang cukup untuk membuktikan kesesuaian alamat bebas dengan zona pilihan.

Poin tersebut adalah batas interpretasi/observasi, bukan bug baru atau tambahan entri Needs Further Verification. Kebijakan restock setelah produksi belum disimpulkan; ORDER-004 hanya mengonfirmasi pembatalan sebelum produksi.

## Bagian Repository yang Sudah Baik

- **Atomic checkout:** Order, OrderItem, snapshot harga customization, pengurangan stok, increment voucher, dan Payment dibuat dalam satu DB::transaction. Exception di dalam closure membatalkan mutasi tersebut pada engine transactional.
- **Lock stok:** lockForUpdate berada di dalam transaction. Untuk produk tidak berulang dalam request, transaksi kedua membaca stok setelah transaksi pertama selesai pada MySQL/InnoDB.
- **Lock voucher:** Voucher dikunci sebelum isValid dan increment. Tidak ditemukan jalur checkout yang melewati usage limit melalui race biasa.
- **Eligibility voucher:** Active, expiry, minimum purchase, dan usage limit diperiksa. Fixed/percent discount dibatasi subtotal dengan min.
- **Harga server-side:** Harga produk, extra_price, ongkir, diskon, dan total dihitung dari database; nominal frontend tidak menjadi sumber keputusan.
- **Quantity dasar:** Checkout menolak quantity nol, negatif, dan bukan integer.
- **Shipping dasar:** Delivery mewajibkan zona dan alamat; zona nonexistent ditolak; pickup menghasilkan ongkir nol.
- **Lead time:** Tanggal minimal checkout diperiksa server.
- **Ownership order:** Detail, status, payment, success, dan upload memeriksa pemilik. Invoice secara eksplisit mengizinkan pemilik atau admin.
- **Admin boundary:** Route admin menggunakan auth dan admin; isAdmin memeriksa role.
- **Mass assignment:** Role tidak fillable; registrasi memaksakan customer dan mempunyai regression test.
- **Address/review ownership:** Mutasi alamat memeriksa pemilik. Review memeriksa pemilik order, produk dalam order, eligibility, serta ownership review/image.
- **Review duplicate:** Ada pemeriksaan aplikasi dan unique constraint user_id/product_id/order_id.
- **Upload dasar:** Image, MIME, JPEG/PNG, dan ukuran 2 MB diperiksa. Storage membuat path/nama, bukan mengambil path client. Tidak ditemukan path traversal atau executable upload yang terbukti pada flow ini.
- **Query daftar:** Produk, order, finance, customer, dan beberapa panel admin menggunakan pagination serta eager loading.
- **Error checkout utama:** Exception pembuatan order dicatat dan detail error internal tidak langsung dibocorkan ke customer.

Temuan audit tidak menunjukkan bahwa seluruh locking salah atau harga dipercaya dari frontend. Kelemahan utama berada pada eligibility, agregasi quantity, lifecycle, dan transisi setelah checkout.

Mekanisme yang dikonfirmasi pada verifikasi ulang:

- **Dirty tracking Eloquent:** Assignment unpaid pada snapshot unpaid tidak menimpa paid concurrent karena status tidak dirty. Ini membantah subklaim PAY-004, tetapi bukan pengganti transaction/locking.
- **Proteksi command production:** FreshCommand memanggil confirmToProceed sebelum wipe; helper tidak memberi --force. Ini mempersempit TEST-002 ke risiko konfigurasi non-production, bukan meniadakan kebutuhan isolasi test.
- **Boundary admin:** Mutasi katalog/status/payment membutuhkan auth + admin. PAY-003 adalah inkonsistensi operasi admin sah, bukan bypass customer.
- **Penghapusan akun:** current_password diwajibkan. DB-001 adalah masalah retensi setelah penghapusan sah, bukan penghapusan akun orang lain.

## Recommended Fix Batches

Ini adalah rencana, bukan perubahan yang sudah diterapkan. Setiap batch harus diuji pada database disposable terisolasi. Regression test dibuat bersama perbaikannya; jangan menundanya seluruhnya ke Batch 5. Kelompok besar dibagi menjadi perubahan kecil sesuai subkelompok. Needs Further Verification harus dibuktikan sebelum diperlakukan sebagai bug Confirmed.

### Batch 1 — Security & Data Integrity

- **ID:** DB-001, DB-002, ORDER-001, PAY-003, PAY-004 — lima temuan Confirmed.
- **Alasan:** Menjaga histori, stok nonnegatif, dan konsistensi record transaksi/pembayaran dengan risiko integritas tertinggi yang sudah terbukti.
- **Dependency:** Tetapkan kebijakan retensi/snapshot sebelum FK DB-001/DB-002 diubah. Aturan transisi PAY-003 menjadi dasar atomicity/locking PAY-004; uji bersama. ORDER-001 harus mengagregasi quantity tanpa menghapus perbedaan konfigurasi. Retensi dan stok dapat dikerjakan independen dari pembayaran.
- **Test setelah batch:** Delete akun/produk/kategori/opsi dengan order aktif/lunas mempertahankan histori dan rekonsiliasi; duplicate product ID tidak oversell; rollback order/item/stock/voucher/payment; matriks transisi; fault injection update kedua; race failed dan test pembantahan snapshot unpaid; regression authorization customer/admin.

### Batch 2 — Checkout & Payment

- **ID:** PAY-001, PAY-002, ORDER-003, ORDER-004.
- **Alasan:** Melengkapi lifecycle COD/DP, identitas intent checkout, dan pelepasan inventory sebelum produksi. Pisahkan perubahan pembayaran dari perubahan lifecycle order agar tetap kecil.
- **Dependency:** Menggunakan state/atomicity PAY-003/PAY-004 serta stok ORDER-001 dari Batch 1. Definisikan penerimaan COD, akumulasi pelunasan, identitas intent, dan kebijakan cancellation sebelum implementasi.
- **Test setelah batch:** COD unpaid sampai collection; kombinasi COD/DP; DP lalu pelunasan tepat total; confirm/retry tidak menggandakan nominal; key sama membuat satu order sedangkan intent berbeda tetap valid; cancel pending memulihkan stok tepat sekali; concurrency stok/voucher dan rollback.

### Batch 3 — Validation & Edge Cases

- **ID Confirmed:** ORDER-002, CUSTOM-001, SHIP-001, PAY-005, VAL-001, VAL-002, CART-001, CART-002, VOUCHER-001, REVIEW-001.
- **Alasan:** Menyamakan kontrak server/UI dan mempertahankan pilihan customer. Kerjakan sebagai subkelompok kecil: eligibility checkout; cart/transport JSON; slot/metode/voucher; moderasi.
- **Dependency:** Customization/cart mengikuti agregasi ORDER-001 dengan varian tetap terpisah. Metode pembayaran mengikuti aturan COD/DP Batch 2. Kebijakan publikasi review harus jelas sebelum filter/reset approval disamakan.
- **Test setelah batch:** Produk unavailable; opsi kategori lain/inactive/duplikat/mutually exclusive; zona unavailable; metode/slot/JSON invalid; harga tetap dari database; pilihan cart/Beli Sekarang terpelihara; voucher invalid saat submit; visibility review dan approval setelah edit.
- **Verifikasi bersyarat:** UPLOAD-001 dan SEC-001, bukan fix Confirmed. Jalankan fault injection replacement dan GET URL bukti uji tanpa session. Jika terkonfirmasi, prioritaskan perubahan kecil untuk private storage/replacement aman bersama PAY-004, termasuk ownership/download admin. Jangan menunda exposure yang sudah terbukti hanya karena nomor batch.

### Batch 4 — Analytics & Performance

- **ID:** ANALYTICS-001, ANALYTICS-002, ANALYTICS-003, REPORT-001, PERF-001.
- **Alasan:** Membuat metrik dapat direkonsiliasi dan menghilangkan query user per order. Pisahkan perbaikan definisi metrik, field laporan, dan query kalender.
- **Dependency:** Definisi revenue mengikuti pembayaran Batch 1–2; histori harus terlindungi. Batas bulan, nama atribut, dan eager loading dapat dikerjakan terpisah setelah fixture stabil.
- **Test setelah batch:** Ranking quantity/status valid; unpaid/cancelled/DP/COD; pembayaran lintas bulan; AOV sesuai cohort; akhir bulan/tahun kabisat; nilai export/invoice/kalender; query count kalender tidak tumbuh linear.

### Batch 5 — Testing & Maintainability

- **ID:** TEST-001, TEST-002, ERROR-001.
- **Alasan:** Mengurangi false confidence suite, mengisolasi operasi destruktif testing, dan mencatat kegagalan dispatch. TEST-002 kini Medium, bukan klaim penghapusan production otomatis.
- **Dependency:** Guard/isolasi TEST-002 adalah prasyarat operasional sebelum menjalankan test batch mana pun; nomor batch tidak berarti guard ditunda. Regression test ditambahkan pada tiap perbaikan. Logging dispatch mengikuti batas commit transaksi.
- **Test setelah batch:** Helper menolak database non-test dan fail-fast; assertion record/nominal checkout; payload stok kurang yang seluruh field lainnya valid; CSRF dengan middleware aktif; mutation test terbatas; dispatch gagal dicatat tanpa membatalkan order committed. Jalankan suite pada MySQL disposable dengan GD dan laporkan test skipped.

Dokumen mempertahankan 29 temuan induk dan histori subklaim false positive. Tidak ada patch aplikasi, perbaikan bug, refactor, branch, commit, atau Pull Request dalam pembaruan laporan ini.
