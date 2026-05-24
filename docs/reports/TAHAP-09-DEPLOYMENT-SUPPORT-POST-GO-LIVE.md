# Tahap 9 — Deployment Support & Post-Go-Live Verification

## Ringkasan

Tahap ini membuat dokumen support deployment dan checklist pasca go-live untuk SAFA UBP di domain `https://safa.cloud`. Dokumen ini disiapkan untuk membantu proses pengecekan setelah deployment aktual dilakukan di VPS.

Tidak ada deployment sungguhan yang dilakukan. Tidak ada command production dijalankan. Tidak ada credential asli disimpan.

## File Dibuat/Diubah

- `docs/POST-GO-LIVE-VERIFICATION-SAFA-CLOUD.md`
  - File baru berisi checklist verifikasi sebelum dan setelah go-live, troubleshooting, dan format berita acara.
- `docs/DEPLOYMENT-SAFA-CLOUD.md`
  - Menambahkan rujukan ke dokumen post-go-live verification.
- `docs/DEPLOYMENT-EXECUTION-CHECKLIST-SAFA-CLOUD.md`
  - Menambahkan rujukan lanjutan ke dokumen post-go-live verification.
- `docs/reports/TAHAP-09-DEPLOYMENT-SUPPORT-POST-GO-LIVE.md`
  - Report tahap 9.

## Isi Dokumen Post-Go-Live

Dokumen `docs/POST-GO-LIVE-VERIFICATION-SAFA-CLOUD.md` memuat:

- Pre-go-live checklist untuk DNS, SSL, `.env`, database, admin password, storage, backup, Nginx, dan larangan `migrate:fresh`.
- Post-go-live checklist untuk landing, admin login, kartu aplikasi, search/filter, empty state, external link, status aplikasi, announcement, kontak, dan footer.
- Admin verification untuk dashboard, quick links, tambah/edit aplikasi, duplicate, upload thumbnail, export CSV, kategori, pengumuman, landing settings, dan akses non-admin.
- Storage verification untuk `storage:link`, path thumbnail, akses browser, fallback icon, permission, dan folder backup.
- Security verification untuk `APP_DEBUG=false`, `.env`, admin login, export admin-only, route publik, SSL, dan backup.
- Backup verification untuk backup database, backup storage, lokasi backup, restore procedure, dan jadwal backup.
- Update production verification untuk alur update aman dengan backup, dependency install, build asset, migration force, cache Laravel, dan cek ulang `/` serta `/admin`.
- Troubleshooting pasca go-live untuk error umum beserta gejala, penyebab, langkah pengecekan, dan solusi aman.
- Format berita acara verifikasi sederhana untuk keputusan layak go-live atau perlu perbaikan.

## Validasi Keamanan

Dokumen menekankan:

- `APP_DEBUG=false` wajib di production.
- `.env` tidak boleh publik dan tidak boleh masuk repository.
- Password admin seed wajib diganti sebelum production dipakai.
- Export CSV tetap admin-only.
- Backup harus disimpan di luar public web root.
- Jangan memakai `migrate:fresh` di production.
- SSL harus valid.
- File backup tidak boleh berada di public directory.
- Route publik tidak boleh digunakan untuk create/update/delete data.

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

Gunakan `docs/POST-GO-LIVE-VERIFICATION-SAFA-CLOUD.md` saat dan setelah deployment aktual ke VPS:

1. Buka dokumen setelah deployment execution selesai.
2. Ikuti checklist sebelum go-live untuk memastikan server aman.
3. Jalankan pengecekan browser pada landing dan admin.
4. Uji fungsi admin penting seperti duplicate, export, upload thumbnail, dan settings.
5. Cek backup database dan storage.
6. Gunakan bagian troubleshooting jika ada masalah.
7. Isi format berita acara verifikasi sebagai catatan final.

## Catatan Risiko

- DNS propagation bisa membuat domain belum langsung mengarah ke VPS.
- SSL bisa gagal jika DNS atau port 80/443 belum benar.
- Permission `storage` dan `bootstrap/cache` yang salah bisa membuat upload, cache, session, atau log gagal.
- Credential database yang salah membuat aplikasi gagal koneksi.
- Backup yang tidak valid menyulitkan rollback.
- `storage:link` yang belum aktif membuat thumbnail tidak tampil.
- Cache config lama bisa membuat `.env` terbaru tidak terbaca.
- Password admin default adalah risiko tinggi jika tidak diganti.
- Human error saat menjalankan command production bisa merusak data, terutama jika salah memakai command migration.

## Rekomendasi Tahap Berikutnya

Rekomendasi tahap berikutnya:

**Tahap 10 — Optional Enhancements**

Jika deployment aktual sudah dilakukan, alternatifnya:

**Tahap 10 — Real Deployment Notes**

Tahap tersebut dapat mencatat hasil deploy nyata, domain/SSL final, hasil smoke test, akun admin yang sudah diganti, status backup, serta isu yang muncul setelah live.
