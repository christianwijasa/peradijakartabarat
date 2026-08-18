<?php

namespace App\Http\Controllers\Firm;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use App\Models\LogbookEntry;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $pendamping = Auth::user()->advokatPendamping;
        $firm = $pendamping->lawFirm;

        $kuotaTerpakai = $firm->kuotaTerpakai();
        $kuotaSlots = collect(range(1, $firm->kuota_maks))->map(fn ($i) => $i <= $kuotaTerpakai);

        $pelamarMenunggu = Lamaran::whereHas('lowongan', fn ($q) => $q->where('law_firm_id', $firm->id))
            ->whereIn('status', ['terkirim', 'review_cv', 'interview'])
            ->count();

        $logbookBelumTtd = LogbookEntry::whereIn('calon_advokat_id', $firm->calonAdvokats()->pluck('id'))
            ->where('status', 'menunggu_ttd')
            ->count();

        $mendekatiSelesai = $firm->calonAdvokats()
            ->where('status_keanggotaan', 'aktif')
            ->get()
            ->filter(fn ($ca) => $ca->bulanBerjalan() >= $ca->masa_magang_bulan - 2)
            ->count();

        $firmTasks = [];
        if ($pelamarMenunggu > 0) {
            $firmTasks[] = [
                'label' => $pelamarMenunggu.' pelamar menunggu review CV',
                'sub' => 'Kuota tersisa '.$firm->kuotaTersisa().' slot',
                'cta' => 'Buka pelamar',
                'route' => route('firm.pelamar'),
            ];
        }
        if ($logbookBelumTtd > 0) {
            $firmTasks[] = [
                'label' => $logbookBelumTtd.' entri logbook belum ditandatangani',
                'sub' => 'Rekap bulan ini',
                'cta' => 'Review logbook',
                'route' => route('firm.logbook'),
            ];
        }
        if ($mendekatiSelesai > 0) {
            $firmTasks[] = [
                'label' => 'Pemagang mendekati masa selesai',
                'sub' => $mendekatiSelesai.' pemagang mendekati bulan ke-'.($firm->calonAdvokats()->first()?->masa_magang_bulan ?? 24),
                'cta' => 'Lihat pemagang',
                'route' => route('firm.dashboard'),
            ];
        }

        $pemagang = $firm->calonAdvokats()->with('user')->get()->map(function ($ca) {
            $lastEntry = $ca->logbookEntries()->latest('tanggal')->first();
            if (! $lastEntry) {
                $logStatus = 'Belum ada entri';
                $variant = 'mute';
            } elseif ($lastEntry->tanggal->diffInDays(now()) > 7) {
                $logStatus = 'Terlambat '.$lastEntry->tanggal->diffInDays(now()).' hari';
                $variant = 'bad';
            } elseif ($ca->logbookEntries()->where('status', 'menunggu_ttd')->exists()) {
                $logStatus = 'Menunggu ttd';
                $variant = 'wait';
            } else {
                $logStatus = 'Lengkap';
                $variant = 'ok';
            }

            return [
                'ca' => $ca,
                'logStatus' => $logStatus,
                'variant' => $variant,
            ];
        });

        return view('firm.dashboard', [
            'firm' => $firm,
            'kuotaTerpakai' => $kuotaTerpakai,
            'kuotaSlots' => $kuotaSlots,
            'firmTasks' => $firmTasks,
            'pemagang' => $pemagang,
        ]);
    }
}
