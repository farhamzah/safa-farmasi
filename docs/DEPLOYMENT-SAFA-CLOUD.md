# Deployment SAFA UBP ke safa.cloud

Dokumen ini adalah panduan aman untuk menyiapkan SAFA UBP di VPS production dengan domain `safa.cloud`. Jangan menjalankan command production dari dokumen ini di mesin lokal tanpa menyesuaikan path, user, versi PHP, dan database server.

Untuk checklist eksekusi deploy yang lebih praktis dan berurutan saat hari go-live, gunakan juga [DEPLOYMENT-EXECUTION-CHECKLIST-SAFA-CLOUD.md](DEPLOYMENT-EXECUTION-CHECKLIST-SAFA-CLOUD.md). Setelah aplikasi live, gunakan [POST-GO-LIVE-VERIFICATION-SAFA-CLOUD.md](POST-GO-LIVE-VERIFICATION-SAFA-CLOUD.md) untuk verifikasi pasca deployment.

## 1. Ringkasan Deployment

SAFA UBP adalah aplikasi Laravel 12 dengan Filament Admin Panel, MySQL/MariaDB, Blade, Tailwind CSS, dan Laravel Storage. Deployment production harus memastikan:

- Landing page publik tersedia di `https://safa.cloud`.
- Admin panel tersedia di `https://safa.cloud/admin`.
- File upload tersedia melalui `storage:link`.
- Database dan folder storage punya backup berkala.
- Tidak ada credential asli yang disimpan di repository.

## 2. Kebutuhan Server

Minimal server:

- Ubuntu LTS atau distro Linux server setara.
- Nginx.
- PHP sesuai kebutuhan Laravel 12 beserta ekstensi umum: `mbstring`, `xml`, `curl`, `zip`, `bcmath`, `mysql`, `intl`, `gd` atau `imagick`.
- Composer.
- Node.js dan npm.
- MySQL atau MariaDB.
- Certbot untuk SSL Let's Encrypt.
- Git, unzip, tar, gzip.

## 3. Persiapan Domain DNS

Di DNS provider domain:

- A record `safa.cloud` mengarah ke IP VPS.
- A record `www.safa.cloud` opsional, mengarah ke IP VPS.
- Tunggu propagasi DNS sebelum menerbitkan SSL.

Validasi:

```bash
dig safa.cloud
dig www.safa.cloud
```

## 4. Persiapan VPS

Login ke VPS dengan SSH key. Hindari password SSH jika memungkinkan.

```bash
ssh deploy@safa.cloud
```

Rekomendasi dasar:

- Gunakan user non-root untuk deploy.
- Matikan root SSH login jika memungkinkan.
- Aktifkan firewall, misalnya hanya port 22, 80, dan 443.
- Update paket server secara berkala.

## 5. Install Dependency Server

Contoh umum:

```bash
sudo apt update
sudo apt install nginx mysql-server git unzip curl tar gzip
```

Install PHP dan ekstensi sesuai versi yang dipakai server. Sesuaikan package name dengan repository PHP server.

Install Composer dari dokumentasi resmi Composer.

Install Node.js versi LTS yang kompatibel dengan Vite.

## 6. Setup Database MySQL/MariaDB

Masuk MySQL:

```bash
sudo mysql
```

Buat database dan user:

```sql
CREATE DATABASE safa_ubp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'safa_user'@'localhost' IDENTIFIED BY 'GANTI_DENGAN_PASSWORD_KUAT';
GRANT ALL PRIVILEGES ON safa_ubp.* TO 'safa_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Jangan menyimpan password production di repository.

## 7. Upload atau Clone Project

Contoh path production:

```bash
sudo mkdir -p /var/www/safa-ubp
sudo chown -R deploy:www-data /var/www/safa-ubp
cd /var/www/safa-ubp
git clone REPOSITORY_URL .
```

Jika upload manual, pastikan folder `vendor`, `node_modules`, `.env`, dan backup tidak ikut sembarang diupload kecuali memang sengaja.

## 8. Konfigurasi .env Production

Salin contoh env:

```bash
cp .env.example .env
```

Edit `.env` production:

```env
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
DB_PASSWORD=PASSWORD_DATABASE_PRODUCTION

FILESYSTEM_DISK=public

SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

Generate APP_KEY hanya di server production:

```bash
php artisan key:generate
```

Jangan commit file `.env` production.

## 9. Install Dependency Laravel

```bash
composer install --no-dev --optimize-autoloader
```

Jika server membatasi memory Composer, sesuaikan konfigurasi server, bukan credential aplikasi.

## 10. Build Asset Frontend

```bash
npm ci
npm run build
```

Folder `public/build` harus tersedia setelah build.

## 11. Jalankan Migration Production

Backup dulu sebelum migration.

```bash
php artisan migrate --force
```

Penting: jangan menjalankan `php artisan migrate:fresh` di production karena akan menghapus data.

Jika butuh seeder awal hanya saat database masih kosong:

```bash
php artisan db:seed --force
```

Setelah seeder, segera ganti password admin.

## 12. Storage Link dan Permission

Buat symbolic link storage:

```bash
php artisan storage:link
```

Pastikan permission:

```bash
sudo chown -R deploy:www-data /var/www/safa-ubp
sudo chmod -R ug+rw storage bootstrap/cache
```

Folder penting upload:

```text
storage/app/public/application-thumbnails
storage/app/public/branding
```

## 13. Konfigurasi Nginx

Contoh server block:

```nginx
server {
    listen 80;
    listen [::]:80;

    server_name safa.cloud www.safa.cloud;
    root /var/www/safa-ubp/public;

    index index.php index.html;

    client_max_body_size 10M;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~* \.(env|log|sql|sqlite|bak|backup)$ {
        deny all;
    }
}
```

Catatan: sesuaikan socket PHP-FPM dengan versi PHP yang digunakan server, misalnya `/run/php/php8.3-fpm.sock`.

Aktifkan site:

```bash
sudo ln -s /etc/nginx/sites-available/safa.cloud /etc/nginx/sites-enabled/safa.cloud
sudo nginx -t
sudo systemctl reload nginx
```

## 14. SSL Let's Encrypt

Setelah DNS mengarah ke VPS:

```bash
sudo certbot --nginx -d safa.cloud -d www.safa.cloud
```

Cek auto-renew:

```bash
sudo certbot renew --dry-run
```

Pastikan `APP_URL=https://safa.cloud` dan `SESSION_SECURE_COOKIE=true`.

## 15. Optimasi Laravel Production

Setelah deploy dan `.env` benar:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jika terjadi error setelah perubahan config:

```bash
php artisan optimize:clear
```

## 16. Setup Backup Database dan Storage

### Backup Database

Contoh backup:

```bash
mkdir -p ~/backups/safa-ubp
mysqldump -u safa_user -p safa_ubp | gzip > ~/backups/safa-ubp/safa_ubp_backup_$(date +%F).sql.gz
```

Contoh restore:

```bash
gunzip -c ~/backups/safa-ubp/safa_ubp_backup_YYYY-MM-DD.sql.gz | mysql -u safa_user -p safa_ubp
```

### Backup Storage

Backup folder public storage:

```bash
tar -czf ~/backups/safa-ubp/safa_storage_backup_$(date +%F).tar.gz -C /var/www/safa-ubp storage/app/public
```

Folder penting:

```text
storage/app/public/application-thumbnails
storage/app/public/branding
```

Catatan backup:

- Simpan backup di luar folder public.
- Jangan upload file backup ke repository.
- Atur backup berkala setelah production.
- Uji restore secara berkala di environment staging/lokal.

## 17. Cara Update Aplikasi Setelah Production

Backup dulu:

```bash
mysqldump -u safa_user -p safa_ubp | gzip > ~/backups/safa-ubp/safa_ubp_backup_$(date +%F-%H%M).sql.gz
tar -czf ~/backups/safa-ubp/safa_storage_backup_$(date +%F-%H%M).tar.gz -C /var/www/safa-ubp storage/app/public
```

Update code:

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

Jangan gunakan `migrate:fresh` di production.

## 18. Troubleshooting Umum

### 500 Error

```bash
tail -f storage/logs/laravel.log
```

Cek:

- `.env` benar.
- `APP_KEY` sudah ada.
- Permission `storage` dan `bootstrap/cache`.
- Database bisa diakses.
- Config cache sudah di-clear setelah perubahan env.

### Gambar Upload Tidak Tampil

Cek:

```bash
php artisan storage:link
ls -la public/storage
ls -la storage/app/public
```

Pastikan `APP_URL=https://safa.cloud` dan `FILESYSTEM_DISK=public`.

### Admin Tidak Bisa Login

Cek user admin:

```bash
php artisan tinker
```

```php
\App\Models\User::where('email', 'admin@safa.cloud')->first();
```

Ganti password admin jika perlu:

```php
$user = \App\Models\User::where('email', 'admin@safa.cloud')->first();
$user->password = \Illuminate\Support\Facades\Hash::make('PASSWORD_BARU_YANG_KUAT');
$user->save();
```

## 19. Checklist Go-Live

- [ ] Domain DNS `safa.cloud` sudah mengarah ke VPS production.
- [ ] `APP_ENV=production`.
- [ ] `APP_DEBUG=false`.
- [ ] `APP_URL=https://safa.cloud`.
- [ ] `APP_KEY` sudah digenerate.
- [ ] Database production sudah dibuat.
- [ ] User database memakai password kuat.
- [ ] `.env` production tidak masuk repository.
- [ ] Password akun seed `admin@safa.cloud` sudah diganti.
- [ ] `composer install --no-dev --optimize-autoloader` sudah dijalankan.
- [ ] `npm ci` dan `npm run build` sudah dijalankan.
- [ ] `php artisan migrate --force` sudah dijalankan.
- [ ] `php artisan storage:link` sudah dijalankan.
- [ ] Folder `storage` dan `bootstrap/cache` writable.
- [ ] Nginx root mengarah ke `/var/www/safa-ubp/public`.
- [ ] SSL Let's Encrypt aktif.
- [ ] `php artisan config:cache`, `route:cache`, dan `view:cache` sudah dijalankan.
- [ ] Upload thumbnail berhasil dan gambar storage tampil.
- [ ] Export CSV aplikasi dan kategori berhasil.
- [ ] Duplicate aplikasi berhasil.
- [ ] Search/filter landing berhasil.
- [ ] Backup database berjalan dan file backup tersimpan di luar folder public.
- [ ] Backup storage berjalan, termasuk `storage/app/public/application-thumbnails`.
- [ ] `migrate:fresh` tidak digunakan di production.

## Health Check Setelah Deployment

Cek:

- `https://safa.cloud`.
- `https://safa.cloud/admin/login`.
- Login admin berhasil.
- Search/filter landing berjalan.
- Upload thumbnail berhasil.
- Gambar storage tampil.
- Duplicate aplikasi berhasil.
- Export CSV aplikasi/kategori berhasil.
- SSL valid.
- `APP_DEBUG=false`.
- Log Laravel tidak menunjukkan error kritis.

## Catatan Keamanan Opsional

- Admin panel tetap di `/admin` dan wajib login.
- Gunakan password admin yang kuat dan unik.
- Pertimbangkan rate limiting login.
- Aktifkan firewall VPS.
- Matikan root SSH login jika memungkinkan.
- Gunakan SSH key.
- Update server secara berkala.
- Backup database dan storage secara berkala.
