# Shared hosting layout (same as scrb)

On the server, the web root for this app is **not** `src/public`. It looks like:

```text
peradijakartabarat/
├── index.php          ← deploy/index.php from this repo
├── .htaccess          ← deploy/.htaccess.example (fix RewriteBase)
├── build/             ← from src/public/build after npm run build
├── robots.txt         ← from src/public/robots.txt
├── storage/           ← symlink → src/storage/app/public (optional uploads)
└── src/               ← full git clone (app, bootstrap, config, vendor, …)
```

The top-level `vendor/` folder on scrb is usually **Composer package assets** published under `public/vendor/`, not PHP `vendor/`. PHP dependencies stay in `src/vendor/`.

## Deploy steps

1. Pull or clone the repo into `src/`.
2. In `src/`: `composer install --no-dev`, copy `.env`, `php artisan key:generate`, migrate, seed if needed.
3. In `src/`: `npm ci && npm run build`.
4. Build frontend assets (required — `@vite` needs `manifest.json`):

   ```bash
   cd src
   npm ci
   npm run build
   ```

   This creates `src/public/build/`. Either leave it there **or** sync to the webroot (step 5).

5. Copy to the **parent** of `src/` (deploy root):
   - `deploy/index.php` → `index.php`
   - `deploy/.htaccess.example` → `.htaccess` (edit `RewriteBase`)
   - `src/public/build/` → `build/`
   - `src/public/robots.txt` → `robots.txt`
6. `.env` in `src/`:

   ```env
   APP_URL=https://fchr.space/laravel/peradijakartabarat
   SESSION_PATH=/laravel/peradijakartabarat
   ```

7. Clear caches **in src/** (do not use `route:cache` on shared hosting until stable):

   ```bash
   cd src
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   rm -f bootstrap/cache/config.php bootstrap/cache/routes-v7.php
   ```

8. Permissions: `storage/` and `bootstrap/cache/` writable under `src/`.

### Vite manifest missing?

| Symptom | Fix |
|--------|-----|
| Looks for `src/public/build/manifest.json` | Run `npm run build` inside `src/` |
| Looks for `…/peradijakartabarat/build/manifest.json` | Copy `src/public/build` → webroot `build/` **or** remove unconditional `usePublicPath` from `index.php` |
| “Start the development server” on production | You need a production build, not `npm run dev` |

## Common mistakes (peradi vs scrb)

| Issue | Fix |
|--------|-----|
| `index.php` still uses `../vendor` (public paths) | Use `deploy/index.php` → `src/vendor` |
| Missing `usePublicPath` | Included in `deploy/index.php` |
| No `RewriteBase` in subfolder | Set in `.htaccess` |
| Stale `bootstrap/cache/routes-*.php` | Delete + `route:clear` |
| `optimize` / `route:cache` on host | Avoid until site works |
