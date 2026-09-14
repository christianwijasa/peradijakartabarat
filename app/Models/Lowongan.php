<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lowongan extends Model
{
    use HasFactory;

    protected $fillable = [
        'law_firm_id',
        'judul',
        'deskripsi',
        'bidang',
        'kuota',
        'status',
    ];

    protected $casts = [
        'bidang' => 'array',
    ];

    public function lawFirm(): BelongsTo
    {
        return $this->belongsTo(LawFirm::class);
    }

    public function lamarans(): HasMany
    {
        return $this->hasMany(Lamaran::class);
    }

    public function slotTersisa(): int
    {
        return max(0, $this->kuota - $this->lamarans()->where('status', 'diterima')->count());
    }
}
