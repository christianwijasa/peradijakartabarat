<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogbookRekapBulanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'calon_advokat_id',
        'bulan',
        'tahun',
        'ditandatangani_oleh',
        'status',
        'tanggal_ttd',
    ];

    protected $casts = [
        'tanggal_ttd' => 'datetime',
    ];

    public function calonAdvokat(): BelongsTo
    {
        return $this->belongsTo(CalonAdvokat::class);
    }

    public function penandaTangan(): BelongsTo
    {
        return $this->belongsTo(AdvokatPendamping::class, 'ditandatangani_oleh');
    }
}
