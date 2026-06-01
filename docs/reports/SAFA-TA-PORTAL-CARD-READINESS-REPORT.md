# SAFA TA Portal Card Readiness Report

## Ringkasan

SAFA menyiapkan kartu portal `TA Farmasi UBP` sebagai link normal ke aplikasi TA. Kartu ini tidak memakai SSO, token URL, auto-login, atau credential di URL.

## Card

- Title: `TA Farmasi UBP`
- Short name: `TA`
- Slug: `ta-farmasi-ubp`
- URL lokal: `http://127.0.0.1:8007`
- Category: `Layanan Akademik`
- Status: `internal`
- Button: `Buka TA`
- Active: yes

## File Dibuat / Diubah

Dibuat:

- `database/seeders/TaPortalCardSeeder.php`
- `tests/Feature/TaPortalCardPreparationTest.php`
- `docs/reports/SAFA-TA-PORTAL-CARD-READINESS-REPORT.md`

Diubah:

- `database/seeders/DatabaseSeeder.php`
- `.env.example`

## Seeder

Seeder `TaPortalCardSeeder` idempotent memakai slug `ta-farmasi-ubp` dan category `layanan-akademik`.

Seeder lokal yang dijalankan:

```bash
php artisan db:seed --class=TaPortalCardSeeder
```

## Tests

Test:

- `tests/Feature/TaPortalCardPreparationTest.php`

Hasil:

- `php artisan test --filter=TaPortalCardPreparationTest`: 2 passed, 17 assertions.
- `php artisan test`: 30 passed, 126 assertions.

## Guardrails

- Kartu hanya link portal normal.
- Tidak ada token URL.
- Tidak ada auto-login.
- Tidak ada SSO.
- Tidak ada client secret/password/hash/token di URL.
- Tidak ada perubahan ke Core/TU/KP/Lab.
