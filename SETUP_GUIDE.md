# Setup Guide — Kantong Magang Advokat

Complete setup instructions for local development and testing.

## Prerequisites

- **PHP** ≥ 8.2 with extensions: `pdo`, `mbstring`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`
- **Composer** ≥ 2.0
- **Node.js** ≥ 18.x & **npm** ≥ 9.x
- **MySQL** ≥ 8.0 or **MariaDB** ≥ 10.3
- **Git**

## Initial Setup

### 1. Clone & Install Dependencies

```bash
git clone https://github.com/christianwijasa/peradijakartabarat.git
cd peradijakartabarat

# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 2. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Configure Database

Edit `.env` with your MySQL credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=peradi_jakbar
DB_USERNAME=root
DB_PASSWORD=your_password
```

Create the database:

```bash
mysql -u root -p
CREATE DATABASE peradi_jakbar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 4. Run Migrations & Seed Demo Data

```bash
# Run migrations
php artisan migrate

# Seed demo data (includes all demo accounts)
php artisan db:seed
```

### 5. Configure File Storage

```bash
# Create storage symlink for file uploads/downloads
php artisan storage:link
```

This creates `public/storage → storage/app/public` symlink, enabling:
- Berkas uploads at `storage/app/berkas/`
- Public access via `/storage/berkas/{filename}`

### 6. Build Frontend Assets

```bash
# Development build
npm run dev

# Or production build
npm run build
```

### 7. Start Development Server

```bash
php artisan serve
```

Application will be available at: **http://127.0.0.1:8000**

## Post-Setup Verification

### Check Installation

```bash
# Verify Laravel installation
php artisan about

# List routes
php artisan route:list

# Check storage permissions
ls -la storage/
```

### Test Demo Accounts

Login with any demo account (password: `password`):
- **Law Firm:** `hendra@peradijakbar.test`
- **Calon Advokat:** `andi@peradijakbar.test`
- **Admin DPC:** `admin@peradijakbar.test`

See `DEMO_ACCOUNTS.md` for full list.

## Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --filter=FirmLowonganTest
php artisan test --filter=LamaranStatusTest
php artisan test --filter=LogbookRevisiTest

# Run with coverage (requires Xdebug)
php artisan test --coverage
```

## Common Issues & Fixes

### Issue: "Symlink already exists"

```bash
# Remove existing symlink first
rm public/storage
php artisan storage:link
```

### Issue: "Class 'Storage' not found"

Ensure Laravel facades are enabled in `config/app.php`:

```php
'aliases' => [
    'Storage' => Illuminate\Support\Facades\Storage::class,
    // ...
]
```

### Issue: File upload fails with 413 error

Increase upload limits in `php.ini`:

```ini
upload_max_filesize = 20M
post_max_size = 20M
```

And in `.env`:

```env
# Optional: limit in Laravel (defaults to php.ini)
MAX_FILE_SIZE=10240  # KB
```

### Issue: Database connection error

1. Check MySQL is running: `mysql -u root -p`
2. Verify credentials in `.env`
3. Ensure database exists: `SHOW DATABASES;`
4. Clear config cache: `php artisan config:clear`

### Issue: Views not updating

```bash
# Clear view cache
php artisan view:clear

# Clear all caches
php artisan optimize:clear
```

## Development Workflow

### Making Changes

1. Create feature branch: `git checkout -b feature/your-feature`
2. Make changes
3. Run tests: `php artisan test`
4. Commit with conventional commits:
   - `feat:` for new features
   - `fix:` for bug fixes
   - `docs:` for documentation
   - `test:` for tests
   - `refactor:` for refactoring
5. Push and create PR

### Database Reset

```bash
# Fresh migration + seed
php artisan migrate:fresh --seed

# This will:
# - Drop all tables
# - Run all migrations
# - Seed demo data
```

### Code Style

Follow Laravel conventions:
- PSR-12 coding standard
- Use Laravel facades where appropriate
- Blade templates for views
- Tailwind CSS for styling
- Alpine.js for simple interactivity

## Production Deployment

### Pre-Deployment Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Generate new `APP_KEY`
- [ ] Configure database credentials
- [ ] Set up queue workers (if using queues)
- [ ] Configure mail driver (not `log`)
- [ ] Set up scheduled tasks (cron)
- [ ] Configure trusted proxies if behind load balancer

### Deployment Steps

```bash
# Update code
git pull origin main

# Install dependencies (production only)
composer install --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build assets
npm run build

# Storage link (if not persisted)
php artisan storage:link

# Set permissions
chmod -R 755 storage bootstrap/cache
```

### Web Server Configuration

#### Nginx

```nginx
server {
    listen 80;
    server_name kantongmagang.peradijakbar.or.id;
    root /var/www/peradijakartabarat/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Apache (.htaccess included)

Ensure `mod_rewrite` is enabled:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

## Monitoring & Logs

### View Logs

```bash
# Tail latest log
tail -f storage/logs/laravel.log

# View specific date
cat storage/logs/laravel-2026-09-13.log
```

### Clear Logs

```bash
# Truncate current log
> storage/logs/laravel.log

# Or use log rotation (recommended for production)
```

## Backup & Restore

### Database Backup

```bash
# Backup
php artisan backup:run  # If spatie/laravel-backup installed

# Or manually
mysqldump -u root -p peradi_jakbar > backup_$(date +%Y%m%d).sql
```

### Restore

```bash
mysql -u root -p peradi_jakbar < backup_20260913.sql
```

## Troubleshooting

### Enable Debug Mode (Development Only)

```env
APP_DEBUG=true
APP_ENV=local
```

### Clear All Caches

```bash
php artisan optimize:clear
composer dump-autoload
```

### Check Permissions

```bash
# Storage should be writable
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Support

For issues or questions:
- Check `README.md` for overview
- See `DEMO_ACCOUNTS.md` for test accounts
- Review Laravel documentation: https://laravel.com/docs/11.x
- Check application logs in `storage/logs/`
