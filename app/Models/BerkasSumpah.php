<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BerkasSumpah extends Model
{
    use HasFactory;

    protected $fillable = [
        'calon_advokat_id',
        'jenis',
        'sumber',
        'status',
        'file_path',
        'ukuran',
    ];

    public function calonAdvokat(): BelongsTo
    {
        return $this->belongsTo(CalonAdvokat::class);
    }

    public function label(): string
    {
        return match ($this->jenis) {
            'sertifikat_pkpa' => 'Sertifikat PKPA',
            'sertifikat_lulus_upa' => 'Sertifikat Lulus UPA',
            'ijazah_transkrip' => 'Ijazah S.H. & transkrip nilai',
            'rekap_logbook' => 'Rekap logbook 24 bulan',
            'sertifikat_selesai_magang' => 'Sertifikat selesai magang',
            'surat_rekomendasi_dpc' => 'Surat rekomendasi DPC',
            default => $this->jenis,
        };
    }
}
