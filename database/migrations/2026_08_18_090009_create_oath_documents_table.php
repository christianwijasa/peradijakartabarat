<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oath_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_advocate_id')->constrained()->cascadeOnDelete();
            $table->enum('document_type', [
                'sertifikat_pkpa', 'sertifikat_lulus_upa', 'ijazah_transkrip',
                'rekap_logbook', 'sertifikat_selesai_magang', 'surat_rekomendasi_dpc',
            ]);
            $table->string('source_label')->nullable();
            $table->enum('status', ['COMPLETE', 'IN_PROGRESS', 'PENDING'])->default('PENDING');
            $table->string('file_path')->nullable();
            $table->string('file_size_label')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oath_documents');
    }
};
