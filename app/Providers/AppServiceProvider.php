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
        //
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
