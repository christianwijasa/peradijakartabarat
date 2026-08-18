<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalonAdvokat;
use App\Models\LawFirm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerifikasiController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'calon');
        $tab = in_array($tab, ['calon', 'firm'], true) ? $tab : 'calon';

        $queueCalon = CalonAdvokat::whereIn('status_verifikasi', ['menunggu', 'perlu_perbaikan'])
            ->with(['user', 'checklistItems'])
            ->get();

        $queueFirm = LawFirm::whereIn('status_verifikasi', ['menunggu', 'perlu_perbaikan'])
            ->with('checklistItems')
            ->get();

        $stats = [
            ['value' => (string) ($queueCalon->count() + $queueFirm->count()), 'label' => 'Menunggu verifikasi'],
            ['value' => (string) CalonAdvokat::where('status_keanggotaan', 'aktif')->count(), 'label' => 'Alumni lulus UPA aktif'],
            ['value' => (string) LawFirm::where('status_verifikasi', 'terverifikasi')->count(), 'label' => 'Kantor hukum terverifikasi'],
            ['value' => (string) LawFirm::where('status_verifikasi', 'terverifikasi')->get()->sum(fn ($f) => $f->kuotaTersisa()), 'label' => 'Slot bimbingan tersedia'],
        ];

        return view('admin.verifikasi', [
            'tab' => $tab,
            'queueCalon' => $queueCalon,
            'queueFirm' => $queueFirm,
            'stats' => $stats,
        ]);
    }

    public function setujuiCalon(CalonAdvokat $calonAdvokat): RedirectResponse
    {
        $calonAdvokat->update(['status_verifikasi' => 'terverifikasi']);
        $calonAdvokat->checklistItems()->update(['is_checked' => true]);

        return back()->with('status', $calonAdvokat->user->name.' berhasil diverifikasi.');
    }

    public function tetapkanKuotaFirm(LawFirm $lawFirm): RedirectResponse
    {
        $lawFirm->update(['status_verifikasi' => 'terverifikasi', 'diverifikasi_pada' => now()]);
        $lawFirm->checklistItems()->update(['is_checked' => true]);

        return back()->with('status', 'Kuota bimbingan '.$lawFirm->nama.' ditetapkan dan kantor terverifikasi.');
    }
}
