<?php

namespace App\Http\Controllers\Calon;

use App\Http\Controllers\Controller;
use App\Models\CandidateAdvocate;
use App\Models\VerificationChecklist;
use App\Support\CandidateVerificationChecklist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

    public function upload(Request $request, VerificationChecklist $verificationChecklist): RedirectResponse
    {
        $ca = Auth::user()->candidateAdvocate;

        abort_unless(
            $verificationChecklist->checkable_type === CandidateAdvocate::class
            && (int) $verificationChecklist->checkable_id === (int) $ca->id,
            403
        );

        $uploadLabels = collect(CandidateVerificationChecklist::defaultItems())
            ->filter(fn ($i) => $i['requires_upload'])
            ->pluck('label')
            ->all();

        abort_unless(in_array($verificationChecklist->label, $uploadLabels, true), 422);

        $validated = $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $file = $validated['document'];
        $directory = 'verification/'.$ca->id;

        if ($verificationChecklist->file_path) {
            Storage::disk('public')->delete($verificationChecklist->file_path);
        }

        $path = $file->store($directory, 'public');
        $sizeKb = (int) ceil($file->getSize() / 1024);
        $sizeLabel = $sizeKb >= 1024
            ? number_format($sizeKb / 1024, 1, ',', '.').' MB'
            : number_format($sizeKb, 0, ',', '.').' KB';

        $verificationChecklist->update([
            'file_path' => $path,
            'file_size_label' => $sizeLabel,
            'is_checked' => false,
        ]);

        return back()->with('status', 'Berkas "'.$verificationChecklist->label.'" berhasil diunggah. Menunggu review Admin DPC.');
    }
}
