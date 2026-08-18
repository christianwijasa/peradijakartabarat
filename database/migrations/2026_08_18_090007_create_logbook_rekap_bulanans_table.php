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
        Schema::create('logbook_rekap_bulanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_advokat_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('bulan');
            $table->unsignedSmallInteger('tahun');
            $table->foreignId('ditandatangani_oleh')->nullable()->constrained('advokat_pendampings')->nullOnDelete();
            $table->enum('status', ['berjalan', 'menunggu_ttd', 'ditandatangani'])->default('berjalan');
            $table->timestamp('tanggal_ttd')->nullable();
            $table->timestamps();
            $table->unique(['calon_advokat_id', 'bulan', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook_rekap_bulanans');
    }
};
