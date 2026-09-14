<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        $rootUrl = config('app.url');

        if (is_string($rootUrl) && $rootUrl !== '') {
            $normalized = rtrim($rootUrl, '/');
            URL::forceRootUrl($normalized);

            $urlPath = parse_url($normalized, PHP_URL_PATH);
            if (is_string($urlPath) && $urlPath !== '' && $urlPath !== '/') {
                $cookiePath = rtrim($urlPath, '/').'/';
                if (config('session.path') === '/') {
                    config(['session.path' => $cookiePath]);
                }
            }
        }
    }
}
