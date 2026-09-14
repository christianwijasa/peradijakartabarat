<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupervisingLawyer extends Model
{
    use HasFactory;

    protected $fillable = [
        'law_firm_id',
        'user_id',
        'name',
        'bar_membership_number',
        'bar_membership_active',
        'years_of_experience',
    ];

    protected $casts = [
        'bar_membership_active' => 'boolean',
    ];

    public function lawFirm(): BelongsTo
    {
        return $this->belongsTo(LawFirm::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function candidateAdvocates(): HasMany
    {
        return $this->hasMany(CandidateAdvocate::class);
    }
}
