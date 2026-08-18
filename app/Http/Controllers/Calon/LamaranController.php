<?php

namespace App\Http\Controllers\Calon;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LamaranController extends Controller
{
    public function index(): View
    {
        $lamarans = Auth::user()->calonAdvokat
            ->lamarans()
            ->with(['lowongan.lawFirm'])
            ->latest('tanggal_lamar')
            ->get();

        return view('calon.lamaran', ['lamarans' => $lamarans]);
    }
}
