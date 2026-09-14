<?php

namespace App\Http\Controllers\Calon;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LowonganController extends Controller
{
    public function index(Request $request): View
    {
        $ca = Auth::user()->candidateAdvocate;

        $bidangFilter = ['Semua bidang', 'Litigasi', 'Korporasi', 'Prodeo'];
        $filter = in_array($request->query('bidang'), $bidangFilter, true) ? $request->query('bidang') : 'Semua bidang';

        $jobPostings = JobPosting::query()
            ->where('status', 'ACTIVE')
            ->whereHas('lawFirm', fn ($q) => $q->where('verification_status', 'VERIFIED'))
            ->with('lawFirm')
            ->when($filter !== 'Semua bidang', fn ($q) => $q->whereJsonContains('practice_areas', $filter))
            ->when($request->query('q'), fn ($q, $keyword) => $q->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhereHas('lawFirm', fn ($q) => $q->where('name', 'like', "%{$keyword}%"));
            }))
            ->get();

        $internshipApplicationFirmIds = $ca->internshipApplications()->with('jobPosting')->get()->pluck('jobPosting.law_firm_id')->filter()->all();

        return view('calon.lowongan', [
            'jobPostings' => $jobPostings,
            'bidangFilter' => $bidangFilter,
            'filter' => $filter,
            'internshipApplicationFirmIds' => $internshipApplicationFirmIds,
            'keyword' => $request->query('q'),
        ]);
    }

    public function lamar(JobPosting $jobPosting): RedirectResponse
    {
        $ca = Auth::user()->candidateAdvocate;

        if (! $ca->internshipApplications()->where('job_posting_id', $jobPosting->id)->exists()) {
            $ca->internshipApplications()->create([
                'job_posting_id' => $jobPosting->id,
                'status' => 'SUBMITTED',
                'applied_on' => now(),
            ]);
        }

        return back()->with('status', 'Lamaran berhasil dikirim ke '.$jobPosting->lawFirm->name.'.');
    }
}
