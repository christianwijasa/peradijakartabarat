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

    public function updateStatus(Request $request, Lamaran $lamaran): RedirectResponse
    {
        $pendamping = Auth::user()->advokatPendamping;
        abort_unless($lamaran->lowongan->law_firm_id === $pendamping->law_firm_id, 403);

        $data = $request->validate([
            'status' => ['required', 'in:review_cv,interview,diterima,tidak_lanjut'],
        ]);

        $newStatus = $data['status'];

        if ($newStatus === 'diterima') {
            if ($lamaran->lowongan->slotTersisa() <= 0) {
                return back()->withErrors(['error' => 'Kuota lowongan telah penuh. Tidak dapat menerima pelamar lagi.']);
            }

            $ca = $lamaran->calonAdvokat;
            $ca->update([
                'law_firm_id' => $pendamping->law_firm_id,
                'advokat_pendamping_id' => $ca->advokat_pendamping_id ?? $pendamping->id,
                'tanggal_mulai_magang' => $ca->tanggal_mulai_magang ?? now(),
                'bidang_penempatan' => $ca->bidang_penempatan ?? $lamaran->lowongan->judul,
            ]);

            $this->generateAcceptanceLetter($lamaran);
        }

        $lamaran->update(['status' => $newStatus]);

        $messages = [
            'review_cv' => 'Lamaran dipindahkan ke tahap review CV.',
            'interview' => 'Lamaran dipindahkan ke tahap interview.',
            'diterima' => 'Surat penerimaan magang diterbitkan untuk '.$lamaran->calonAdvokat->user->name.'.',
            'tidak_lanjut' => 'Lamaran ditandai tidak dilanjutkan.',
        ];

        return back()->with('status', $messages[$newStatus]);
    }

    private function generateAcceptanceLetter(Lamaran $lamaran): void
    {
        $ca = $lamaran->calonAdvokat;
        $firm = $lamaran->lowongan->lawFirm;

        $filename = 'surat-penerimaan-'.$ca->kode_ca.'-'.now()->format('YmdHis').'.txt';
        $path = 'berkas/'.$filename;

        $content = "SURAT PENERIMAAN MAGANG ADVOKAT\n\n";
        $content .= "Nomor: ".$firm->sk_kemenkumham."/".now()->format('Y')."\n";
        $content .= "Tanggal: ".now()->translatedFormat('d F Y')."\n\n";
        $content .= "Kepada Yth.\n";
        $content .= $ca->user->name."\n";
        $content .= "Calon Advokat\n\n";
        $content .= "Dengan hormat,\n\n";
        $content .= "Berdasarkan hasil seleksi dan wawancara, dengan ini kami dari ".$firm->nama." ";
        $content .= "menyatakan menerima Saudara/i untuk melaksanakan program magang advokat ";
        $content .= "selama 24 bulan di kantor kami.\n\n";
        $content .= "Bidang penempatan: ".$lamaran->lowongan->judul."\n";
        $content .= "Tanggal mulai: ".now()->translatedFormat('d F Y')."\n\n";
        $content .= "Demikian surat ini kami sampaikan. Atas perhatiannya kami ucapkan terima kasih.\n\n";
        $content .= "Hormat kami,\n\n";
        $content .= $firm->nama."\n";

        \Storage::put($path, $content);

        $ca->berkasSumpahs()->updateOrCreate(
            ['jenis' => 'surat_penerimaan'],
            [
                'sumber' => 'Magang · diterbitkan '.$firm->nama,
                'status' => 'lengkap',
                'file_path' => $path,
                'ukuran' => number_format(strlen($content) / 1024, 1).' KB',
            ]
        );
    }

    public function terima(Lamaran $lamaran): RedirectResponse
    {
        return $this->updateStatus(new Request(['status' => 'diterima']), $lamaran);
    }
}
