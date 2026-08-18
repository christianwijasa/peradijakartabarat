<?php

namespace App\Http\Controllers\Firm;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PelamarController extends Controller
{
    public function index(): View
    {
        $pendamping = Auth::user()->advokatPendamping;
        $firm = $pendamping->lawFirm;

        $lamarans = Lamaran::whereHas('lowongan', fn ($q) => $q->where('law_firm_id', $firm->id))
            ->where('status', '!=', 'tidak_lanjut')
            ->with(['calonAdvokat.user', 'lowongan'])
            ->orderByDesc('tanggal_lamar')
            ->get();

        return view('firm.pelamar', ['lamarans' => $lamarans, 'firm' => $firm]);
    }

    public function terima(Lamaran $lamaran): RedirectResponse
    {
        $pendamping = Auth::user()->advokatPendamping;
        abort_unless($lamaran->lowongan->law_firm_id === $pendamping->law_firm_id, 403);

        $lamaran->update(['status' => 'diterima']);

        $ca = $lamaran->calonAdvokat;
        $ca->update([
            'law_firm_id' => $pendamping->law_firm_id,
            'advokat_pendamping_id' => $ca->advokat_pendamping_id ?? $pendamping->id,
            'tanggal_mulai_magang' => $ca->tanggal_mulai_magang ?? now(),
            'bidang_penempatan' => $ca->bidang_penempatan ?? $lamaran->lowongan->judul,
        ]);

        return back()->with('status', 'Surat penerimaan magang diterbitkan untuk '.$ca->user->name.'.');
    }
}
