<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('berkas_sumpahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_advokat_id')->constrained()->cascadeOnDelete();
            $table->enum('jenis', [
                'sertifikat_pkpa', 'sertifikat_lulus_upa', 'ijazah_transkrip',
                'rekap_logbook', 'sertifikat_selesai_magang', 'surat_rekomendasi_dpc',
            ]);
            $table->string('sumber')->nullable();
            $table->enum('status', ['lengkap', 'berjalan', 'menunggu'])->default('menunggu');
            $table->string('file_path')->nullable();
            $table->string('ukuran')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berkas_sumpahs');
    }
};
