# Panduan Admin SAFA UBP

Dokumen ini membantu admin Fakultas/TU mengelola konten SAFA UBP setelah aplikasi berjalan. Jangan menyimpan password production di dokumen ini.

## Cara Login Admin

1. Buka `/admin` atau `https://safa.cloud/admin`.
2. Masukkan email dan password admin.
3. Pastikan akun yang digunakan adalah akun resmi dengan akses admin.

## Cara Mengganti Password

Jika tersedia dari UI admin, gunakan menu akun/profil untuk mengganti password.

Jika perlu melalui server, minta pengelola teknis menjalankan reset password dengan `php artisan tinker`. Jangan memakai password default seperti `password` di production.

## Cara Menambah Aplikasi

1. Buka menu `Portal Applications`.
2. Klik `Create`.
3. Isi nama, slug, deskripsi singkat, status, URL jika status aktif/internal, label tombol, kategori, dan thumbnail jika ada.
4. Simpan.
5. Cek landing page untuk memastikan kartu tampil.

## Cara Mengedit Aplikasi

1. Buka menu `Portal Applications`.
2. Pilih aplikasi.
3. Edit data yang diperlukan.
4. Simpan dan cek kembali landing page.

## Cara Mengatur Status Aplikasi

Status yang tersedia:

- `Aktif`: tampil dan tombol bisa diklik.
- `Internal`: tampil dan tombol bisa diklik.
- `Maintenance`: tampil, tetapi tombol nonaktif.
- `Segera Hadir`: tampil, tetapi tombol nonaktif.
- `Nonaktif`: tidak tampil di landing page.

Gunakan status `Maintenance` untuk layanan yang sedang diperbaiki, dan `Nonaktif` untuk menyembunyikan aplikasi dari publik.

## Cara Upload Thumbnail

1. Buka form aplikasi.
2. Upload file gambar JPG, JPEG, PNG, atau WEBP.
3. Gunakan ukuran file maksimal sesuai validasi admin.
4. Simpan.
5. Cek landing page untuk memastikan gambar tampil.

Jika thumbnail hilang atau kosong, landing page akan memakai fallback icon.

## Cara Duplicate Aplikasi

1. Buka daftar aplikasi.
2. Pilih action duplicate pada aplikasi yang ingin disalin.
3. Sistem membuat salinan dengan slug unik.
4. Edit nama, URL, status, kategori, dan urutan tampilan jika perlu.

## Cara Export Aplikasi/Kategori

1. Buka dashboard admin.
2. Gunakan quick link export jika tersedia, atau buka route export admin.
3. Download CSV aplikasi atau kategori.
4. Simpan file CSV secara aman karena berisi data internal portal.

## Cara Mengatur Kategori

1. Buka menu `App Categories`.
2. Tambah atau edit kategori.
3. Atur slug, deskripsi, sort order, dan status aktif.
4. Kategori nonaktif tidak tampil di filter landing page.

## Cara Mengatur Pengumuman

1. Buka menu `Announcements`.
2. Tambah judul, isi pesan, tipe, tanggal mulai, tanggal selesai, dan status aktif.
3. Pengumuman hanya tampil jika aktif dan masih dalam rentang tanggal valid.

## Cara Mengubah Teks Landing Page

1. Buka menu `Landing Settings`.
2. Edit key seperti:
   - `site_name`
   - `site_subtitle`
   - `site_logo`
   - `site_favicon`
   - `hero_title`
   - `hero_description`
   - `contact_email`
   - `contact_whatsapp`
   - `footer_text`
3. Jika value kosong, landing page memakai fallback bawaan.

Logo default berada di `/public/images/logo-fakultas-farmasi-ubp.png`. Favicon default berada di `/public/favicon.png`.

## Cara Membaca Dashboard/Klik Aplikasi

Dashboard admin menampilkan ringkasan jumlah aplikasi, kategori, pengumuman, dan total klik 7 hari terakhir.

Widget `Top Klik Aplikasi` menampilkan 5 aplikasi yang paling sering dibuka dari landing page. Data akan muncul setelah pengunjung mengklik tombol aplikasi aktif/internal.

Export klik aplikasi tersedia sebagai CSV dan tidak menampilkan IP hash.

## Catatan Keamanan Admin

- Gunakan password kuat dan unik.
- Jangan membagikan akun admin.
- Jangan memakai password seed/default di production.
- Jangan upload file selain gambar yang valid.
- Jangan menyimpan file backup di folder public.
- Jangan membagikan CSV export ke pihak yang tidak berwenang.
- Pastikan hanya user `is_admin=true` yang bisa mengakses admin panel.

## Hal yang Tidak Boleh Dilakukan di Production

- Jangan menjalankan `php artisan migrate:fresh`.
- Jangan commit atau upload `.env` production ke repository.
- Jangan menyimpan credential asli di dokumentasi.
- Jangan menghapus data aplikasi/kategori tanpa backup.
- Jangan mematikan SSL.
- Jangan mengubah permission server tanpa memahami dampaknya.
