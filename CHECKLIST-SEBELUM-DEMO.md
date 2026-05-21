# Checklist Sebelum Demo/Submit

Pastikan semua langkah di bawah selesai sebelum presentation atau submission final.

## Pre-Demo Checklist

- [ ] **Set APP_DEBUG=false** di `.env` (line 4)
  - Alasan: APP_DEBUG=true mengekspos stack trace, file path, SQL query, env var saat error
  - Lakukan: Edit `.env`, ubah `APP_DEBUG=true` → `APP_DEBUG=false`

- [ ] **Clear config cache**: Jalankan `php artisan config:clear`
  - Memastikan Laravel membaca `.env` yang sudah diubah

- [ ] **Verifikasi test suite tetap hijau**: Jalankan `php artisan test`
  - Target: Semua 105 tests PASS (0 failed, 0 skipped)
  - Jika ada yang gagal, debug sebelum demo

- [ ] **Konfirmasi bisa login admin** dengan kredensial di README (`admin@jagoankue.test` / `password`) setelah `migrate:fresh --seed`

- [ ] **Verifikasi .env tidak ter-commit**: Jalankan `git status` dan `git log --oneline -1`
  - Pastikan `.env` TIDAK ada di staged files atau recent commits
  - `.env` harus always untracked (ada di `.gitignore`)

- [ ] **Kembalikan APP_DEBUG=true** setelah selesai demo (jika development lanjut)
  - Lakukan: Edit `.env`, ubah `APP_DEBUG=false` → `APP_DEBUG=true`
  - Jalankan: `php artisan config:clear`

## Catatan

- File checklist ini **TIDAK perlu di-commit** — gunakan sebagai panduan manual
- Semua audit keamanan modul 1-11 sudah complete dengan bukti
- Custom error pages (403/404/500) sudah implemented sebagai mitigasi debug exposure
