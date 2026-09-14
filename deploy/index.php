<?php

/**
 * Shared-hosting front controller (fchr.space layout).
 *
 * Place this file next to src/ (same level as scrb: index.php + src/ + build/).
 * Do not use public/index.php paths here — they assume vendor lives beside public/.
 */

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/src/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/src/vendor/autoload.php';

// Stale route:cache on shared hosting can break "/" (GET → MethodNotAllowed, HEAD only).
foreach (glob(__DIR__.'/src/bootstrap/cache/routes-v7.php') ?: [] as $cachedRoutes) {
    @unlink($cachedRoutes);
}

$app = require_once __DIR__.'/src/bootstrap/app.php';

// Vite output: either webroot/build/ (after sync-webroot.sh) or src/public/build/ (npm run build in src).
if (is_file(__DIR__.'/build/manifest.json')) {
    $app->usePublicPath(__DIR__);
}

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
