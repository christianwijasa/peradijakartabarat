<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSiteNotInMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.site_maintenance')) {
            return $next($request);
        }

        $message = (string) config('app.site_maintenance_message');

        return response()->view('maintenance', ['message' => $message], 503);
    }
}
