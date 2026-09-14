<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

class CandidateAdvocate extends Model
{
    use HasFactory;

    protected $attributes = [
        'membership_status' => 'ACTIVE',
        'verification_status' => 'PENDING',
        'internship_months' => 24,
    ];

    protected $fillable = [
        'user_id',
        'candidate_code',
        'national_id_number',
        'university',
        'gpa',
        'bar_exam_cohort',
        'bar_exam_graduation_year',
        'membership_status',
        'verification_status',
        'law_firm_id',
        'supervising_lawyer_id',
        'placement_area',
        'internship_started_on',
        'internship_months',
    ];

    protected $casts = [
        'internship_started_on' => 'date',
        'gpa' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lawFirm(): BelongsTo
    {
        return $this->belongsTo(LawFirm::class);
    }

    public function supervisingLawyer(): BelongsTo
    {
        return $this->belongsTo(SupervisingLawyer::class);
    }

    public function internshipApplications(): HasMany
    {
        return $this->hasMany(InternshipApplication::class);
    }

    public function logbookEntries(): HasMany
    {
        return $this->hasMany(LogbookEntry::class);
    }

    public function monthlyLogbookSummaries(): HasMany
    {
        return $this->hasMany(MonthlyLogbookSummary::class);
    }

    public function oathDocuments(): HasMany
    {
        return $this->hasMany(OathDocument::class);
    }

    public function finalAudits(): HasMany
    {
        return $this->hasMany(FinalAudit::class);
    }

    public function checklistItems(): MorphMany
    {
        return $this->morphMany(VerificationChecklist::class, 'checkable');
    }

    public function bulanBerjalan(): int
    {
        if (! $this->internship_started_on) {
            return 0;
        }

        return min($this->internship_months, (int) $this->internship_started_on->diffInMonths(now()));
    }

    public function progresPersen(): int
    {
        if ($this->internship_months <= 0) {
            return 0;
        }

        return (int) round(($this->bulanBerjalan() / $this->internship_months) * 100);
    }

    public function estimasiSelesai(): ?Carbon
    {
        return $this->internship_started_on?->copy()->addMonths($this->internship_months);
    }
}
