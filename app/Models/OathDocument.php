<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OathDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_advocate_id',
        'document_type',
        'source_label',
        'status',
        'file_path',
        'file_size_label',
    ];

    public function candidateAdvocate(): BelongsTo
    {
        return $this->belongsTo(CandidateAdvocate::class);
    }

    public function label(): string
    {
        return match ($this->document_type) {
            'sertifikat_pkpa' => 'Sertifikat PKPA',
            'sertifikat_lulus_upa' => 'Sertifikat Lulus UPA',
            'ijazah_transkrip' => 'Ijazah S.H. & transkrip nilai',
            'rekap_logbook' => 'Rekap logbook 24 bulan',
            'sertifikat_selesai_magang' => 'Sertifikat selesai magang',
            'surat_rekomendasi_dpc' => 'Surat rekomendasi DPC',
            default => $this->document_type,
        };
    }
}
