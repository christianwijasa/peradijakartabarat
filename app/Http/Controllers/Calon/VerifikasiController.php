<?php

namespace App\Http\Controllers\Calon;

use App\Http\Controllers\Controller;
use App\Models\CandidateAdvocate;
use App\Models\VerificationChecklist;
use App\Support\CandidateVerificationChecklist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VerifikasiController extends Controller
{
    public function index(): View
    {
        $ca = Auth::user()->candidateAdvocate;
        CandidateVerificationChecklist::seedFor($ca);
        CandidateVerificationChecklist::syncProfileItem($ca->fresh());

        $items = $ca->fresh()->checklistItems;

        return view('calon.verifikasi', [
            'ca' => $ca,
            'items' => $items,
        ]);
    }

    public function storeLink(Request $request, VerificationChecklist $verificationChecklist): RedirectResponse
    {
        $ca = Auth::user()->candidateAdvocate;

        abort_unless(
            $verificationChecklist->checkable_type === CandidateAdvocate::class
            && (int) $verificationChecklist->checkable_id === (int) $ca->id,
            403
        );

        abort_unless(CandidateVerificationChecklist::requiresUpload($verificationChecklist->label), 422);

        $validated = $request->validate([
            'document_url' => ['required', 'url', 'max:2048'],
        ]);

        $verificationChecklist->update([
            'document_url' => $validated['document_url'],
            'is_checked' => false,
        ]);

        return back()->with('status', 'Link berkas "'.$verificationChecklist->label.'" tersimpan. Menunggu review Admin DPC.');
    }
}
