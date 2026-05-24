# Tahap 10 — Branding Polish & Optional Enhancements

## Ringkasan

Tahap ini menambahkan polish ringan sebelum deployment aktual SAFA UBP. Perubahan berfokus pada branding logo/favicon, meta SEO/Open Graph sederhana, tracking klik aplikasi, analytics ringan di dashboard admin, export klik CSV admin-only, dan dokumentasi handover admin.

Tidak ada deployment sungguhan yang dilakukan. Tidak ada credential production disimpan.

## File Dibuat/Diubah

- `public/images/logo-fakultas-farmasi-ubp.png`
- `public/favicon.png`
- `database/migrations/2026_05_23_000001_create_application_clicks_table.php`
- `app/Models/ApplicationClick.php`
- `app/Models/PortalApplication.php`
- `app/Models/LandingSetting.php`
- `app/Support/LandingSettings.php`
- `app/Http/Controllers/ApplicationRedirectController.php`
- `app/Http/Controllers/Admin/ClickExportController.php`
- `app/Filament/Widgets/AdminOverview.php`
- `app/Filament/Widgets/ApplicationClickSummary.php`
- `app/Providers/Filament/AdminPanelProvider.php`
- `resources/views/landing.blade.php`
- `resources/views/partials/application-card.blade.php`
- `resources/views/filament/widgets/admin-quick-links.blade.php`
- `resources/views/filament/widgets/application-click-summary.blade.php`
- `routes/web.php`
- `database/seeders/DatabaseSeeder.php`
- `tests/Feature/ExampleTest.php`
- `docs/ADMIN-HANDOVER-SAFA-UBP.md`
- `docs/reports/TAHAP-10-BRANDING-POLISH-OPTIONAL-ENHANCEMENTS.md`

## Detail Perubahan

### Branding/Favicon

- Menambahkan logo Fakultas Farmasi UBP sebagai aset default di `public/images/logo-fakultas-farmasi-ubp.png`.
- Menambahkan favicon default di `public/favicon.png`.
- Landing page memakai logo pada navbar dan card preview hero.
- Landing tetap memiliki fallback teks `SAFA UBP` jika logo tidak tersedia.
- Menambahkan dukungan setting key:
  - `site_logo`
  - `site_favicon`
  - `site_subtitle`

### Meta SEO/Open Graph

Landing page sekarang memiliki meta sederhana:

- `title`
- `description`
- `theme-color`
- `og:title`
- `og:description`
- `og:type`
- `og:url`
- `og:image` jika logo tersedia

Nilai memakai setting `site_name`, `site_subtitle`, dan `hero_description` dengan fallback aman.

### Tracking Klik Aplikasi

- Menambahkan tabel `application_clicks`.
- Menambahkan model `ApplicationClick`.
- Menambahkan route `/go/{portalApplication:slug}`.
- Kartu aplikasi active/internal mengarah ke route `/go/{slug}`.
- Route `/go` mencatat klik lalu redirect ke URL asli.
- Aplikasi `inactive`, `is_active=false`, maintenance, dan coming soon tidak menjadi link aktif.
- IP tidak disimpan mentah, hanya `ip_hash`.
- Klik menyimpan snapshot nama aplikasi, target URL, waktu klik, dan user agent.

### Admin Analytics

- Dashboard admin menampilkan total klik 7 hari terakhir.
- Menambahkan widget `Top Klik Aplikasi` untuk 5 aplikasi dengan klik terbanyak.
- Jika belum ada klik, widget menampilkan empty state sederhana.

### Export Klik

- Menambahkan export CSV klik aplikasi di `/admin/exports/clicks`.
- Export tetap admin-only.
- Kolom CSV:
  - `application_name`
  - `target_url`
  - `clicked_at`
  - `user_agent`
- Export tidak menampilkan IP hash.

### Handover Admin Documentation

Menambahkan `docs/ADMIN-HANDOVER-SAFA-UBP.md` yang menjelaskan:

- Cara login admin.
- Cara mengganti password.
- Cara menambah dan mengedit aplikasi.
- Cara mengatur status aplikasi.
- Cara upload thumbnail.
- Cara duplicate aplikasi.
- Cara export aplikasi/kategori/klik.
- Cara mengatur kategori, pengumuman, dan teks landing.
- Cara membaca dashboard/klik aplikasi.
- Catatan keamanan admin.
- Hal yang tidak boleh dilakukan di production.

## Validasi Keamanan

- IP pengunjung tidak disimpan mentah, hanya hash berbasis `APP_KEY`.
- Route redirect `/go/{slug}` hanya menerima aplikasi yang aktif, visible, punya URL, dan statusnya linkable.
- Redirect hanya menerima URL dengan skema `http` atau `https`.
- Export klik tetap admin-only dan tidak menampilkan IP hash.
- Admin panel tetap dilindungi user `is_admin=true`.
- Tidak ada credential production disimpan.
- Tidak ada package analytics eksternal ditambahkan.
- Tidak ada instruksi production yang memakai `migrate:fresh`.

## Hasil Testing

Command yang dijalankan:

```bash
php artisan test
```

Hasil:

```text
26 passed, 96 assertions
```

Command yang dijalankan:

```bash
npm run build
```

Hasil:

```text
vite build berhasil
```

`php artisan migrate:fresh` tidak dijalankan karena tidak diperlukan untuk tahap ini. Migration baru tervalidasi melalui test `RefreshDatabase` lokal. Jangan menjalankan `migrate:fresh` di production.

## Cara Testing Manual

1. Jalankan aplikasi lokal.
2. Buka landing page `/`.
3. Pastikan logo tampil di navbar dan hero preview.
4. Cek source HTML untuk meta description, Open Graph, favicon, dan theme-color.
5. Klik tombol aplikasi dengan status active/internal.
6. Pastikan browser membuka `/go/{slug}` lalu redirect ke URL asli.
7. Login admin melalui `/admin`.
8. Pastikan dashboard tampil dan widget klik aplikasi tersedia.
9. Klik beberapa aplikasi dari landing, lalu refresh dashboard untuk melihat data top klik.
10. Coba export klik melalui `/admin/exports/clicks`.
11. Buka `docs/ADMIN-HANDOVER-SAFA-UBP.md` dan pastikan panduan admin sesuai kebutuhan.

## Catatan Risiko

- Analytics klik butuh data berjalan, jadi dashboard bisa kosong di awal.
- Hash IP bukan identitas pengguna dan tidak dimaksudkan untuk pelacakan personal.
- User agent bisa panjang atau tidak selalu akurat.
- Jangan menyimpan credential production di repository atau dokumen.
- Jangan menjalankan `migrate:fresh` di production.
- Redirect bergantung pada URL aplikasi yang benar di admin.

## Rekomendasi Tahap Berikutnya

Jika deployment aktual sudah dilakukan:

**Real Deployment Notes**

Jika belum deploy:

**Stop development dan lanjut deploy ke VPS `safa.cloud` menggunakan deployment checklist yang sudah dibuat.**
