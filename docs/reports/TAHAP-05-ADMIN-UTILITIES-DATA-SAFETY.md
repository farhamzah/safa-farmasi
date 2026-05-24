# Tahap 5 — Admin Utilities & Data Safety

## Ringkasan

Tahap ini menambahkan utilitas admin ringan untuk mempercepat pengelolaan konten SAFA UBP dan memperkuat keamanan data dasar. Fitur utama yang ditambahkan adalah preview landing dari admin, duplicate aplikasi, export CSV aplikasi, export CSV kategori, dan catatan backup/data safety.

## Perubahan Utama

- Menambahkan action `Duplicate` pada table Portal Applications.
- Menambahkan export CSV aplikasi dan kategori melalui route admin-only.
- Menambahkan toolbar action `Preview Landing` dan `Export CSV` pada resource yang relevan.
- Menambahkan field `accent_color` pada aplikasi agar data tersebut bisa ikut disalin ketika duplicate.
- Menambahkan service `DuplicatePortalApplication` agar logika duplicate bisa dites dan dipakai ulang.
- Memastikan route export tidak mengekspos data sensitif user/password.
- Menambahkan test untuk duplicate, unique slug, relasi kategori, dan proteksi export admin-only.

## File Dibuat/Diubah

- `app/Services/DuplicatePortalApplication.php`
- `app/Http/Controllers/Admin/ApplicationExportController.php`
- `app/Http/Controllers/Admin/CategoryExportController.php`
- `database/migrations/2026_05_22_035450_add_accent_color_to_portal_applications_table.php`
- `app/Models/PortalApplication.php`
- `app/Filament/Resources/PortalApplications/Tables/PortalApplicationsTable.php`
- `app/Filament/Resources/AppCategories/Tables/AppCategoriesTable.php`
- `routes/web.php`
- `tests/Feature/ExampleTest.php`
- `docs/reports/TAHAP-05-ADMIN-UTILITIES-DATA-SAFETY.md`

## Detail Fitur

### Preview Landing

Dashboard admin sudah memiliki quick link `Lihat Landing Page` yang membuka route `/` di tab baru. Portal Applications Resource juga memiliki toolbar action `Preview Landing` agar admin dapat cepat melihat hasil perubahan di halaman publik.

### Duplicate Aplikasi

Admin dapat menduplikasi aplikasi dari table action Portal Applications. Data utama disalin, termasuk deskripsi, URL, status, button label, accent color, sort order, toggle featured/active/open new tab, dan relasi kategori many-to-many.

Slug selalu dibuat baru dan unik dari nama salinan. Thumbnail sengaja tidak disalin agar aman dan menghindari asumsi file lama masih valid.

### Export Aplikasi

Export aplikasi tersedia sebagai CSV melalui route:

- `/admin/exports/applications`

Kolom export:

- name
- slug
- short_description
- url
- status
- button_label
- categories
- sort_order
- is_featured
- is_active
- open_in_new_tab
- updated_at

Nama file mengikuti format `safa-applications-export-YYYY-MM-DD.csv`.

### Export Kategori

Export kategori tersedia sebagai CSV melalui route:

- `/admin/exports/categories`

Kolom export:

- name
- slug
- description
- icon
- sort_order
- is_active
- applications_count
- updated_at

Nama file mengikuti format `safa-categories-export-YYYY-MM-DD.csv`.

### Catatan Backup/Data Safety

Data penting SAFA UBP tersimpan di database. File thumbnail aplikasi tersimpan di `storage/app/public/application-thumbnails`. Sebelum production, backup wajib mencakup database dan folder `storage/app/public`.

Jangan menjalankan `php artisan migrate:fresh` di production karena perintah tersebut menghapus seluruh tabel dan data.

## Validasi dan Keamanan

- Export hanya bisa diakses oleh user login dengan `is_admin=true`.
- User belum login diarahkan ke `/admin/login`.
- Non-admin mendapat `403 Forbidden`.
- Duplicate hanya tersedia di admin panel yang sudah dilindungi Filament dan `users.is_admin`.
- CSV export tidak memuat data user, password, token, atau data sensitif autentikasi.
- Tidak ada route export publik di luar prefix `/admin/exports`.
- Relasi many-to-many kategori ikut aman karena disalin melalui `sync()` pada service duplicate.

## Hasil Testing

- `php artisan migrate:fresh --seed`: berhasil.
- `php artisan test`: berhasil, 19 passed, 58 assertions.
- `npm.cmd run build`: berhasil.

## Cara Testing Manual

1. Buka `/` dan pastikan landing page tetap normal.
2. Buka `/admin/login`.
3. Login sebagai admin seed: `admin@safa.cloud` / `password`.
4. Di dashboard, klik `Lihat Landing Page` dan pastikan membuka route `/`.
5. Buka menu Aplikasi.
6. Klik action `Duplicate` pada salah satu aplikasi.
7. Pastikan aplikasi salinan muncul dengan nama `Salinan`, slug unik, dan kategori ikut tersalin.
8. Klik `Export CSV` pada menu Aplikasi, lalu buka file CSV dan cek kolomnya.
9. Buka menu Kategori, klik `Export CSV`, lalu cek file CSV kategori.
10. Logout atau gunakan user non-admin untuk memastikan route export tidak bisa diakses.

## Catatan Risiko

- Export CSV berisi data operasional aplikasi dan kategori. Jangan bagikan file CSV ke pihak yang tidak berkepentingan.
- Duplicate tidak menyalin thumbnail. Ini disengaja untuk menghindari referensi file yang mungkin sudah tidak valid atau perlu thumbnail berbeda.
- Backup otomatis belum dibuat. Backup masih berupa prosedur manual yang harus disiapkan sebelum production.
- `migrate:fresh` hanya aman untuk lokal/development, bukan production.

## Rekomendasi Tahap Berikutnya

Tahap berikutnya sebaiknya fokus pada Deployment Preparation untuk `safa.cloud`: konfigurasi environment production, MySQL/MariaDB, storage link, cache config, queue/logging, backup database/storage, hardening admin seed, dan checklist deploy tanpa `migrate:fresh`.
