<?php

namespace App\Http\Controllers\Calon;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BerkasController extends Controller
{
    public function index(): View
    {
        $ca = Auth::user()->candidateAdvocate;
        $berkas = $ca->oathDocuments;
        $audit = $ca->finalAudits()->latest()->first();
        $lulusAudit = $audit?->status === 'PASSED';
        $semuaLengkap = $berkas->every(fn ($b) => $b->status === 'COMPLETE');

        return view('calon.berkas', [
            'ca' => $ca,
            'berkas' => $berkas,
            'bisaUnduh' => $lulusAudit && $semuaLengkap,
        ]);
    }
}
