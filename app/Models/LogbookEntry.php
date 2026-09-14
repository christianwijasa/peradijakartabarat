<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogbookEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'calon_advokat_id',
        'tanggal',
        'jenis_kegiatan',
        'jam',
        'uraian',
        'status',
        'catatan_revisi',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function calonAdvokat(): BelongsTo
    {
        return $this->belongsTo(CalonAdvokat::class);
    }
}
