<?php

namespace App\Support;

use App\Models\CalonAdvokat;
use App\Models\Lamaran;
use App\Models\LawFirm;
use Illuminate\Support\Facades\Auth;

class SidebarMenu
{
    public static function calon(string $active): array
    {
        $ca = Auth::user()->calonAdvokat;
        $logbookBadge = $ca ? $ca->logbookEntries()->where('status', 'menunggu_ttd')->count() : 0;

        return [
            ['label' => 'Dashboard', 'route' => route('calon.dashboard'), 'active' => $active === 'dashboard'],
            ['label' => 'Cari Lowongan', 'route' => route('calon.lowongan'), 'active' => $active === 'lowongan'],
            ['label' => 'Lamaran Saya', 'route' => route('calon.lamaran'), 'active' => $active === 'lamaran'],
            ['label' => 'Logbook Digital', 'route' => route('calon.logbook'), 'active' => $active === 'logbook', 'badge' => $logbookBadge ?: null],
            ['label' => 'Berkas Sumpah', 'route' => route('calon.berkas'), 'active' => $active === 'berkas'],
        ];
    }

    public static function firm(string $active): array
    {
        $pendamping = Auth::user()->advokatPendamping;
        $pelamarBadge = 0;
        if ($pendamping) {
            $pelamarBadge = Lamaran::whereHas('lowongan', fn ($q) => $q->where('law_firm_id', $pendamping->law_firm_id))
                ->whereIn('status', ['terkirim', 'review_cv', 'interview'])
                ->count();
        }

        return [
            ['label' => 'Dashboard & Kuota', 'route' => route('firm.dashboard'), 'active' => $active === 'dashboard'],
            ['label' => 'Lowongan Magang', 'route' => route('firm.lowongan.index'), 'active' => $active === 'lowongan'],
            ['label' => 'Pelamar', 'route' => route('firm.pelamar'), 'active' => $active === 'pelamar', 'badge' => $pelamarBadge ?: null],
            ['label' => 'Review Logbook', 'route' => route('firm.logbook'), 'active' => $active === 'logbook'],
        ];
    }

    public static function admin(string $active): array
    {
        $badge = CalonAdvokat::whereIn('status_verifikasi', ['menunggu', 'perlu_perbaikan'])->count()
            + LawFirm::whereIn('status_verifikasi', ['menunggu', 'perlu_perbaikan'])->count();

        return [
            ['label' => 'Verifikasi', 'route' => route('admin.verifikasi'), 'active' => $active === 'verifikasi', 'badge' => $badge ?: null],
            ['label' => 'Monitoring & Audit', 'route' => route('admin.monitoring'), 'active' => $active === 'monitoring'],
        ];
    }
}
