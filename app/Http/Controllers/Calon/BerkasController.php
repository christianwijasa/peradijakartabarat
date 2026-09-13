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

    public function downloadPackage(): StreamedResponse
    {
        $ca = Auth::user()->calonAdvokat;
        $audit = $ca->auditAkhirs()->latest()->first();
        
        abort_unless($audit && $audit->status === 'lulus_audit', 403, 'Paket berkas belum tersedia. Tunggu audit akhir lulus.');
        
        $berkas = $ca->berkasSumpahs;
        $requiredDocs = ['sertifikat_pkpa', 'sertifikat_lulus_upa', 'ijazah_transkrip'];
        
        foreach ($requiredDocs as $jenis) {
            $doc = $berkas->firstWhere('jenis', $jenis);
            abort_unless($doc && $doc->status === 'lengkap' && $doc->file_path, 403, 'Berkas wajib belum lengkap.');
        }

        $zipPath = storage_path('app/temp/paket-sumpah-'.$ca->kode_ca.'-'.now()->format('YmdHis').'.zip');
        
        if (! is_dir(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) !== true) {
            abort(500, 'Gagal membuat paket ZIP.');
        }

        foreach ($berkas->where('status', 'lengkap')->where('file_path', '!=', null) as $b) {
            if (Storage::exists($b->file_path)) {
                $extension = pathinfo($b->file_path, PATHINFO_EXTENSION);
                $filename = $b->label().'.'.$extension;
                $zip->addFile(Storage::path($b->file_path), $filename);
            }
        }

        $this->generateRekapLogbookPdf($ca, $zip);
        $this->generateDaftarIsiPdf($ca, $berkas, $zip);

        $zip->close();

        return response()->download($zipPath, 'Paket-Berkas-Sumpah-'.$ca->kode_ca.'.zip')->deleteFileAfterSend(true);
    }

    private function generateRekapLogbookPdf($ca, $zip): void
    {
        $rekaps = $ca->logbookRekapBulanans()->where('status', 'ditandatangani')->orderBy('tahun')->orderBy('bulan')->get();
        $entries = $ca->logbookEntries()->where('status', 'disetujui')->orderBy('tanggal')->get();

        $content = "REKAP LOGBOOK MAGANG 24 BULAN\n\n";
        $content .= "Nama: ".$ca->user->name."\n";
        $content .= "Kode CA: ".$ca->kode_ca."\n";
        $content .= "Periode: ".$ca->tanggal_mulai_magang?->format('d/m/Y')." - ".$ca->estimasiSelesai()?->format('d/m/Y')."\n";
        $content .= "Kantor Hukum: ".($ca->lawFirm?->nama ?? '-')."\n\n";
        $content .= str_repeat("=", 80)."\n\n";

        foreach ($rekaps as $rekap) {
            $content .= strtoupper(\Carbon\Carbon::create($rekap->tahun, $rekap->bulan)->translatedFormat('F Y'))."\n";
            $content .= "Ditandatangani: ".$rekap->tanggal_ttd?->translatedFormat('d F Y')."\n\n";
            
            $monthEntries = $entries->filter(fn ($e) => $e->tanggal->year == $rekap->tahun && $e->tanggal->month == $rekap->bulan);
            foreach ($monthEntries as $e) {
                $content .= $e->tanggal->format('d/m/Y')." | ".$e->jenis_kegiatan." | ".$e->jam." jam\n";
                $content .= $e->uraian."\n\n";
            }
            $content .= str_repeat("-", 80)."\n\n";
        }

        $content .= "\nTotal Jam: ".$entries->sum('jam')." jam\n";
        $content .= "Total Entri: ".$entries->count()." entri\n";

        $tempPath = storage_path('app/temp/rekap-logbook-'.$ca->kode_ca.'.txt');
        file_put_contents($tempPath, $content);
        $zip->addFile($tempPath, 'Rekap Logbook 24 Bulan.txt');
    }

    private function generateDaftarIsiPdf($ca, $berkas, $zip): void
    {
        $content = "DAFTAR ISI PAKET BERKAS SUMPAH ADVOKAT\n\n";
        $content .= "Nama: ".$ca->user->name."\n";
        $content .= "Kode CA: ".$ca->kode_ca."\n";
        $content .= "Tanggal Paket: ".now()->translatedFormat('d F Y')."\n\n";
        $content .= str_repeat("=", 80)."\n\n";

        $i = 1;
        foreach ($berkas->where('status', 'lengkap') as $b) {
            $content .= $i.". ".$b->label()."\n";
            $content .= "   Status: ".$b->status." | Ukuran: ".($b->ukuran ?? '-')."\n\n";
            $i++;
        }

        $content .= "\nPaket ini telah melalui audit akhir DPC PERADI Jakarta Barat.\n";
        $content .= "Status Audit: Lulus Audit\n";

        $audit = $ca->auditAkhirs()->latest()->first();
        if ($audit) {
            $content .= "Tanggal Audit: ".$audit->tanggal_audit?->translatedFormat('d F Y')."\n";
            $content .= "Catatan: ".$audit->catatan."\n";
        }

        $tempPath = storage_path('app/temp/daftar-isi-'.$ca->kode_ca.'.txt');
        file_put_contents($tempPath, $content);
        $zip->addFile($tempPath, 'DAFTAR ISI.txt');
    }
}
}
