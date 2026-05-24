# SAFA - Lab Portal Card Preparation Report

## Ringkasan

SAFA UBP menyiapkan portal card `Lab Farmasi UBP` sebagai link menuju aplikasi Lab. Card ini hanya portal link dan bukan mekanisme auth.

## Card Lab

- Title: `Lab Farmasi UBP`
- Short name: `LAB`
- Subtitle: `Sistem Operasional Laboratorium`
- Description: absensi QR, logbook alat, stok bahan/reagen, SOP/SDS/K3, maintenance/kalibrasi, dashboard, dan laporan.
- Target URL lokal: `http://127.0.0.1:8006/dashboard`
- Suggested scan URL: `http://127.0.0.1:8006/scan`
- Category: `Laboratorium`
- Icon: `flask-conical`
- Status: internal/development sesuai pola SAFA

Seeder:

- `database/seeders/LabPortalCardSeeder.php`

Seeder dipanggil dari:

- `database/seeders/DatabaseSeeder.php`

Seeder bersifat idempotent dan aman dijalankan ulang.

## Cara Mengubah URL Nanti

URL production/staging sebaiknya diubah melalui environment/config sesuai deployment:

```env
LAB_FARMASI_DASHBOARD_URL=https://lab.example.ac.id/dashboard
```

Jangan menaruh token login, password, atau secret pada URL.

## Hasil Validasi

- `php artisan db:seed --class=LabPortalCardSeeder`: OK.
- `php artisan test`: 28 passed / 109 assertions.
- PHP lint file penting: OK.

## Guardrails

- Card hanya link portal.
- Tidak ada token URL.
- Tidak ada login bypass.
- Tidak ada SSO.
- Tidak ada auto-login.
- Tidak ada secret.
- Tidak memberi app access otomatis.
