<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdvokatPendamping extends Model
{
    use HasFactory;

    protected $fillable = [
        'law_firm_id',
        'user_id',
        'nama',
        'kta_nomor',
        'kta_aktif',
        'pengalaman_tahun',
    ];

    protected $casts = [
        'kta_aktif' => 'boolean',
    ];

    public function lawFirm(): BelongsTo
    {
        return $this->belongsTo(LawFirm::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function calonAdvokats(): HasMany
    {
        return $this->hasMany(CalonAdvokat::class);
    }
}
