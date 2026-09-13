<?php

namespace App\Http\Controllers\Firm;

use App\Http\Controllers\Controller;
use App\Models\Lowongan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LowonganController extends Controller
{
    public function index(): View
    {
        $pendamping = Auth::user()->advokatPendamping;
        $firm = $pendamping->lawFirm;

        $lowongans = $firm->lowongans()
            ->withCount(['lamarans as total_pelamar', 'lamarans as pelamar_diterima' => fn ($q) => $q->where('status', 'diterima')])
            ->orderByDesc('created_at')
            ->get();

        return view('firm.lowongan.index', ['firm' => $firm, 'lowongans' => $lowongans]);
    }

    public function create(): View
    {
        $pendamping = Auth::user()->advokatPendamping;
        $firm = $pendamping->lawFirm;

        abort_if($firm->status_verifikasi !== 'terverifikasi', 403, 'Kantor hukum belum terverifikasi oleh Admin DPC.');

        return view('firm.lowongan.create', ['firm' => $firm]);
    }

    public function store(Request $request): RedirectResponse
    {
        $pendamping = Auth::user()->advokatPendamping;
        $firm = $pendamping->lawFirm;

        abort_if($firm->status_verifikasi !== 'terverifikasi', 403);

        $data = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'bidang' => ['required', 'array', 'min:1'],
            'bidang.*' => ['string'],
            'kuota' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $firm->lowongans()->create([
            'judul' => $data['judul'],
            'deskripsi' => $data['deskripsi'],
            'bidang' => $data['bidang'],
            'kuota' => $data['kuota'],
            'status' => 'aktif',
        ]);

        return redirect()->route('firm.lowongan.index')->with('status', 'Lowongan berhasil dipublikasikan.');
    }

    public function edit(Lowongan $lowongan): View
    {
        $pendamping = Auth::user()->advokatPendamping;
        abort_unless($lowongan->law_firm_id === $pendamping->law_firm_id, 403);

        return view('firm.lowongan.edit', ['lowongan' => $lowongan, 'firm' => $pendamping->lawFirm]);
    }

    public function update(Request $request, Lowongan $lowongan): RedirectResponse
    {
        $pendamping = Auth::user()->advokatPendamping;
        abort_unless($lowongan->law_firm_id === $pendamping->law_firm_id, 403);

        $data = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'bidang' => ['required', 'array', 'min:1'],
            'bidang.*' => ['string'],
            'kuota' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $lowongan->update([
            'judul' => $data['judul'],
            'deskripsi' => $data['deskripsi'],
            'bidang' => $data['bidang'],
            'kuota' => $data['kuota'],
        ]);

        return redirect()->route('firm.lowongan.index')->with('status', 'Lowongan berhasil diperbarui.');
    }

    public function toggleStatus(Lowongan $lowongan): RedirectResponse
    {
        $pendamping = Auth::user()->advokatPendamping;
        abort_unless($lowongan->law_firm_id === $pendamping->law_firm_id, 403);

        $newStatus = $lowongan->status === 'aktif' ? 'nonaktif' : 'aktif';
        $lowongan->update(['status' => $newStatus]);

        $message = $newStatus === 'aktif' ? 'Lowongan diaktifkan kembali.' : 'Lowongan dinonaktifkan.';

        return back()->with('status', $message);
    }
}
