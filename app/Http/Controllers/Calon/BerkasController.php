<?php

namespace App\Http\Controllers\Calon;

use App\Http\Controllers\Controller;
use App\Models\BerkasSumpah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function upload(Request $request, BerkasSumpah $berkas): RedirectResponse
    {
        $ca = Auth::user()->calonAdvokat;
        abort_unless($berkas->calon_advokat_id === $ca->id, 403);

        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        if ($berkas->file_path && Storage::exists($berkas->file_path)) {
            Storage::delete($berkas->file_path);
        }

        $file = $data['file'];
        $filename = $berkas->jenis.'-'.$ca->kode_ca.'-'.now()->format('YmdHis').'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('berkas', $filename);

        $berkas->update([
            'file_path' => $path,
            'status' => 'lengkap',
            'ukuran' => number_format($file->getSize() / 1024 / 1024, 1).' MB',
        ]);

        return back()->with('status', 'Berkas berhasil diunggah.');
    }

    public function download(BerkasSumpah $berkas): StreamedResponse
    {
        $ca = Auth::user()->calonAdvokat;
        abort_unless($berkas->calon_advokat_id === $ca->id, 403);
        abort_unless($berkas->file_path && Storage::exists($berkas->file_path), 404, 'File tidak ditemukan.');

        return Storage::download($berkas->file_path, basename($berkas->file_path));
    }
}
