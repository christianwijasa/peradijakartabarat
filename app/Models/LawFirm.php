<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class LawFirm extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'alamat',
        'sk_kemenkumham',
        'setara_kantor_advokat',
        'kuota_maks',
        'status_verifikasi',
        'diverifikasi_pada',
    ];

    protected function casts(): array
    {
        return [
            'setara_kantor_advokat' => 'boolean',
            'diverifikasi_pada' => 'datetime',
        ];
    }

    public function advokatPendampings(): HasMany
    {
        return $this->hasMany(AdvokatPendamping::class);
    }

    public function calonAdvokats(): HasMany
    {
        return $this->hasMany(CalonAdvokat::class);
    }

    public function lowongans(): HasMany
    {
        return $this->hasMany(Lowongan::class);
    }

    public function checklistItems(): MorphMany
    {
        return $this->morphMany(VerifikasiChecklist::class, 'checkable');
    }

    public function kuotaTerpakai(): int
    {
        return $this->calonAdvokats()->where('status_keanggotaan', 'aktif')->count();
    }

    public function kuotaTersisa(): int
    {
        return max(0, $this->kuota_maks - $this->kuotaTerpakai());
    }

    public function kepatuhanLogbookPersen(): int
    {
        $entries = LogbookEntry::whereIn('calon_advokat_id', $this->calonAdvokats()->pluck('id'));
        $total = $entries->count();
        if ($total === 0) {
            return 0;
        }
        $disetujui = (clone $entries)->where('status', 'disetujui')->count();

        return (int) round(($disetujui / $total) * 100);
    }
}
