<?php

namespace App\Http\Controllers\Calon;

use App\Http\Controllers\Controller;
use App\Models\Lowongan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LowonganController extends Controller
{
    public function index(Request $request): View
    {
        $ca = Auth::user()->calonAdvokat;

        $bidangFilter = ['Semua bidang', 'Litigasi', 'Korporasi', 'Prodeo'];
        $filter = in_array($request->query('bidang'), $bidangFilter, true) ? $request->query('bidang') : 'Semua bidang';

        $lowongans = Lowongan::query()
            ->where('status', 'aktif')
            ->whereHas('lawFirm', fn ($q) => $q->where('status_verifikasi', 'terverifikasi'))
            ->with('lawFirm')
            ->when($filter !== 'Semua bidang', fn ($q) => $q->whereJsonContains('bidang', $filter))
            ->when($request->query('q'), fn ($q, $keyword) => $q->where(function ($q) use ($keyword) {
                $q->where('judul', 'like', "%{$keyword}%")
                    ->orWhereHas('lawFirm', fn ($q) => $q->where('nama', 'like', "%{$keyword}%"));
            }))
            ->get();

        $lamaranFirmIds = $ca->lamarans()->with('lowongan')->get()->pluck('lowongan.law_firm_id')->filter()->all();

        return view('calon.lowongan', [
            'lowongans' => $lowongans,
            'bidangFilter' => $bidangFilter,
            'filter' => $filter,
            'lamaranFirmIds' => $lamaranFirmIds,
            'keyword' => $request->query('q'),
        ]);
    }

    public function lamar(Lowongan $lowongan): RedirectResponse
    {
        $ca = Auth::user()->calonAdvokat;

        if (! $ca->lamarans()->where('lowongan_id', $lowongan->id)->exists()) {
            $ca->lamarans()->create([
                'lowongan_id' => $lowongan->id,
                'status' => 'terkirim',
                'tanggal_lamar' => now(),
            ]);
        }

        return back()->with('status', 'Lamaran berhasil dikirim ke '.$lowongan->lawFirm->nama.'.');
    }
}
