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

    public function setujui(LogbookEntry $entry): RedirectResponse
    {
        $this->authorizeEntry($entry);
        $entry->update(['status' => 'disetujui', 'catatan_revisi' => null]);

        return back()->with('status', 'Entri logbook disetujui.');
    }

    public function revisi(Request $request, LogbookEntry $entry): RedirectResponse
    {
        $this->authorizeEntry($entry);
        $data = $request->validate(['catatan_revisi' => ['nullable', 'string', 'max:500']]);
        $entry->update(['status' => 'revisi', 'catatan_revisi' => $data['catatan_revisi'] ?? 'Perlu direvisi oleh calon advokat.']);

        return back()->with('status', 'Entri logbook dikembalikan untuk revisi.');
    }

    public function tandatanganiSemua(LogbookRekapBulanan $rekap): RedirectResponse
    {
        $pendamping = Auth::user()->advokatPendamping;
        abort_unless($rekap->calonAdvokat->advokat_pendamping_id === $pendamping->id, 403);

        $rekap->calonAdvokat->logbookEntries()
            ->whereYear('tanggal', $rekap->tahun)
            ->whereMonth('tanggal', $rekap->bulan)
            ->whereIn('status', ['menunggu_ttd', 'revisi'])
            ->update(['status' => 'disetujui', 'catatan_revisi' => null]);

        $rekap->update([
            'status' => 'ditandatangani',
            'ditandatangani_oleh' => $pendamping->id,
            'tanggal_ttd' => now(),
        ]);

        return back()->with('status', 'Seluruh entri disetujui dan rekap bulanan ditandatangani.');
    }

    private function authorizeEntry(LogbookEntry $entry): void
    {
        $pendamping = Auth::user()->advokatPendamping;
        abort_unless($entry->calonAdvokat->advokat_pendamping_id === $pendamping->id, 403);
    }
}
