# Deployment Execution Checklist — SAFA UBP safa.cloud

Dokumen ini adalah checklist eksekusi aktual untuk deploy SAFA UBP ke VPS production dengan domain `safa.cloud`. Jangan menjalankan command di bawah tanpa menyesuaikan user server, path, versi PHP, socket PHP-FPM, credential database, dan kebijakan VPS yang digunakan.

Dokumen ini tidak menyimpan credential asli. Jangan gunakan `migrate:fresh` di production.

Setelah deployment selesai, lanjutkan verifikasi menggunakan [POST-GO-LIVE-VERIFICATION-SAFA-CLOUD.md](POST-GO-LIVE-VERIFICATION-SAFA-CLOUD.md).

## 1. Informasi Deployment

- Nama aplikasi: SAFA UBP
- Domain: `https://safa.cloud`
- Framework: Laravel 12
- Admin panel: `/admin`
- Database: MySQL/MariaDB
- Web server: Nginx
- SSL: Let's Encrypt
- Public root: `/var/www/safa-ubp/public`

## 2. Prasyarat

- [ ] Akses SSH VPS tersedia.
- [ ] Domain `safa.cloud` sudah diarahkan ke IP VPS.
- [ ] User Linux non-root tersedia.
- [ ] Git tersedia atau metode upload project tersedia.
- [ ] Database MySQL/MariaDB tersedia.
- [ ] PHP dan extension Laravel tersedia.
- [ ] Composer tersedia.
- [ ] Node.js dan npm tersedia.
- [ ] Nginx tersedia.
- [ ] Certbot tersedia.

## 3. Pre-deployment Local Checklist

Selesaikan di mesin lokal sebelum upload atau release:

- [ ] `php artisan test` berhasil.
- [ ] `npm run build` berhasil.
- [ ] Tidak ada file `.env` ikut commit.
- [ ] `.env.example` sudah production-safe.
- [ ] Password admin seed lokal tidak dianggap password production.
- [ ] Dokumentasi deployment sudah tersedia.
- [ ] Backup kode terakhir tersedia.

## 4. Persiapan Server

Login ke VPS:

```bash
ssh deploy@safa.cloud
```

Update package:

```bash
sudo apt update
sudo apt upgrade
```

Install Nginx, database, dan utilitas dasar:

```bash
sudo apt install nginx mysql-server git unzip curl tar gzip
```

Install PHP dan extension umum Laravel. Sesuaikan nama package dan versi PHP dengan server:

```bash
sudo apt install php-fpm php-cli php-mysql php-mbstring php-xml php-curl php-zip php-bcmath php-intl php-gd
```

Install Composer mengikuti dokumentasi resmi Composer:

```bash
composer --version
```

Install Node.js dan npm versi LTS sesuai kebijakan server:

```bash
node -v
npm -v
```

Install Certbot untuk Nginx:

```bash
sudo apt install certbot python3-certbot-nginx
```

Catatan:

- Jangan mengunci versi PHP terlalu spesifik sebelum mengetahui versi PHP di VPS.
- Sesuaikan socket PHP-FPM di konfigurasi Nginx dengan versi PHP server, misalnya `/run/php/phpX.X-fpm.sock`.

## 5. Setup Database Production

Masuk MySQL/MariaDB:

```bash
sudo mysql
```

Buat database dan user production:

```sql
CREATE DATABASE safa_ubp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'safa_user'@'localhost' IDENTIFIED BY 'CHANGE_THIS_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON safa_ubp.* TO 'safa_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Gunakan password kuat milik production. Jangan menyimpan password asli di repository atau dokumen.

## 6. Upload atau Clone Project

Path target production:

```bash
/var/www/safa-ubp
```

### Opsi A. Clone dari Git Repository

```bash
cd /var/www
sudo git clone REPOSITORY_URL safa-ubp
sudo chown -R deploy:www-data /var/www/safa-ubp
```

Jika repository private, pastikan SSH key deploy sudah terpasang dengan aman.

### Opsi B. Upload Manual Zip/Project

Upload archive project ke server, lalu ekstrak:

```bash
cd /var/www
sudo mkdir -p safa-ubp
sudo unzip safa-ubp-release.zip -d safa-ubp
sudo chown -R deploy:www-data /var/www/safa-ubp
```

Pastikan `.env` lokal, backup, dan file credential tidak ikut terupload.

Permission dasar:

```bash
cd /var/www/safa-ubp
sudo chown -R deploy:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## 7. Konfigurasi .env Production

Buat file `.env` production di server:

```bash
cd /var/www/safa-ubp
cp .env.example .env
nano .env
```

Contoh nilai penting:

```dotenv
APP_NAME="SAFA UBP"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://safa.cloud

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=safa_ubp
DB_USERNAME=safa_user
DB_PASSWORD=CHANGE_THIS_STRONG_PASSWORD

FILESYSTEM_DISK=public

SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

Wajib diperhatikan:

- Jangan commit `.env`.
- Generate `APP_KEY` di server.
- Gunakan password database yang kuat.
- Jangan memakai credential contoh untuk production.

## 8. Install Dependency Production

Masuk ke folder aplikasi:

```bash
cd /var/www/safa-ubp
```

Install dependency Laravel:

```bash
composer install --no-dev --optimize-autoloader
```

Install dan build asset frontend:

```bash
npm ci
npm run build
```

Jika `npm ci` gagal karena tidak ada `package-lock.json`, gunakan:

```bash
npm install
npm run build
```

## 9. Laravel Production Commands

Jalankan command Laravel production:

```bash
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Penting:

- Jangan pakai `php artisan migrate:fresh` di production.
- Backup database sebelum menjalankan migration pada update berikutnya.

## 10. Konfigurasi Nginx

Buat server block:

```bash
sudo nano /etc/nginx/sites-available/safa.cloud
```

Contoh konfigurasi:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name safa.cloud www.safa.cloud;

    root /var/www/safa-ubp/public;
    index index.php index.html;

    access_log /var/log/nginx/safa.cloud.access.log;
    error_log /var/log/nginx/safa.cloud.error.log;

    client_max_body_size 10M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/phpX.X-fpm.sock;
    }

    location ~ /\. {
        deny all;
    }

    location ~* \.(env|log|sql|sqlite|bak|backup)$ {
        deny all;
    }
}
```

Sesuaikan `phpX.X-fpm.sock` dengan versi PHP server.

Aktifkan site dan reload Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/safa.cloud /etc/nginx/sites-enabled/safa.cloud
sudo nginx -t
sudo systemctl reload nginx
```

## 11. SSL Let’s Encrypt

Jika memakai domain utama dan `www`:

```bash
sudo certbot --nginx -d safa.cloud -d www.safa.cloud
```

Jika tidak memakai `www`, cukup:

```bash
sudo certbot --nginx -d safa.cloud
```

Checklist SSL:

- [ ] `https://safa.cloud` aktif.
- [ ] HTTP redirect ke HTTPS.
- [ ] Sertifikat auto-renew aktif.

Verifikasi renew:

```bash
sudo systemctl status certbot.timer
sudo certbot renew --dry-run
```

## 12. Post-deployment Health Check

Cek melalui browser:

- [ ] `https://safa.cloud` terbuka.
- [ ] `https://safa.cloud/admin/login` terbuka.
- [ ] Login admin berhasil.
- [ ] Landing menampilkan SAFA UBP.
- [ ] Kartu aplikasi tampil.
- [ ] Search/filter berjalan.
- [ ] Duplicate aplikasi berjalan.
- [ ] Export CSV berjalan.
- [ ] Upload thumbnail berjalan.
- [ ] Gambar thumbnail tampil.
- [ ] SSL valid.
- [ ] `APP_DEBUG=false`.

## 13. Ganti Password Admin

Jika UI admin menyediakan penggantian password, login sebagai admin lalu ganti password dari admin panel.

Jika perlu memakai Tinker:

```bash
php artisan tinker
```

```php
$user = \App\Models\User::where('email', 'admin@safa.cloud')->first();
$user->password = \Illuminate\Support\Facades\Hash::make('PASSWORD_BARU_YANG_KUAT');
$user->save();
```

Penting:

- Jangan pakai password `password` di production.
- Jangan menyimpan password production di repository, chat, atau dokumen.

## 14. Backup Awal Setelah Deploy

Buat folder backup di luar public web root:

```bash
mkdir -p ~/backups/safa-ubp
```

Backup database:

```bash
mysqldump -u safa_user -p safa_ubp | gzip > ~/backups/safa-ubp/safa_ubp_backup_$(date +%F).sql.gz
```

Backup storage:

```bash
tar -czf ~/backups/safa-ubp/safa_storage_backup_$(date +%F).tar.gz -C /var/www/safa-ubp storage/app/public
```

Catatan:

- Simpan backup di luar public web root.
- Jangan upload backup ke repository.
- Backup storage harus mencakup `storage/app/public/application-thumbnails`.

## 15. Update Aplikasi Setelah Production

Checklist update aman:

- [ ] Backup database dan storage.
- [ ] Masuk ke folder aplikasi.
- [ ] Pull kode terbaru atau upload release baru.
- [ ] Install dependency production.
- [ ] Build asset frontend.
- [ ] Jalankan migration production.
- [ ] Clear dan rebuild cache Laravel.
- [ ] Cek landing dan admin.

Contoh command:

```bash
cd /var/www/safa-ubp
git pull
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jika `npm ci` tidak bisa digunakan:

```bash
npm install
npm run build
```

Penting:

- Jangan gunakan `migrate:fresh` di production.
- Selalu backup sebelum migration.

## 16. Rollback Manual Sederhana

Strategi rollback sederhana:

- Simpan release sebelumnya sebelum update.
- Buat backup database sebelum migration.
- Jika update gagal, restore kode sebelumnya.
- Jika migration merusak data, restore database dari backup terakhir.
- Jalankan ulang cache Laravel setelah restore kode.
- Reload Nginx atau PHP-FPM jika diperlukan.

Contoh langkah umum:

```bash
cd /var/www/safa-ubp
git checkout PREVIOUS_COMMIT_OR_TAG
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo systemctl reload nginx
```

Restore database hanya dilakukan jika memang diperlukan dan setelah memahami dampaknya.

## 17. Troubleshooting

### 500 Error

- Cek `storage/logs/laravel.log`.
- Pastikan `APP_DEBUG=false`, lalu baca log server secara aman.
- Jalankan `php artisan optimize:clear`.
- Pastikan `.env` production benar.

### Permission Denied Storage atau Bootstrap Cache

```bash
sudo chown -R deploy:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Gambar Thumbnail Tidak Tampil

- Jalankan `php artisan storage:link`.
- Pastikan `FILESYSTEM_DISK=public`.
- Pastikan file ada di `storage/app/public/application-thumbnails`.
- Pastikan permission storage benar.

### Admin Login Gagal

- Pastikan email admin benar.
- Reset password admin dengan Tinker jika diperlukan.
- Pastikan session dan cache sudah di-clear.

### CSRF atau Session Issue

- Pastikan `APP_URL=https://safa.cloud`.
- Pastikan SSL aktif.
- Pastikan `SESSION_SECURE_COOKIE=true`.
- Clear cache Laravel.

### Database Connection Refused

- Pastikan MySQL/MariaDB aktif.
- Cek `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD`.
- Pastikan privilege user database benar.

### Nginx 404

- Pastikan root mengarah ke `/var/www/safa-ubp/public`.
- Pastikan `try_files $uri $uri/ /index.php?$query_string;` ada.
- Jalankan `sudo nginx -t`.
- Reload Nginx.

### SSL Gagal

- Pastikan DNS sudah mengarah ke IP VPS.
- Pastikan port 80 dan 443 terbuka.
- Cek konfigurasi Nginx.
- Jalankan ulang Certbot setelah DNS valid.

### npm Build Gagal

- Pastikan Node.js dan npm tersedia.
- Coba `npm install` jika `npm ci` gagal karena lockfile.
- Cek error package Vite/Tailwind.

### Composer Install Gagal

- Pastikan Composer tersedia.
- Pastikan extension PHP Laravel sudah lengkap.
- Cek permission folder project.
- Jalankan kembali `composer install --no-dev --optimize-autoloader`.

## 18. Final Go-live Checklist

- [ ] DNS benar.
- [ ] SSL aktif.
- [ ] `APP_DEBUG=false`.
- [ ] `APP_URL=https://safa.cloud`.
- [ ] Database production aman.
- [ ] Admin password diganti.
- [ ] Storage writable.
- [ ] Backup awal selesai.
- [ ] Upload thumbnail dites.
- [ ] Export CSV dites.
- [ ] Duplicate aplikasi dites.
- [ ] Landing dites.
- [ ] Admin dites.
- [ ] Dokumentasi tersimpan.
