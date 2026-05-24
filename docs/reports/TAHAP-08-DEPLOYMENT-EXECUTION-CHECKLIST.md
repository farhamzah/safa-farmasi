# Tahap 8 — Deployment Execution Checklist for safa.cloud

## Ringkasan

Tahap ini membuat checklist eksekusi deployment aktual untuk SAFA UBP ke VPS production dengan domain `safa.cloud`. Dokumen dibuat sebagai panduan runtut untuk hari deploy, mulai dari prasyarat server, setup database, konfigurasi `.env`, install dependency, Nginx, SSL, health check, backup, update aplikasi, rollback sederhana, sampai troubleshooting.

Tidak ada deployment sungguhan yang dilakukan. Tidak ada command production dijalankan. Tidak ada credential asli disimpan.

## File Dibuat/Diubah

- `docs/DEPLOYMENT-EXECUTION-CHECKLIST-SAFA-CLOUD.md`
  - File baru berisi checklist eksekusi deployment aktual.
- `docs/DEPLOYMENT-SAFA-CLOUD.md`
  - Menambahkan referensi ke checklist eksekusi deployment baru.
- `docs/reports/TAHAP-08-DEPLOYMENT-EXECUTION-CHECKLIST.md`
  - Report tahap 8.

## Isi Checklist Deployment

Dokumen `docs/DEPLOYMENT-EXECUTION-CHECKLIST-SAFA-CLOUD.md` memuat:

- Informasi deployment SAFA UBP untuk `https://safa.cloud`.
- Prasyarat akses SSH, DNS, user Linux non-root, Git/upload, database, PHP, Composer, Node/NPM, Nginx, dan Certbot.
- Checklist lokal sebelum upload, termasuk test, build, `.env`, dan dokumentasi.
- Persiapan server dengan contoh command install dependency umum.
- Setup database production MySQL/MariaDB dengan placeholder password.
- Dua opsi upload project: clone Git repository atau upload manual.
- Konfigurasi `.env` production yang aman.
- Install dependency production Laravel dan frontend.
- Command Laravel production yang aman.
- Contoh server block Nginx untuk `safa.cloud`.
- Setup SSL Let's Encrypt dengan Certbot.
- Post-deployment health check.
- Instruksi mengganti password admin.
- Backup awal database dan storage.
- Prosedur update aplikasi setelah production.
- Rollback manual sederhana.
- Troubleshooting umum.
- Final go-live checklist.

## Validasi Keamanan

Dokumen menekankan beberapa guardrail production:

- Jangan commit `.env`.
- Jangan memakai credential asli di dokumentasi atau repository.
- Jangan memakai `migrate:fresh` di production.
- Password admin seed wajib diganti sebelum production.
- Backup database dan storage wajib dilakukan sebelum update atau migration.
- `APP_DEBUG=false` wajib di production.
- SSL harus aktif untuk `https://safa.cloud`.
- Database password harus kuat.
- Backup disimpan di luar public web root dan tidak masuk repository.

## Hasil Testing

Command yang dijalankan:

```bash
php artisan test
```

Hasil:

```text
22 passed, 73 assertions
```

Command yang dijalankan:

```bash
npm run build
```

Hasil:

```text
vite build berhasil
```

`php artisan migrate:fresh` tidak dijalankan karena tahap ini hanya dokumentasi dan tidak ada perubahan database.

## Cara Menggunakan Dokumen

Gunakan `docs/DEPLOYMENT-EXECUTION-CHECKLIST-SAFA-CLOUD.md` saat deployment aktual ke VPS:

1. Baca dokumen dari awal sebelum mulai deploy.
2. Siapkan credential production di luar repository.
3. Ikuti checklist dari prasyarat sampai final go-live.
4. Centang item yang sudah selesai.
5. Simpan hasil health check dan catatan deploy.
6. Jangan menjalankan command yang belum disesuaikan dengan user, path, versi PHP, dan konfigurasi VPS.

## Catatan Risiko

- DNS propagation bisa menyebabkan SSL atau akses domain belum langsung valid.
- SSL Let's Encrypt bisa gagal jika DNS belum benar atau port 80/443 tertutup.
- Permission `storage` dan `bootstrap/cache` yang salah bisa membuat upload/cache gagal.
- Credential database yang salah akan menyebabkan aplikasi gagal koneksi.
- Backup yang belum diuji bisa menyulitkan rollback.
- PHP extension yang kurang bisa membuat Composer atau Laravel error.
- Dependency Composer/NPM bisa gagal jika versi PHP/Node tidak sesuai.
- `storage:link` yang belum dibuat membuat thumbnail tidak tampil.

## Rekomendasi Tahap Berikutnya

Tahap berikutnya direkomendasikan:

**Tahap 9 — Deployment Support & Post-Go-Live Verification**

Fokusnya adalah pendampingan saat deploy aktual, smoke test setelah go-live, verifikasi backup, dan dokumentasi hasil deployment.
