<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditAkhir extends Model
{
    use HasFactory;

    protected $fillable = [
        'calon_advokat_id',
        'status',
        'catatan',
        'tanggal_audit',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_audit' => 'datetime',
        ];
    }

    public function calonAdvokat(): BelongsTo
    {
        return $this->belongsTo(CalonAdvokat::class);
    }
}
