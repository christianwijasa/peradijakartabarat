<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class VerificationChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'checkable_type',
        'checkable_id',
        'label',
        'file_path',
        'file_size_label',
        'is_checked',
    ];

    protected $casts = [
        'is_checked' => 'boolean',
    ];

    public function checkable(): MorphTo
    {
        return $this->morphTo();
    }
}
