<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lamaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'calon_advokat_id',
        'lowongan_id',
        'status',
        'tanggal_lamar',
    ];

    protected $casts = [
        'tanggal_lamar' => 'date',
    ];

    public function calonAdvokat(): BelongsTo
    {
        return $this->belongsTo(CalonAdvokat::class);
    }

    public function lowongan(): BelongsTo
    {
        return $this->belongsTo(Lowongan::class);
    }
}
