<?php

namespace App\Support;

use App\Models\CandidateAdvocate;
use App\Models\InternshipApplication;
use App\Models\LawFirm;
use App\Support\CandidateVerificationChecklist;
use Illuminate\Support\Facades\Auth;

class SidebarMenu
{
    public static function candidate(string $active): array
    {
        $ca = Auth::user()->candidateAdvocate;
        $logbookBadge = $ca ? $ca->logbookEntries()->where('status', 'PENDING_SIGNATURE')->count() : 0;
        $verificationBadge = $ca && in_array($ca->verification_status, ['PENDING', 'NEEDS_CORRECTION'], true)
            ? CandidateVerificationChecklist::pendingUploadCount($ca)
            : 0;

        return [
            ['label' => 'Dashboard', 'route' => route('candidate.dashboard'), 'active' => $active === 'dashboard'],
            ['label' => 'Verifikasi Admisi', 'route' => route('candidate.verification'), 'active' => $active === 'verification', 'badge' => $verificationBadge ?: null],
            ['label' => 'Cari Lowongan', 'route' => route('candidate.lowongan'), 'active' => $active === 'lowongan'],
            ['label' => 'Lamaran Saya', 'route' => route('candidate.lamaran'), 'active' => $active === 'lamaran'],
            ['label' => 'Logbook Digital', 'route' => route('candidate.logbook'), 'active' => $active === 'logbook', 'badge' => $logbookBadge ?: null],
            ['label' => 'Berkas Sumpah', 'route' => route('candidate.berkas'), 'active' => $active === 'berkas'],
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
            ['label' => 'Verifikasi', 'route' => route('admin.verification'), 'active' => $active === 'verification', 'badge' => $badge ?: null],
            ['label' => 'Monitoring & Audit', 'route' => route('admin.monitoring'), 'active' => $active === 'monitoring'],
        ];
    }
}
