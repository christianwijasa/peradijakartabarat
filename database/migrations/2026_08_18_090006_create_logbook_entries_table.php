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
        Schema::create('logbook_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_advokat_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('jenis_kegiatan');
            $table->decimal('jam', 4, 1)->default(0);
            $table->text('uraian');
            $table->enum('status', ['menunggu_ttd', 'disetujui', 'revisi'])->default('menunggu_ttd');
            $table->text('catatan_revisi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook_entries');
    }
};
