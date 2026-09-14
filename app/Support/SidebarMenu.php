<?php

namespace App\Support;

use App\Models\CandidateAdvocate;
use App\Models\InternshipApplication;
use App\Models\LawFirm;
use Illuminate\Support\Facades\Auth;

class SidebarMenu
{
    public static function calon(string $active): array
    {
        $ca = Auth::user()->candidateAdvocate;
        $logbookBadge = $ca ? $ca->logbookEntries()->where('status', 'PENDING_SIGNATURE')->count() : 0;

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
        $pendamping = Auth::user()->supervisingLawyer;
        $pelamarBadge = 0;
        if ($pendamping) {
            $pelamarBadge = InternshipApplication::whereHas('jobPosting', fn ($q) => $q->where('law_firm_id', $pendamping->law_firm_id))
                ->whereIn('status', ['SUBMITTED', 'CV_REVIEW', 'INTERVIEW'])
                ->count();
        }

        return [
            ['label' => 'Dashboard & Kuota', 'route' => route('firm.dashboard'), 'active' => $active === 'dashboard'],
            ['label' => 'Pelamar', 'route' => route('firm.pelamar'), 'active' => $active === 'pelamar', 'badge' => $pelamarBadge ?: null],
            ['label' => 'Review Logbook', 'route' => route('firm.logbook'), 'active' => $active === 'logbook'],
        ];
    }

    public static function admin(string $active): array
    {
        $badge = CandidateAdvocate::whereIn('verification_status', ['PENDING', 'NEEDS_CORRECTION'])->count()
            + LawFirm::whereIn('verification_status', ['PENDING', 'NEEDS_CORRECTION'])->count();

        return [
            ['label' => 'Verifikasi', 'route' => route('admin.verifikasi'), 'active' => $active === 'verifikasi', 'badge' => $badge ?: null],
            ['label' => 'Monitoring & Audit', 'route' => route('admin.monitoring'), 'active' => $active === 'monitoring'],
        ];
    }
}
