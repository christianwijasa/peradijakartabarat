<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        return match (auth()->user()->role) {
            'calon_advokat' => redirect()->route('calon.dashboard'),
            'law_firm' => redirect()->route('firm.dashboard'),
            'admin_dpc' => redirect()->route('admin.verifikasi'),
            default => redirect('/'),
        };
    }
}
