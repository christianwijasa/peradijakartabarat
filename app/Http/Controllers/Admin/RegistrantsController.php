<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CandidateAdvocate;
use App\Models\LawFirm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrantsController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'candidate');
        if ($tab === 'calon') {
            $tab = 'candidate';
        }
        $tab = in_array($tab, ['candidate', 'firm'], true) ? $tab : 'candidate';

        $keyword = trim((string) $request->query('q', ''));

        $stats = [
            ['value' => (string) CandidateAdvocate::count(), 'label' => 'Calon advokat terdaftar'],
            ['value' => (string) LawFirm::count(), 'label' => 'Kantor hukum terdaftar'],
        ];

        $candidates = $tab === 'candidate'
            ? $this->candidateQuery($keyword)->get()
            : collect();

        $firms = $tab === 'firm'
            ? $this->firmQuery($keyword)->get()
            : collect();

        return view('admin.registrants', [
            'tab' => $tab,
            'keyword' => $keyword,
            'stats' => $stats,
            'candidates' => $candidates,
            'firms' => $firms,
        ]);
    }

    private function candidateQuery(string $keyword): Builder
    {
        return CandidateAdvocate::query()
            ->with('user')
            ->when($keyword !== '', function (Builder $query) use ($keyword) {
                $query->where(function (Builder $inner) use ($keyword) {
                    $inner->where('candidate_code', 'like', "%{$keyword}%")
                        ->orWhere('university', 'like', "%{$keyword}%")
                        ->orWhereHas('user', fn (Builder $u) => $u
                            ->where('name', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%"));
                });
            })
            ->orderByDesc('created_at');
    }

    private function firmQuery(string $keyword): Builder
    {
        return LawFirm::query()
            ->with(['supervisingLawyers.user'])
            ->when($keyword !== '', function (Builder $query) use ($keyword) {
                $query->where(function (Builder $inner) use ($keyword) {
                    $inner->where('name', 'like', "%{$keyword}%")
                        ->orWhere('address', 'like', "%{$keyword}%")
                        ->orWhere('ministry_registration_number', 'like', "%{$keyword}%");
                });
            })
            ->orderByDesc('created_at');
    }
}
