<?php

namespace App\Http\Controllers\Firm;

use App\Http\Controllers\Controller;
use App\Models\LogbookEntry;
use App\Models\LogbookRekapBulanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LogbookController extends Controller
{
    public function index(Request $request): View
    {
        $pendamping = Auth::user()->advokatPendamping;
        $calonList = $pendamping->calonAdvokats()->with('user')->get();

        $selectedId = $request->query('calon');
        $selected = $selectedId
            ? $calonList->firstWhere('id', (int) $selectedId)
            : $calonList->first(fn ($ca) => $ca->logbookEntries()->where('status', 'menunggu_ttd')->exists());
        $selected ??= $calonList->first();

        $entries = collect();
        $rekap = null;
        if ($selected) {
            $now = now();
            $entries = $selected->logbookEntries()
                ->whereYear('tanggal', $now->year)
                ->whereMonth('tanggal', $now->month)
                ->orderByDesc('tanggal')
                ->get();

            $rekap = LogbookRekapBulanan::firstOrCreate(
                ['calon_advokat_id' => $selected->id, 'bulan' => $now->month, 'tahun' => $now->year],
                ['status' => 'berjalan']
            );
        }

        return view('firm.logbook', [
            'pendamping' => $pendamping,
            'calonList' => $calonList,
            'selected' => $selected,
            'entries' => $entries,
            'rekap' => $rekap,
            'bulanLabel' => now()->translatedFormat('F Y'),
        ]);
    }

    public function setujui(Request $request, LogbookEntry $entry): RedirectResponse
    {
        $this->authorizeEntry($entry);
        
        $pendamping = Auth::user()->advokatPendamping;
        $signatureData = $request->validate([
            'signature_type' => ['required', 'in:typed,drawn'],
            'signature_value' => ['required', 'string'],
        ]);

        $payload = json_encode([
            'entry_id' => $entry->id,
            'tanggal' => $entry->tanggal->format('Y-m-d'),
            'uraian' => $entry->uraian,
            'jam' => $entry->jam,
        ]);

        $entry->update([
            'status' => 'disetujui',
            'catatan_revisi' => null,
            'ditandatangani_oleh' => $pendamping->nama,
            'kta_penandatangan' => $pendamping->kta_nomor,
            'tanggal_ttd' => now(),
            'signature_data' => json_encode($signatureData),
            'payload_hash' => hash('sha256', $payload),
        ]);

        return back()->with('status', 'Entri logbook disetujui dan ditandatangani digital.');
    }

    public function revisi(Request $request, LogbookEntry $entry): RedirectResponse
    {
        $this->authorizeEntry($entry);
        $data = $request->validate(['catatan_revisi' => ['nullable', 'string', 'max:500']]);
        $entry->update(['status' => 'revisi', 'catatan_revisi' => $data['catatan_revisi'] ?? 'Perlu direvisi oleh calon advokat.']);

        return back()->with('status', 'Entri logbook dikembalikan untuk revisi.');
    }

    public function tandatanganiSemua(Request $request, LogbookRekapBulanan $rekap): RedirectResponse
    {
        $pendamping = Auth::user()->advokatPendamping;
        abort_unless($rekap->calonAdvokat->advokat_pendamping_id === $pendamping->id, 403);

        $signatureData = $request->validate([
            'signature_type' => ['required', 'in:typed,drawn'],
            'signature_value' => ['required', 'string'],
        ]);

        $entries = $rekap->calonAdvokat->logbookEntries()
            ->whereYear('tanggal', $rekap->tahun)
            ->whereMonth('tanggal', $rekap->bulan)
            ->whereIn('status', ['menunggu_ttd', 'revisi'])
            ->get();

        foreach ($entries as $entry) {
            $payload = json_encode([
                'entry_id' => $entry->id,
                'tanggal' => $entry->tanggal->format('Y-m-d'),
                'uraian' => $entry->uraian,
                'jam' => $entry->jam,
            ]);

            $entry->update([
                'status' => 'disetujui',
                'catatan_revisi' => null,
                'ditandatangani_oleh' => $pendamping->nama,
                'kta_penandatangan' => $pendamping->kta_nomor,
                'tanggal_ttd' => now(),
                'signature_data' => json_encode($signatureData),
                'payload_hash' => hash('sha256', $payload),
            ]);
        }

        $rekapPayload = json_encode([
            'rekap_id' => $rekap->id,
            'bulan' => $rekap->bulan,
            'tahun' => $rekap->tahun,
            'entry_count' => $entries->count(),
        ]);

        $rekap->update([
            'status' => 'ditandatangani',
            'ditandatangani_oleh' => $pendamping->id,
            'kta_penandatangan' => $pendamping->kta_nomor,
            'tanggal_ttd' => now(),
            'signature_data' => json_encode($signatureData),
            'payload_hash' => hash('sha256', $rekapPayload),
        ]);

        return back()->with('status', 'Seluruh entri disetujui dan rekap bulanan ditandatangani digital.');
    }

    private function authorizeEntry(LogbookEntry $entry): void
    {
        $pendamping = Auth::user()->advokatPendamping;
        abort_unless($entry->calonAdvokat->advokat_pendamping_id === $pendamping->id, 403);
    }
}
