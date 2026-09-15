<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CandidateAdvocate;
use App\Models\LawFirm;
use App\Models\VerificationChecklist;
use App\Support\CandidateVerificationChecklist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerifikasiController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'candidate');
        if ($tab === 'calon') {
            $tab = 'candidate';
        }
        $tab = in_array($tab, ['candidate', 'firm'], true) ? $tab : 'candidate';

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

        return view('admin.verification', [
            'tab' => $tab,
            'queueCalon' => $queueCalon,
            'queueFirm' => $queueFirm,
            'stats' => $stats,
        ]);
    }

    public function setujuiCalon(CandidateAdvocate $candidateAdvocate): RedirectResponse
    {
        CandidateVerificationChecklist::syncStandardItems($candidateAdvocate);

        abort_unless(
            CandidateVerificationChecklist::adminQueueStatus($candidateAdvocate) === 'siap',
            422,
            'Semua berkas harus disetujui terlebih dahulu.'
        );

        $candidateAdvocate->update(['verification_status' => 'VERIFIED']);

        return back()->with('status', $candidateAdvocate->user->name.' berhasil diverifikasi.');
    }

    public function setujuiChecklistItem(VerificationChecklist $verificationChecklist): RedirectResponse
    {
        $this->assertCandidateChecklistItem($verificationChecklist);

        $verificationChecklist->update([
            'is_checked' => true,
            'admin_note' => null,
        ]);

        $ca = $verificationChecklist->checkable;
        if ($ca instanceof CandidateAdvocate && $ca->verification_status === 'NEEDS_CORRECTION') {
            $ca->update(['verification_status' => 'PENDING']);
        }

        return back()->with('status', 'Berkas "'.$verificationChecklist->label.'" disetujui.');
    }

    public function tolakChecklistItem(Request $request, VerificationChecklist $verificationChecklist): RedirectResponse
    {
        $this->assertCandidateChecklistItem($verificationChecklist);

        $data = $request->validate([
            'admin_note' => ['required', 'string', 'max:500'],
        ]);

        $verificationChecklist->update([
            'is_checked' => false,
            'admin_note' => $data['admin_note'],
        ]);

        $ca = $verificationChecklist->checkable;
        if ($ca instanceof CandidateAdvocate) {
            $ca->update(['verification_status' => 'NEEDS_CORRECTION']);
        }

        return back()->with('status', 'Berkas "'.$verificationChecklist->label.'" perlu diperbaiki calon advokat.');
    }

    private function assertCandidateChecklistItem(VerificationChecklist $verificationChecklist): void
    {
        abort_unless($verificationChecklist->checkable_type === CandidateAdvocate::class, 404);

        $ca = $verificationChecklist->checkable;
        abort_unless(
            $ca instanceof CandidateAdvocate
            && in_array($ca->verification_status, ['PENDING', 'NEEDS_CORRECTION'], true),
            403
        );
    }

    public function tetapkanKuotaFirm(LawFirm $lawFirm): RedirectResponse
    {
        $lawFirm->update(['verification_status' => 'VERIFIED', 'verified_at' => now()]);
        $lawFirm->checklistItems()->update(['is_checked' => true]);

        return back()->with('status', 'Kuota bimbingan '.$lawFirm->name.' ditetapkan dan kantor terverifikasi.');
    }
}
