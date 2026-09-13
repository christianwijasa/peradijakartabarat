<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditAkhir;
use App\Models\CalonAdvokat;
use App\Models\LawFirm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    public function index(): View
    {
        $kepatuhan = LawFirm::where('status_verifikasi', 'terverifikasi')
            ->get()
            ->map(fn ($firm) => [
                'firm' => $firm,
                'pct' => $firm->kepatuhanLogbookPersen(),
            ])
            ->sortByDesc('pct')
            ->values();

        $alerts = [];

        $firmPenuh = LawFirm::where('status_verifikasi', 'terverifikasi')->get()
            ->first(fn ($f) => $f->kuota_maks > 0 && $f->kuotaTerpakai() / $f->kuota_maks >= 0.9);
        if ($firmPenuh) {
            $alerts[] = [
                'judul' => 'Kuota gabungan hampir penuh',
                'detail' => $firmPenuh->nama.': '.$firmPenuh->kuotaTerpakai().' dari '.$firmPenuh->kuota_maks.' slot terpakai.',
                'variant' => 'warn',
            ];
        }

        $telat = CalonAdvokat::where('status_keanggotaan', 'aktif')->whereNotNull('law_firm_id')->with(['user', 'lawFirm'])->get()
            ->map(function ($ca) {
                $last = $ca->logbookEntries()->latest('tanggal')->first();
                $days = $last ? $last->tanggal->diffInDays(now()) : ($ca->tanggal_mulai_magang?->diffInDays(now()) ?? 0);

                return ['ca' => $ca, 'days' => $days];
            })
            ->sortByDesc('days')
            ->first(fn ($row) => $row['days'] >= 14);
        if ($telat) {
            $alerts[] = [
                'judul' => 'Logbook tidak diisi '.$telat['days'].' hari',
                'detail' => $telat['ca']->user->name.' — '.($telat['ca']->lawFirm->nama ?? '-').'.',
                'variant' => 'bad',
            ];
        }

        $mendekati = CalonAdvokat::where('status_keanggotaan', 'aktif')->whereNotNull('law_firm_id')->with(['user', 'lawFirm'])->get()
            ->first(fn ($ca) => $ca->masa_magang_bulan - $ca->bulanBerjalan() <= 2 && $ca->masa_magang_bulan - $ca->bulanBerjalan() > 0);
        if ($mendekati) {
            $sisa = $mendekati->masa_magang_bulan - $mendekati->bulanBerjalan();
            $alerts[] = [
                'judul' => 'Masa magang mendekati '.$mendekati->masa_magang_bulan.' bulan',
                'detail' => $mendekati->user->name.' — sisa '.$sisa.' bulan, berkas mulai disiapkan.',
                'variant' => 'info',
            ];
        }

        $auditList = CalonAdvokat::whereNotNull('law_firm_id')
            ->with(['user', 'auditAkhirs' => fn ($q) => $q->latest()])
            ->get()
            ->filter(fn ($ca) => $ca->bulanBerjalan() >= $ca->masa_magang_bulan - 2)
            ->map(function ($ca) {
                $audit = $ca->auditAkhirs->first();
                $rekapBelumTtd = $ca->logbookRekapBulanans()->where('status', '!=', 'ditandatangani')->count();
                $status = $audit?->status ?? 'dalam_proses';
                $detail = $ca->bulanBerjalan().'/'.$ca->masa_magang_bulan.' bulan';
                $detail .= $rekapBelumTtd > 0 ? ' · '.$rekapBelumTtd.' rekap belum ditandatangani' : ' · logbook lengkap';

                return ['ca' => $ca, 'status' => $status, 'detail' => $detail];
            })
            ->values();

        return view('admin.monitoring', [
            'kepatuhan' => $kepatuhan,
            'alerts' => $alerts,
            'auditList' => $auditList,
        ]);
    }

    public function setAuditStatus(Request $request, CalonAdvokat $calonAdvokat): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:lulus_audit,dalam_proses,berkas_kurang'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        AuditAkhir::updateOrCreate(
            ['calon_advokat_id' => $calonAdvokat->id],
            [
                'status' => $data['status'],
                'catatan' => $data['catatan'] ?? '',
                'tanggal_audit' => now(),
            ]
        );

        $messages = [
            'lulus_audit' => 'Status audit diubah menjadi lulus audit untuk '.$calonAdvokat->user->name.'.',
            'dalam_proses' => 'Status audit diubah menjadi dalam proses untuk '.$calonAdvokat->user->name.'.',
            'berkas_kurang' => 'Status audit diubah menjadi berkas kurang untuk '.$calonAdvokat->user->name.'.',
        ];

        return back()->with('status', $messages[$data['status']]);
    }
}
