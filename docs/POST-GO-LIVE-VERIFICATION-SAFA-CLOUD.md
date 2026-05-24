# Post-Go-Live Verification — SAFA UBP safa.cloud

Dokumen ini dipakai untuk membantu pengecekan saat dan setelah SAFA UBP live di `https://safa.cloud`. Dokumen ini bukan script deployment otomatis dan tidak berisi credential asli.

## 1. Identitas Sistem

- Nama aplikasi: SAFA UBP
- Kepanjangan: Satu Akses Farmasi UBP
- Domain: `https://safa.cloud`
- Admin panel: `https://safa.cloud/admin`
- Framework: Laravel 12
- Admin panel: Filament
- Database: MySQL/MariaDB
- Hosting: VPS public

## 2. Checklist Sebelum Go-Live

- [ ] DNS `safa.cloud` sudah mengarah ke IP VPS.
- [ ] SSL sudah aktif.
- [ ] `APP_ENV=production`.
- [ ] `APP_DEBUG=false`.
- [ ] `APP_URL=https://safa.cloud`.
- [ ] `APP_KEY` sudah dibuat di server.
- [ ] Database production sudah dibuat.
- [ ] User database production memakai password kuat.
- [ ] `.env` production tidak masuk repository.
- [ ] Password admin seed sudah diganti.
- [ ] `php artisan storage:link` sudah aktif.
- [ ] Permission `storage` dan `bootstrap/cache` benar.
- [ ] Backup database awal sudah dibuat.
- [ ] Backup storage awal sudah dibuat.
- [ ] Nginx root mengarah ke folder `public` Laravel.
- [ ] Tidak menjalankan `migrate:fresh` di production.

## 3. Checklist Setelah Go-Live

Cek manual melalui browser:

- [ ] `https://safa.cloud` terbuka.
- [ ] `https://safa.cloud/admin/login` terbuka.
- [ ] Login admin berhasil.
- [ ] Landing menampilkan SAFA UBP.
- [ ] Identitas Fakultas Farmasi UBP tampil.
- [ ] Kartu aplikasi tampil.
- [ ] Search aplikasi berjalan.
- [ ] Filter kategori berjalan.
- [ ] Empty state berjalan.
- [ ] Link aplikasi membuka tab baru.
- [ ] Link external memakai `rel="noopener noreferrer"`.
- [ ] Status maintenance/coming soon tombolnya disabled.
- [ ] Announcement aktif tampil.
- [ ] Announcement expired tidak tampil.
- [ ] Kontak bantuan tampil sesuai setting.
- [ ] Footer tampil dengan tahun berjalan.

## 4. Verifikasi Admin Panel

- [ ] Dashboard admin tampil.
- [ ] Quick links berjalan.
- [ ] Tambah aplikasi berhasil.
- [ ] Edit aplikasi berhasil.
- [ ] Duplicate aplikasi berhasil.
- [ ] Slug hasil duplicate unik.
- [ ] Kategori ikut tersalin saat duplicate.
- [ ] Upload thumbnail berhasil.
- [ ] Thumbnail tampil di landing.
- [ ] Export CSV aplikasi berhasil.
- [ ] Export CSV kategori berhasil.
- [ ] Kategori bisa ditambah/edit.
- [ ] Pengumuman bisa ditambah/edit.
- [ ] Landing settings bisa diedit.
- [ ] Non-admin tidak bisa akses admin.

## 5. Verifikasi File dan Storage

- [ ] `php artisan storage:link` sudah aktif.
- [ ] File thumbnail tersimpan di `storage/app/public/application-thumbnails`.
- [ ] Thumbnail bisa diakses dari browser.
- [ ] Jika thumbnail kosong/hilang, fallback icon tampil.
- [ ] Permission storage aman.
- [ ] Folder backup tidak berada di public web root.

## 6. Verifikasi Keamanan Dasar

- [ ] `APP_DEBUG=false`.
- [ ] `.env` tidak bisa diakses dari browser.
- [ ] `/admin` wajib login.
- [ ] Export CSV wajib login admin.
- [ ] Tidak ada route publik untuk create/update/delete.
- [ ] Password admin seed tidak lagi password default.
- [ ] SSL valid.
- [ ] HTTP redirect ke HTTPS jika dikonfigurasi.
- [ ] File backup tidak berada di public directory.

## 7. Verifikasi Backup

- [ ] Backup database berhasil dibuat.
- [ ] Backup storage berhasil dibuat.
- [ ] File backup tersimpan di luar public web root.
- [ ] Restore procedure terdokumentasi.
- [ ] Backup tidak masuk repository.
- [ ] Jadwal backup berkala sudah direncanakan.

## 8. Verifikasi Update Production

Checklist simulasi update aman:

- [ ] Backup database dan storage sebelum update.
- [ ] `git pull` atau upload release baru.
- [ ] `composer install --no-dev --optimize-autoloader`.
- [ ] `npm ci` atau `npm install`.
- [ ] `npm run build`.
- [ ] `php artisan migrate --force`.
- [ ] `php artisan optimize:clear`.
- [ ] `php artisan config:cache`.
- [ ] `php artisan route:cache`.
- [ ] `php artisan view:cache`.
- [ ] Cek ulang `/` dan `/admin`.

Penting: jangan memakai `migrate:fresh` di production.

## 9. Troubleshooting Pasca Go-Live

### 500 Internal Server Error

Gejala:

- Halaman menampilkan error 500 atau blank.

Kemungkinan penyebab:

- `.env` salah.
- Cache config lama.
- Permission `storage` atau `bootstrap/cache` bermasalah.
- Dependency Composer belum lengkap.
- Error aplikasi tercatat di log.

Langkah pengecekan:

- Cek `storage/logs/laravel.log`.
- Cek log Nginx.
- Cek `.env` production.
- Jalankan `php artisan optimize:clear`.

Solusi aman:

- Perbaiki konfigurasi yang salah.
- Pastikan permission benar.
- Jalankan ulang `composer install --no-dev --optimize-autoloader`.
- Rebuild cache Laravel setelah konfigurasi benar.

### 403 Forbidden

Gejala:

- Browser menampilkan 403 saat membuka halaman tertentu.

Kemungkinan penyebab:

- Permission folder salah.
- Nginx tidak bisa membaca folder `public`.
- User non-admin mencoba mengakses admin/export.

Langkah pengecekan:

- Cek permission folder project.
- Cek konfigurasi Nginx `root`.
- Cek apakah user yang login memiliki `is_admin=true`.

Solusi aman:

- Perbaiki ownership dan permission.
- Pastikan Nginx root mengarah ke `/var/www/safa-ubp/public`.
- Gunakan akun admin valid untuk akses admin.

### 404 Not Found

Gejala:

- `/` atau route Laravel menampilkan 404 dari Nginx.

Kemungkinan penyebab:

- Nginx root bukan folder `public`.
- `try_files` belum diarahkan ke `index.php`.
- Route cache belum sesuai.

Langkah pengecekan:

- Cek server block Nginx.
- Jalankan `sudo nginx -t`.
- Cek daftar route dengan `php artisan route:list`.

Solusi aman:

- Ubah root ke `/var/www/safa-ubp/public`.
- Pastikan `try_files $uri $uri/ /index.php?$query_string;` tersedia.
- Jalankan `php artisan route:cache` setelah route benar.

### Admin Login Gagal

Gejala:

- Admin tidak bisa login meski halaman login terbuka.

Kemungkinan penyebab:

- Password salah.
- Password seed belum diganti atau tidak diketahui.
- Session/cache bermasalah.
- User admin tidak memiliki `is_admin=true`.

Langkah pengecekan:

- Cek email admin.
- Cek data user di database.
- Clear cache Laravel.

Solusi aman:

- Reset password admin dengan fitur UI jika tersedia.
- Jika perlu, gunakan `php artisan tinker` di server dan set password baru dengan `Hash::make`.
- Pastikan `is_admin=true` untuk akun admin.

### CSRF atau Session Issue

Gejala:

- Login kembali ke halaman login.
- Muncul pesan token expired atau page expired.

Kemungkinan penyebab:

- `APP_URL` tidak sesuai domain HTTPS.
- `SESSION_SECURE_COOKIE=true` tetapi SSL belum aktif.
- Cache config lama.
- Permission session storage bermasalah.

Langkah pengecekan:

- Cek `APP_URL`.
- Cek SSL.
- Cek folder session di storage.
- Jalankan `php artisan optimize:clear`.

Solusi aman:

- Set `APP_URL=https://safa.cloud`.
- Pastikan SSL aktif.
- Pastikan permission storage benar.
- Rebuild cache config setelah `.env` benar.

### Thumbnail Tidak Tampil

Gejala:

- Kartu aplikasi tampil tetapi gambar thumbnail tidak muncul.

Kemungkinan penyebab:

- `storage:link` belum dibuat.
- File thumbnail tidak ada.
- Permission storage salah.
- `FILESYSTEM_DISK` bukan `public`.

Langkah pengecekan:

- Cek symbolic link `public/storage`.
- Cek file di `storage/app/public/application-thumbnails`.
- Cek `.env` bagian `FILESYSTEM_DISK`.

Solusi aman:

- Jalankan `php artisan storage:link`.
- Perbaiki permission storage.
- Upload ulang thumbnail jika file hilang.
- Pastikan fallback icon tetap tampil.

### Upload Gagal

Gejala:

- Upload thumbnail gagal atau request ditolak.

Kemungkinan penyebab:

- Permission storage salah.
- Ukuran file melebihi limit aplikasi atau Nginx.
- Format file tidak diizinkan.
- PHP upload limit terlalu kecil.

Langkah pengecekan:

- Cek log Laravel.
- Cek `client_max_body_size` Nginx.
- Cek `upload_max_filesize` dan `post_max_size` PHP.
- Cek tipe file yang diupload.

Solusi aman:

- Gunakan JPG, JPEG, PNG, atau WEBP di bawah batas ukuran.
- Set `client_max_body_size 10M`.
- Sesuaikan limit PHP jika diperlukan.
- Perbaiki permission storage.

### Export CSV Gagal

Gejala:

- Export aplikasi/kategori gagal atau 403.

Kemungkinan penyebab:

- User belum login.
- User bukan admin.
- Route export tidak tersedia.
- Error query database.

Langkah pengecekan:

- Login ulang sebagai admin.
- Pastikan `is_admin=true`.
- Cek route export.
- Cek log Laravel.

Solusi aman:

- Gunakan akun admin.
- Pastikan export route tetap berada di area admin-only.
- Perbaiki error data jika tercatat di log.

### Database Connection Error

Gejala:

- Aplikasi gagal memuat data dan log menunjukkan koneksi database gagal.

Kemungkinan penyebab:

- MySQL/MariaDB mati.
- Credential `.env` salah.
- Database belum dibuat.
- User database belum diberi privilege.

Langkah pengecekan:

- Cek service database.
- Cek `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD`.
- Coba login database dari CLI.

Solusi aman:

- Nyalakan service database.
- Perbaiki credential `.env`.
- Pastikan database dan privilege sudah benar.
- Rebuild config cache.

### Permission Denied

Gejala:

- Error menulis cache, log, session, atau upload.

Kemungkinan penyebab:

- Ownership folder salah.
- Permission `storage` atau `bootstrap/cache` terlalu ketat.

Langkah pengecekan:

- Cek owner dan group folder.
- Cek log Laravel/Nginx.

Solusi aman:

- Set ownership ke user deploy dan group web server.
- Set permission writable untuk `storage` dan `bootstrap/cache`.

### SSL Error

Gejala:

- Browser menampilkan sertifikat invalid atau HTTPS gagal.

Kemungkinan penyebab:

- Sertifikat belum diterbitkan.
- DNS belum resolve ke VPS.
- Certbot gagal.
- Port 80/443 tertutup.

Langkah pengecekan:

- Cek DNS.
- Cek Nginx.
- Cek status Certbot.
- Cek firewall.

Solusi aman:

- Tunggu DNS valid.
- Buka port 80 dan 443.
- Jalankan Certbot setelah DNS benar.
- Pastikan auto-renew aktif.

### DNS Belum Resolve

Gejala:

- Domain belum membuka server baru.

Kemungkinan penyebab:

- A record belum benar.
- Propagasi DNS belum selesai.
- Cache DNS lokal.

Langkah pengecekan:

- Cek DNS dari provider.
- Gunakan `dig safa.cloud`.
- Coba jaringan berbeda jika perlu.

Solusi aman:

- Perbaiki A record.
- Tunggu propagasi.
- Jangan menerbitkan SSL sebelum DNS mengarah benar.

### Asset CSS/JS Tidak Muncul

Gejala:

- Landing/admin tampil tanpa styling atau interaksi frontend rusak.

Kemungkinan penyebab:

- `npm run build` belum dijalankan.
- Folder `public/build` belum terupload.
- Manifest asset tidak ditemukan.
- Permission public build salah.

Langkah pengecekan:

- Cek folder `public/build`.
- Cek network request CSS/JS di browser.
- Cek log Laravel.

Solusi aman:

- Jalankan `npm ci` atau `npm install`.
- Jalankan `npm run build`.
- Upload folder build jika deployment manual.
- Pastikan permission file public benar.

### APP_KEY Belum Dibuat

Gejala:

- Error encryption key atau session tidak stabil.

Kemungkinan penyebab:

- `APP_KEY` kosong.
- `.env` belum disiapkan.

Langkah pengecekan:

- Cek `.env` di server.

Solusi aman:

- Jalankan `php artisan key:generate` sekali di server.
- Rebuild config cache.

### Cache Config Bermasalah

Gejala:

- Perubahan `.env` tidak terbaca.
- Aplikasi masih memakai konfigurasi lama.

Kemungkinan penyebab:

- Config cache belum dibersihkan setelah edit `.env`.

Langkah pengecekan:

- Cek nilai `.env`.
- Cek apakah cache Laravel aktif.

Solusi aman:

- Jalankan `php artisan optimize:clear`.
- Setelah `.env` benar, jalankan `php artisan config:cache`, `route:cache`, dan `view:cache`.

## 10. Format Berita Acara Verifikasi

```text
Tanggal:
Nama pemeriksa:
Domain:
Status SSL:
Status landing:
Status admin:
Status database:
Status backup:
Catatan:
Keputusan:
[ ] Layak go-live
[ ] Perlu perbaikan
```
