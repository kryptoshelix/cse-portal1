# Production Deployment

Generic guidance; provider-independent.

## Requirements
- PHP 8.2+/8.3 with pdo_mysql, mbstring, openssl, gd, fileinfo, zip.
- MySQL 8 / MariaDB 10.6+.
- Nginx or Apache with vhost DocumentRoot → `/app/public`. HTTPS mandatory.
- Cron: `* * * * * php /app/artisan schedule:run` (session pruning etc.).
- Queue worker optional (`QUEUE_CONNECTION=database` + supervisor) — default sync is fine at this scale.

## Steps
1. Clone to `/var/www/cse-portal`; `composer install --no-dev --optimize-autoloader`.
2. `.env`: `APP_ENV=production`, `APP_DEBUG=false`, unique `APP_KEY` (`php artisan key:generate --show`), `APP_URL=https://…`.
3. DB: dedicated least-privilege user (SELECT/INSERT/UPDATE/DELETE only; no FILE, no DROP for the app user; separate admin used for migrations). `php artisan migrate --force` (NO --seed in production).
4. `php artisan config:cache route:cache view:cache`.
5. `php artisan storage:link`; web-server user must own `storage/` and write only there.
6. Create first super admin via `php artisan app:create-admin` (SSH).
7. Cookies/HTTPS: `SESSION_SECURE_COOKIE=true`, `SESSION_DRIVER=database`, `FILESYSTEM_DISK=public`, force HTTPS in web server + `URL::forceScheme('https')` in AppServiceProvider when `APP_ENV=production`.
8. Backups: nightly `mysqldump` + `storage/` tarball, off-server, retention policy; restore procedure tested quarterly.
9. Log rotation for `storage/logs`; monitor 5xx rate.

## Secrets policy
Never commit `.env`. Rotate `APP_KEY` breaks sessions. Password-reset tokens and remember tokens never logged.

## Post-deploy checklist
Register page creates pending accounts only; `/admin` unreachable for students (spot-check with curl); homepage stats match seeded-empty DB (zeros, not fake numbers).
