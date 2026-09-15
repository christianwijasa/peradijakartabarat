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
        'document_url',
        'is_checked',
    ];

    public function hasDocumentReference(): bool
    {
        return filled($this->document_url) || filled($this->file_path);
    }

    protected $casts = [
        'is_checked' => 'boolean',
    ];

    public function checkable(): MorphTo
    {
        return $this->morphTo();
    }
}
