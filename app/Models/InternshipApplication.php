<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternshipApplication extends Model
{
    use HasFactory;

    public const STATUS_SUBMITTED = 'SUBMITTED';

    public const STATUS_CV_REVIEW = 'CV_REVIEW';

    public const STATUS_INTERVIEW = 'INTERVIEW';

    public const STATUS_ACCEPTED = 'ACCEPTED';

    public const STATUS_REJECTED = 'REJECTED';

    protected $attributes = [
        'status' => self::STATUS_SUBMITTED,
    ];

    protected $fillable = [
        'candidate_advocate_id',
        'job_posting_id',
        'status',
        'applied_on',
    ];

    protected $casts = [
        'applied_on' => 'date',
    ];

    public function candidateAdvocate(): BelongsTo
    {
        return $this->belongsTo(CandidateAdvocate::class);
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }
}
