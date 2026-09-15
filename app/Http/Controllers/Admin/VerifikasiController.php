<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CandidateAdvocate;
use App\Models\LawFirm;
use App\Support\CandidateVerificationChecklist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerifikasiController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'calon');
        $tab = in_array($tab, ['calon', 'firm'], true) ? $tab : 'calon';

        $queueCalon = CandidateAdvocate::whereIn('verification_status', ['PENDING', 'NEEDS_CORRECTION'])
            ->with(['user', 'checklistItems'])
            ->get()
            ->each(fn (CandidateAdvocate $ca) => CandidateVerificationChecklist::syncStandardItems($ca));

        $queueFirm = LawFirm::whereIn('verification_status', ['PENDING', 'NEEDS_CORRECTION'])
            ->with('checklistItems')
            ->get();

        $stats = [
            ['value' => (string) ($queueCalon->count() + $queueFirm->count()), 'label' => 'Menunggu verifikasi'],
            ['value' => (string) CandidateAdvocate::where('membership_status', 'ACTIVE')->count(), 'label' => 'Alumni lulus UPA aktif'],
            ['value' => (string) LawFirm::where('verification_status', 'VERIFIED')->count(), 'label' => 'Kantor hukum terverifikasi'],
            ['value' => (string) LawFirm::where('verification_status', 'VERIFIED')->get()->sum(fn ($f) => $f->kuotaTersisa()), 'label' => 'Slot bimbingan tersedia'],
        ];

        return view('admin.verifikasi', [
            'tab' => $tab,
            'queueCalon' => $queueCalon,
            'queueFirm' => $queueFirm,
            'stats' => $stats,
        ]);
    }

    public function setujuiCalon(CandidateAdvocate $candidateAdvocate): RedirectResponse
    {
        $candidateAdvocate->update(['verification_status' => 'VERIFIED']);
        $candidateAdvocate->checklistItems()->update(['is_checked' => true]);

        return back()->with('status', $candidateAdvocate->user->name.' berhasil diverifikasi.');
    }

    public function tetapkanKuotaFirm(LawFirm $lawFirm): RedirectResponse
    {
        $lawFirm->update(['verification_status' => 'VERIFIED', 'verified_at' => now()]);
        $lawFirm->checklistItems()->update(['is_checked' => true]);

        return back()->with('status', 'Kuota bimbingan '.$lawFirm->name.' ditetapkan dan kantor terverifikasi.');
    }
}
