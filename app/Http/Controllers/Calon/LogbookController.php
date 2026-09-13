<?php

namespace App\Http\Controllers\Calon;

use App\Http\Controllers\Controller;
use App\Models\LogbookRekapBulanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LogbookController extends Controller
{
    public function index(): View
    {
        $ca = Auth::user()->calonAdvokat;

        $now = now();
        $entries = $ca->logbookEntries()
            ->whereYear('tanggal', $now->year)
            ->whereMonth('tanggal', $now->month)
            ->orderByDesc('tanggal')
            ->get();

        return view('calon.logbook', [
            'ca' => $ca,
            'entries' => $entries,
            'bulanLabel' => $now->translatedFormat('F Y'),
            'pending' => $entries->where('status', 'menunggu_ttd')->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'jenis_kegiatan' => ['required', 'string', 'max:255'],
            'uraian' => ['required', 'string'],
            'jam' => ['required', 'numeric', 'min:0.5', 'max:24'],
        ]);

        $ca = Auth::user()->calonAdvokat;

        $ca->logbookEntries()->create([
            'tanggal' => now(),
            'jenis_kegiatan' => $data['jenis_kegiatan'],
            'jam' => $data['jam'],
            'uraian' => $data['uraian'],
            'status' => 'menunggu_ttd',
        ]);

        $now = now();
        LogbookRekapBulanan::firstOrCreate(
            ['calon_advokat_id' => $ca->id, 'bulan' => $now->month, 'tahun' => $now->year],
            ['status' => 'berjalan']
        );

        return back()->with('status', 'Catatan harian tersimpan dan menunggu tanda tangan pendamping.');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $ca = Auth::user()->calonAdvokat;
        $entry = $ca->logbookEntries()->findOrFail($id);

        abort_unless($entry->status === 'revisi', 403, 'Hanya entri dengan status revisi yang dapat diedit.');

        $data = $request->validate([
            'jenis_kegiatan' => ['required', 'string', 'max:255'],
            'uraian' => ['required', 'string'],
            'jam' => ['required', 'numeric', 'min:0.5', 'max:24'],
        ]);

        $entry->update([
            'jenis_kegiatan' => $data['jenis_kegiatan'],
            'jam' => $data['jam'],
            'uraian' => $data['uraian'],
            'status' => 'menunggu_ttd',
            'catatan_revisi' => null,
        ]);

        return back()->with('status', 'Revisi berhasil dikirim ulang untuk ditinjau pendamping.');
    }
}
