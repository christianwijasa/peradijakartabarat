<?php

namespace App\Http\Controllers\Calon;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BerkasController extends Controller
{
    public function index(): View
    {
        $ca = Auth::user()->calonAdvokat;
        $berkas = $ca->berkasSumpahs;
        $audit = $ca->auditAkhirs()->latest()->first();
        $lulusAudit = $audit?->status === 'lulus_audit';
        $semuaLengkap = $berkas->every(fn ($b) => $b->status === 'lengkap');

        return view('calon.berkas', [
            'ca' => $ca,
            'berkas' => $berkas,
            'bisaUnduh' => $lulusAudit && $semuaLengkap,
        ]);
    }
}
