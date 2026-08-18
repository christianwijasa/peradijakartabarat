<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class VerifikasiChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'checkable_type',
        'checkable_id',
        'label',
        'is_checked',
    ];

    protected function casts(): array
    {
        return [
            'is_checked' => 'boolean',
        ];
    }

    public function checkable(): MorphTo
    {
        return $this->morphTo();
    }
}
