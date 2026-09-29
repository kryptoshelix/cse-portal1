# Local Development Setup (XAMPP / Windows)

## Prerequisites
- XAMPP 8.2+ with **Apache + MySQL/MariaDB** started (PHP 8.2/8.3 both supported by Laravel 12).
- Composer (Windows installer or `composer.phar`).
- Git.

## Steps
1. `git clone <repo-url> cse-portal && cd cse-portal`
2. `composer install`
3. `copy .env.example .env` then `php artisan key:generate`
4. Create DB in phpMyAdmin or CLI: `CREATE DATABASE cse_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`
   Set in `.env`: `DB_CONNECTION=mysql`, `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_DATABASE=cse_portal`, `DB_USERNAME=root`, `DB_PASSWORD=` (match your XAMPP).
5. `php artisan migrate --seed`
6. Create the first super administrator (never a public route):
   `php artisan app:create-admin` (interactive: name, email, password ≥8 chars).
7. Serve: `php artisan serve` → http://127.0.0.1:8000
   (or copy into `htdocs/cse-portal` and point Apache vhost DocumentRoot at `public/`; enable `rewrite_module`.)
8. Tests need no MySQL: they run on in-memory SQLite via `php artisan test`.

Seeders create clearly-fictional demo accounts (password documented in DatabaseSeeder header): student, faculty, dept_admin — all `active` except one pending example. Demo content is marked as sample data.

## Troubleshooting
- 404 on routes under Apache: ensure DocumentRoot = `public/`, `AllowOverride All`.
- `could not find driver pdo_mysql`: enable `extension=pdo_mysql` in `php.ini`.
- Storage link errors: `php artisan storage:link` (gallery images served from `public/storage`).
- Port clash: `php artisan serve --port=8080`.
- Mail (password reset): set `MAIL_*` in `.env`; without them the reset form shows an error — this is expected locally.
