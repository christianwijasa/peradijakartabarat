<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinalAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_advocate_id',
        'status',
        'notes',
        'audited_at',
    ];

    protected $casts = [
        'audited_at' => 'datetime',
    ];

    public function candidateAdvocate(): BelongsTo
    {
        return $this->belongsTo(CandidateAdvocate::class);
    }
}
