# Tahap 7 — Final QA & Production Readiness

## Ringkasan

Tahap ini melakukan audit akhir sebelum deployment SAFA UBP ke VPS production untuk domain `safa.cloud`. Fokusnya adalah validasi route publik/admin, perilaku landing page, keamanan akses admin/export, kesiapan dokumentasi deployment, penambahan test final yang tidak rapuh, dan checklist production readiness.

Tidak ada deployment sungguhan yang dilakukan pada tahap ini. Tidak ada command production dijalankan. `migrate:fresh` tidak dijalankan dan tetap tidak boleh digunakan di production.

## Audit Route

Hasil audit `php artisan route:list`:

- Route `/` tersedia sebagai landing page publik dan tidak membutuhkan login.
- Route `/admin` dan seluruh resource Filament berada di area admin yang membutuhkan login.
- Akses admin tetap diproteksi oleh `users.is_admin`.
- Route export hanya tersedia di prefix `/admin/exports`:
  - `/admin/exports/applications`
  - `/admin/exports/categories`
- Export CSV menolak guest dengan redirect ke `/admin/login`.
- Export CSV menolak user non-admin dengan HTTP 403.
- Tidak ditemukan route publik untuk create, update, atau delete data aplikasi, kategori, pengumuman, setting, maupun user.
- Tidak ada route publik yang mengekspos data sensitif user/password.
- Quick link "Lihat Landing Page" hanya membuka landing publik `/`, bukan halaman preview atau route data baru.

## Audit Landing Page

Landing page dicek melalui test otomatis dan browser lokal.

Hasil audit:

- `/` mengembalikan 200 OK.
- Identitas `SAFA UBP` tampil.
- Identitas `Fakultas Farmasi Universitas Buana Perjuangan Karawang` tampil.
- Aplikasi dengan `status=inactive` tidak tampil.
- Aplikasi dengan `is_active=false` tidak tampil.
- Kategori nonaktif tidak tampil di filter.
- Search `q` dapat menemukan aplikasi yang sesuai.
- Search tanpa hasil menampilkan empty state.
- Aplikasi `active` dan `internal` memiliki link aktif.
- Aplikasi `maintenance` dan `coming_soon` tampil, tetapi tidak menjadi link aktif.
- Link aplikasi memakai `target="_blank"` dan `rel="noopener noreferrer"`.
- Thumbnail fallback tetap aman ketika gambar tidak tersedia.
- Announcement aktif dan masih valid tampil.
- Announcement expired tidak tampil.
- Contact section memiliki fallback aman ketika `contact_email` dan `contact_whatsapp` kosong.

## Audit Admin Panel

Admin panel dicek melalui test feature dan manual check ringan.

Hasil audit:

- `/admin/login` dapat dibuka dan form login tampil.
- Admin dengan `is_admin=true` dapat mengakses `/admin`.
- User non-admin ditolak dari `/admin`.
- Dashboard admin tetap tersedia.
- Resource aplikasi, kategori, pengumuman, dan landing setting dapat diakses admin.
- Form create resource utama dapat dibuka admin.
- Duplicate aplikasi menghasilkan record baru dengan slug unik.
- Relasi kategori many-to-many ikut tersalin saat duplicate.
- Export aplikasi menghasilkan CSV dan hanya bisa diakses admin.
- Export kategori menghasilkan CSV dan hanya bisa diakses admin.
- Validasi URL/status, upload image, dan validasi tanggal pengumuman tetap berada di resource Filament yang sudah dipoles pada tahap sebelumnya.
- Setting landing tetap menggunakan fallback sehingga value kosong tidak merusak landing page.

## Test Final

Test final yang ditambahkan/diperkuat:

- Aplikasi dengan `is_active=false` tidak tampil di landing.
- Aplikasi `internal` memiliki link aktif dengan atribut external link yang aman.
- Contact section menampilkan fallback saat setting kontak kosong.
- Export aplikasi dicek content type CSV dan header kolomnya.
- Export kategori dicek content type CSV dan header kolomnya.

Test yang sudah ada tetap mencakup:

- Landing page 200 OK dan menampilkan SAFA UBP.
- Aplikasi aktif tampil.
- Aplikasi inactive tidak tampil.
- Search/filter tidak menampilkan aplikasi inactive.
- Maintenance dan coming soon tidak menjadi link aktif.
- Kategori nonaktif tidak tampil.
- Empty state search tampil.
- Announcement aktif tampil.
- Announcement expired tidak tampil.
- Admin dapat mengakses panel.
- Non-admin tidak dapat mengakses panel.
- Duplicate aplikasi menyalin kategori dan membuat slug unik.
- Guest/non-admin tidak dapat export.

## Audit Dokumentasi Deployment

Dokumentasi `docs/DEPLOYMENT-SAFA-CLOUD.md` sudah mencakup:

- Kebutuhan server.
- DNS domain `safa.cloud`.
- Setup VPS.
- PHP extension umum untuk Laravel.
- Composer.
- Node/NPM.
- MySQL/MariaDB.
- Konfigurasi `.env`.
- `composer install --no-dev --optimize-autoloader`.
- `npm ci` dan `npm run build`.
- `php artisan migrate --force`.
- `php artisan storage:link`.
- Permission `storage` dan `bootstrap/cache`.
- Konfigurasi Nginx.
- SSL Let's Encrypt.
- Cache config/route/view.
- Backup database.
- Backup storage.
- Restore database.
- Update aplikasi.
- Health check.
- Troubleshooting umum.

Checklist go-live juga dirapikan agar eksplisit memuat DNS, upload thumbnail, export CSV, duplicate aplikasi, search/filter landing, backup database, backup storage, larangan `migrate:fresh`, dan larangan memasukkan `.env` production ke repository.

## Production Readiness Checklist

- [ ] Domain DNS `safa.cloud` sudah mengarah ke VPS.
- [ ] SSL aktif.
- [ ] `APP_ENV=production`.
- [ ] `APP_DEBUG=false`.
- [ ] `APP_URL=https://safa.cloud`.
- [ ] `APP_KEY` sudah dibuat.
- [ ] Database production sudah dibuat.
- [ ] User database memakai password kuat.
- [ ] Password admin seed sudah diganti.
- [ ] Storage link aktif.
- [ ] Folder `storage` dan `bootstrap/cache` permission-nya benar.
- [ ] Nginx root mengarah ke folder `public` Laravel.
- [ ] Upload thumbnail berhasil.
- [ ] Export CSV berhasil.
- [ ] Duplicate aplikasi berhasil.
- [ ] Search/filter landing berhasil.
- [ ] Backup database berjalan.
- [ ] Backup storage berjalan.
- [ ] Tidak ada `migrate:fresh` di production.
- [ ] Tidak ada `.env` production masuk repository.

## File Dibuat/Diubah

- `resources/views/partials/application-card.blade.php`
  - Link aplikasi dibuat konsisten memakai `target="_blank"` dan `rel="noopener noreferrer"`.
- `tests/Feature/ExampleTest.php`
  - Menambahkan dan memperkuat test final QA landing, export CSV, dan akses.
- `docs/DEPLOYMENT-SAFA-CLOUD.md`
  - Menambah checklist production readiness yang lebih eksplisit.
- `docs/reports/TAHAP-07-FINAL-QA-PRODUCTION-READINESS.md`
  - Report final QA tahap 7.

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
vite v7.3.3 building client environment for production...
55 modules transformed.
manifest.json 0.33 kB
assets/app-B3gt_xP9.css 67.47 kB
assets/app-UyRVujZY.js 42.40 kB
built in 841ms
```

`php artisan migrate:fresh --seed` tidak dijalankan pada tahap ini karena tidak ada perubahan migration dan fokusnya adalah production readiness. Untuk production, tetap gunakan `php artisan migrate --force`, bukan `migrate:fresh`.

Manual browser check lokal:

- `http://127.0.0.1:8000/`
  - Halaman terbuka.
  - `SAFA UBP` tampil.
  - Identitas fakultas tampil.
  - Search input tersedia.
- `http://127.0.0.1:8000/admin/login`
  - Halaman login terbuka.
  - Form login dan input email tersedia.

## Cara Testing Manual

Testing `/`:

1. Jalankan server lokal dengan `php artisan serve`.
2. Buka `http://127.0.0.1:8000/`.
3. Pastikan identitas SAFA UBP dan Fakultas Farmasi UBP tampil.
4. Coba search aplikasi dengan kata kunci yang ada.
5. Coba search dengan kata kunci yang tidak ada dan pastikan empty state tampil.
6. Pilih filter kategori aktif.
7. Pastikan kategori nonaktif tidak tampil.
8. Pastikan aplikasi maintenance/coming soon tidak memiliki link aktif.
9. Pastikan aplikasi active/internal memiliki tombol aktif.

Testing `/admin`:

1. Buka `http://127.0.0.1:8000/admin/login`.
2. Login sebagai admin lokal.
3. Pastikan dashboard tampil.
4. Buka resource Aplikasi, Kategori, Pengumuman, dan Pengaturan Landing.
5. Tambah/edit aplikasi.
6. Upload thumbnail gambar valid.
7. Coba duplicate aplikasi dan pastikan slug unik serta kategori ikut tersalin.
8. Export aplikasi dan kategori ke CSV.
9. Edit announcement dengan tanggal valid.
10. Kosongkan setting kontak tertentu dan pastikan landing tetap aman dengan fallback.

Testing announcement:

1. Buat announcement aktif dengan rentang tanggal valid.
2. Buka landing dan pastikan banner tampil.
3. Ubah `end_at` menjadi tanggal lampau.
4. Buka landing dan pastikan banner tidak tampil.

Testing setting landing:

1. Edit `hero_title`, `hero_description`, `contact_email`, `contact_whatsapp`, dan `footer_text`.
2. Buka landing.
3. Pastikan perubahan tampil.
4. Kosongkan salah satu value.
5. Pastikan fallback tetap membuat halaman rapi.

## Catatan Risiko

- Password akun seed `admin@safa.cloud` wajib diganti sebelum production.
- Backup database dan storage wajib disiapkan sebelum migration production.
- Permission folder `storage` dan `bootstrap/cache` harus benar agar upload dan cache berjalan.
- SSL harus aktif sebelum go-live agar cookie secure dan URL production konsisten.
- Jangan menjalankan `php artisan migrate:fresh` di production karena akan menghapus data.
- Jangan commit `.env` production atau file backup ke repository.
- DNS `safa.cloud` perlu diverifikasi setelah diarahkan ke VPS karena propagasi bisa membutuhkan waktu.
- Export CSV hanya berisi data aplikasi/kategori, tetapi file hasil export tetap perlu diperlakukan sebagai data internal.

## Rekomendasi Tahap Berikutnya

Tahap berikutnya:

**Tahap 8 — Deployment Execution Checklist for safa.cloud**

Fokus tahap berikutnya adalah membuat checklist eksekusi deployment yang lebih operasional untuk hari go-live, termasuk urutan command aman, verifikasi DNS/SSL, backup pre-deploy, smoke test setelah deploy, dan rollback plan sederhana.
