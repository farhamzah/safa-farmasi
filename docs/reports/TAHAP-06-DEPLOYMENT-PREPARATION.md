# Tahap 6 — Deployment Preparation for safa.cloud

## Ringkasan

Tahap ini menyiapkan project SAFA UBP agar siap dideploy ke VPS production untuk domain `safa.cloud`. Pekerjaan difokuskan pada template environment production, dokumentasi deployment, contoh konfigurasi Nginx, backup guide, checklist go-live, dan catatan hardening password admin.

Tidak ada deployment sungguhan yang dilakukan dari Codex.

## Perubahan Utama

- Merapikan `.env.example` menjadi template production aman untuk `safa.cloud`.
- Membuat dokumentasi deployment utama `docs/DEPLOYMENT-SAFA-CLOUD.md`.
- Menambahkan contoh Nginx server block untuk Laravel.
- Menambahkan panduan backup dan restore database/storage.
- Menambahkan command update production yang aman.
- Menambahkan checklist go-live dan health check.
- Menekankan larangan `migrate:fresh` di production.
- Menambahkan panduan mengganti password admin seed sebelum production.

## File Dibuat/Diubah

- `.env.example`
- `docs/DEPLOYMENT-SAFA-CLOUD.md`
- `docs/reports/TAHAP-06-DEPLOYMENT-PREPARATION.md`

## Detail Deployment Preparation

### .env.example

`.env.example` kini memakai contoh production:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://safa.cloud`
- `LOG_LEVEL=error`
- `DB_CONNECTION=mysql`
- `DB_DATABASE=safa_ubp`
- `DB_USERNAME=safa_user`
- `DB_PASSWORD=change_this_secure_password`
- `FILESYSTEM_DISK=public`
- `SESSION_SECURE_COOKIE=true`
- `SESSION_SAME_SITE=lax`

`APP_KEY` tetap kosong dan harus digenerate di server production. Password database hanyalah placeholder dan wajib diganti.

### Deployment Guide

Dokumen `docs/DEPLOYMENT-SAFA-CLOUD.md` mencakup persiapan server, DNS, VPS, dependency, database, `.env`, Composer, build asset, migration production, storage link, permission, Nginx, SSL, optimization, backup, update workflow, troubleshooting, dan checklist go-live.

### Nginx Example

Contoh Nginx menggunakan:

- root `/var/www/safa-ubp/public`
- `try_files $uri $uri/ /index.php?$query_string`
- PHP-FPM socket placeholder
- proteksi file sensitif seperti `.env`
- `client_max_body_size 10M`

Socket PHP-FPM harus disesuaikan dengan versi PHP server.

### Backup Guide

Backup mencakup:

- database MySQL/MariaDB dengan `mysqldump`
- restore database dari `.sql.gz`
- backup `storage/app/public`
- perhatian khusus untuk `storage/app/public/application-thumbnails`

Backup harus disimpan di luar folder public dan tidak boleh masuk repository.

### Production Checklist

Checklist mencakup:

- environment production
- debug off
- APP_KEY tersedia
- database siap
- admin password diganti
- storage link
- permission
- SSL
- backup
- Laravel cache production
- larangan `migrate:fresh`

### Admin Password Hardening

Dokumentasi menegaskan bahwa `admin@safa.cloud / password` hanya untuk lokal/seed awal. Sebelum production, password wajib diganti melalui Filament atau `php artisan tinker` dengan `Hash::make()`.

## Validasi dan Keamanan

- `.env.example` tidak memuat credential asli.
- Dokumentasi melarang commit `.env` production.
- Dokumentasi melarang `migrate:fresh` di production.
- Instruksi production memakai `php artisan migrate --force`.
- SSL dan secure cookie diminta aktif.
- Backup database dan storage diwajibkan sebelum migration/update.
- Nginx example memblokir akses file sensitif.
- Admin panel tetap dilindungi login dan password kuat disarankan.

## Hasil Testing

- `php artisan test`: berhasil.
- `npm.cmd run build`: berhasil.

Migration fresh tidak diwajibkan pada tahap ini karena fokusnya deployment preparation, bukan perubahan fitur/database.

## Cara Testing Manual

Lokal:

1. Buka `/` dan pastikan landing page normal.
2. Buka `/admin/login`.
3. Login sebagai admin lokal.
4. Cek upload thumbnail, duplicate aplikasi, export CSV, dan search/filter landing.

Production checklist:

1. Pastikan DNS `safa.cloud` mengarah ke VPS.
2. Pastikan `.env` production benar.
3. Jalankan `composer install --no-dev --optimize-autoloader`.
4. Jalankan `npm ci` dan `npm run build`.
5. Jalankan `php artisan migrate --force`.
6. Jalankan `php artisan storage:link`.
7. Set permission `storage` dan `bootstrap/cache`.
8. Aktifkan SSL.
9. Jalankan Laravel cache production.
10. Ganti password admin seed.
11. Cek `https://safa.cloud` dan `https://safa.cloud/admin/login`.

## Catatan Risiko

- Jangan menjalankan `php artisan migrate:fresh` di production.
- Jangan commit `.env` production.
- Password admin seed wajib diganti.
- Backup database dan storage wajib dibuat sebelum migration/update.
- Permission storage yang salah dapat membuat upload thumbnail gagal.
- APP_DEBUG=true di production dapat membocorkan informasi sensitif.
- Backup file `.sql.gz` dan `.tar.gz` tidak boleh diletakkan di folder public.

## Rekomendasi Tahap Berikutnya

Tahap berikutnya: Final QA & Production Readiness atau Deployment Execution Checklist. Fokusnya memverifikasi server aktual, versi PHP, ekstensi PHP, permission, SSL, cron/queue jika dipakai, backup terjadwal, dan checklist go-live sebelum deploy sungguhan ke `safa.cloud`.
