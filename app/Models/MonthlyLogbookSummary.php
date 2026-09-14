<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyLogbookSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_advocate_id',
        'month',
        'year',
        'signed_by_supervising_lawyer_id',
        'status',
        'signed_at',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function candidateAdvocate(): BelongsTo
    {
        return $this->belongsTo(CandidateAdvocate::class);
    }

    public function signedBySupervisingLawyer(): BelongsTo
    {
        return $this->belongsTo(SupervisingLawyer::class, 'signed_by_supervising_lawyer_id');
    }
}
