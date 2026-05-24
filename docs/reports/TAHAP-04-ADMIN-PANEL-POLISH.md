# Tahap 4 — Admin Panel Polish

## Ringkasan

Tahap ini memoles admin panel SAFA UBP agar lebih nyaman dipakai admin Fakultas/TU untuk mengelola aplikasi, kategori, pengumuman, dan setting landing page. Fokus perubahan ada pada struktur form, validasi, table columns, filters, dashboard ringkas, serta dokumentasi testing.

## Perubahan Utama

- Memoles form aplikasi dengan section yang lebih jelas, slug otomatis dari name, helper text, kategori multi-select, upload thumbnail aman, dan validasi URL kondisional.
- Menambahkan `is_active` pada aplikasi agar admin bisa menyembunyikan aplikasi tanpa menghapus atau mengubah status.
- Memoles table aplikasi dengan status badge, kategori, thumbnail, filter status/kategori/aktif, dan default order unggulan lalu urutan.
- Menambahkan metadata kategori: description dan icon.
- Memoles pengumuman dengan type badge, message wajib, jadwal tampil, dan validasi end date.
- Mengubah Landing Settings Resource menjadi key/value sederhana dengan group dan type.
- Menambahkan dashboard admin sederhana berisi ringkasan angka dan quick links.
- Menambah test untuk akses admin, resource table/form, landing, status aplikasi, dan kategori nonaktif.

## File Dibuat/Diubah

- `app/Filament/Resources/PortalApplications/Schemas/PortalApplicationForm.php`
- `app/Filament/Resources/PortalApplications/Tables/PortalApplicationsTable.php`
- `app/Filament/Resources/AppCategories/Schemas/AppCategoryForm.php`
- `app/Filament/Resources/AppCategories/Tables/AppCategoriesTable.php`
- `app/Filament/Resources/Announcements/Schemas/AnnouncementForm.php`
- `app/Filament/Resources/Announcements/Tables/AnnouncementsTable.php`
- `app/Filament/Resources/LandingSettings/Schemas/LandingSettingForm.php`
- `app/Filament/Resources/LandingSettings/Tables/LandingSettingsTable.php`
- `app/Filament/Widgets/AdminOverview.php`
- `app/Filament/Widgets/AdminQuickLinks.php`
- `resources/views/filament/widgets/admin-quick-links.blade.php`
- `app/Providers/Filament/AdminPanelProvider.php`
- `app/Models/PortalApplication.php`
- `app/Models/AppCategory.php`
- `app/Models/LandingSetting.php`
- `app/Support/LandingSettings.php`
- `database/migrations/2026_05_22_034157_add_admin_polish_fields_to_portal_applications_table.php`
- `database/migrations/2026_05_22_034157_add_admin_polish_fields_to_app_categories_table.php`
- `database/migrations/2026_05_22_034157_add_key_value_fields_to_landing_settings_table.php`
- `database/seeders/DatabaseSeeder.php`
- `tests/Feature/ExampleTest.php`
- `docs/reports/TAHAP-04-ADMIN-PANEL-POLISH.md`

## Detail Fitur Admin

### Portal Applications

Form aplikasi kini dikelompokkan menjadi Informasi Aplikasi, Konten Kartu, dan Akses/Status. Slug otomatis dibuat dari name tetapi tetap bisa diedit. URL wajib untuk status `active` dan `internal`, sementara `maintenance` dan `coming_soon` boleh tanpa URL. Upload thumbnail memakai disk `public`, direktori `application-thumbnails`, image only, maksimal 2 MB, dan menerima JPG/JPEG/PNG/WebP.

Table aplikasi menampilkan thumbnail, name, kategori, slug, URL, status badge, sort order, featured, active toggle, dan updated at. Table juga memiliki filter status, kategori, dan aktif/nonaktif, serta default order `is_featured desc`, `sort_order asc`, `name asc`.

### App Categories

Form kategori sekarang memiliki slug otomatis dari name, field description opsional, icon opsional, sort order, dan active toggle. Table kategori menampilkan jumlah aplikasi terkait, sort order, active status, dan updated at dengan default order sort order lalu name.

### Announcements

Form pengumuman memuat title, message wajib, type (`info`, `success`, `warning`, `danger`), URL opsional, jadwal start/end, dan active toggle. End date divalidasi tidak boleh lebih awal dari start date. Table menampilkan type sebagai badge serta filter type dan active.

### Landing Settings

Landing settings dibuat lebih ramah sebagai key/value sederhana. Setiap setting punya group (`general`, `hero`, `contact`, `footer`), type (`text`, `textarea`, `email`, `url`, `phone`), key unik, dan value opsional. Landing page tetap aman karena helper `setting()` memiliki fallback default jika value kosong.

### Dashboard Admin

Dashboard admin berisi ringkasan jumlah aplikasi aktif, maintenance, coming soon, kategori aktif, dan pengumuman aktif. Tersedia quick links untuk Kelola Aplikasi, Kelola Kategori, Pengumuman, Pengaturan Landing, dan Lihat Landing Page.

## Validasi dan Keamanan

- Admin panel tetap dilindungi oleh `users.is_admin`.
- Non-admin mendapat forbidden saat mengakses panel.
- Slug aplikasi dan kategori unik.
- URL aplikasi wajib hanya untuk status `active` dan `internal`.
- Upload thumbnail dibatasi image, ukuran 2 MB, dan MIME JPG/JPEG/PNG/WebP.
- Kategori nonaktif tidak tampil di filter landing.
- Aplikasi `inactive` atau `is_active=false` tidak tampil di landing.
- Relasi many-to-many kategori dan aplikasi aman karena pivot memakai cascade delete.

## Hasil Testing

- `php artisan migrate:fresh --seed`: berhasil.
- `php artisan test`: berhasil, 16 passed, 43 assertions.
- `npm.cmd run build`: berhasil.

## Cara Testing Manual

1. Buka `/` dan pastikan landing page tampil normal.
2. Buka `/admin/login`.
3. Login dengan akun seed `admin@safa.cloud` dan password `password`.
4. Buka menu Aplikasi, tambah/edit aplikasi, isi kategori, status, URL, thumbnail, sort order, dan toggle.
5. Coba status `active` tanpa URL, pastikan admin meminta URL.
6. Coba status `maintenance` atau `coming_soon` tanpa URL, pastikan bisa disimpan.
7. Buka menu Kategori, tambah/edit kategori, ubah sort order dan active toggle.
8. Buka menu Pengumuman, tambah/edit announcement dan cek validasi start/end.
9. Buka menu Pengaturan Landing, edit setting key/value seperti `hero_title`, `hero_description`, `contact_email`, dan `footer_text`.
10. Refresh `/` dan pastikan perubahan tampil sesuai data.

## Catatan Risiko

- Landing Settings masih mendukung data lama berbentuk kolom legacy, tetapi resource admin sekarang menampilkan mode key/value. Untuk deployment production, pastikan data setting utama sudah dibuat sebagai key/value oleh seeder atau migration data.
- Password admin seed masih `password`, wajib diganti sebelum production.
- Validasi upload sudah dibatasi di Filament, tetapi hardening tambahan di level web server tetap disarankan untuk production.

## Rekomendasi Tahap Berikutnya

- Tambahkan action duplicate aplikasi untuk mempercepat input layanan baru.
- Tambahkan preview landing dari admin.
- Tambahkan role/permission lebih granular jika admin lebih dari satu tipe.
- Tambahkan backup/export data aplikasi dan kategori.
