<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

class CalonAdvokat extends Model
{
    use HasFactory;

    protected $attributes = [
        'status_keanggotaan' => 'aktif',
        'status_verifikasi' => 'menunggu',
        'masa_magang_bulan' => 24,
    ];

    protected $fillable = [
        'user_id',
        'kode_ca',
        'nik',
        'universitas',
        'ipk',
        'upa_gelombang',
        'tahun_lulus_upa',
        'status_keanggotaan',
        'status_verifikasi',
        'law_firm_id',
        'advokat_pendamping_id',
        'bidang_penempatan',
        'tanggal_mulai_magang',
        'masa_magang_bulan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai_magang' => 'date',
            'ipk' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lawFirm(): BelongsTo
    {
        return $this->belongsTo(LawFirm::class);
    }

    public function advokatPendamping(): BelongsTo
    {
        return $this->belongsTo(AdvokatPendamping::class);
    }

    public function lamarans(): HasMany
    {
        return $this->hasMany(Lamaran::class);
    }

    public function logbookEntries(): HasMany
    {
        return $this->hasMany(LogbookEntry::class);
    }

    public function logbookRekapBulanans(): HasMany
    {
        return $this->hasMany(LogbookRekapBulanan::class);
    }

    public function berkasSumpahs(): HasMany
    {
        return $this->hasMany(BerkasSumpah::class);
    }

    public function auditAkhirs(): HasMany
    {
        return $this->hasMany(AuditAkhir::class);
    }

    public function checklistItems(): MorphMany
    {
        return $this->morphMany(VerifikasiChecklist::class, 'checkable');
    }

    public function bulanBerjalan(): int
    {
        if (! $this->tanggal_mulai_magang) {
            return 0;
        }

        return min($this->masa_magang_bulan, (int) $this->tanggal_mulai_magang->diffInMonths(now()));
    }

    public function progresPersen(): int
    {
        if ($this->masa_magang_bulan <= 0) {
            return 0;
        }

        return (int) round(($this->bulanBerjalan() / $this->masa_magang_bulan) * 100);
    }

    public function estimasiSelesai(): ?Carbon
    {
        return $this->tanggal_mulai_magang?->copy()->addMonths($this->masa_magang_bulan);
    }
}
