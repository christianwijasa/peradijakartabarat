<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (! filter_var(env('DISABLE_ROUTE_CACHE', false), FILTER_VALIDATE_BOOLEAN)) {
            return;
        }

        $path = base_path('bootstrap/cache/routes-v7.php');

        if (is_file($path)) {
            @unlink($path);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureSharedHostingPublicPath();

        $rootUrl = config('app.url');

        if (is_string($rootUrl) && $rootUrl !== '') {
            $normalized = rtrim($rootUrl, '/');
            URL::forceRootUrl($normalized);

            if (str_starts_with($normalized, 'https://')) {
                URL::forceScheme('https');

                if (env('SESSION_SECURE_COOKIE') === null) {
                    config(['session.secure' => true]);
                }
            }

            $urlPath = parse_url($normalized, PHP_URL_PATH);
            if (is_string($urlPath) && $urlPath !== '' && $urlPath !== '/') {
                config(['session.path' => rtrim($urlPath, '/').'/']);
            }
        }
    }

    protected function configureSharedHostingPublicPath(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        $webroot = dirname(base_path());
        $webrootManifest = $webroot.'/build/manifest.json';

        if (is_file($webrootManifest)) {
            $this->app->usePublicPath($webroot);
            Vite::useHotFile($webroot.'/hot');

            return;
        }

        if (! is_file(public_path('build/manifest.json'))) {
            return;
        }

        // Default Laravel public path (src/public) when build was not synced to webroot.
        $this->app->usePublicPath(base_path('public'));
    }
}
