# SAFA - Lab Card Smoke Report

## Ringkasan

SAFA card `Lab Farmasi UBP` tetap diperlakukan sebagai portal link biasa menuju Lab. LAB-17 tidak menambahkan auth bridge, SSO, auto-login, atau token URL.

## Smoke Criteria

- Card Lab tersedia dari seeder LAB-15.
- Target URL adalah URL Lab biasa.
- Tidak ada query token.
- Tidak ada password/secret pada URL.
- Tidak ada auto-login.

## Guardrails

- SAFA hanya portal.
- Core tetap sumber app access.
- Lab tetap memeriksa access via Core read-only adapter.
