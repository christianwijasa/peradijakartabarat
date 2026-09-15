<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LamaranController extends Controller
{
    public function index(): View
    {
        $internshipApplications = Auth::user()->candidateAdvocate
            ->internshipApplications()
            ->with(['jobPosting.lawFirm'])
            ->latest('applied_on')
            ->get();

        return view('candidate.lamaran', ['internshipApplications' => $internshipApplications]);
    }
}
