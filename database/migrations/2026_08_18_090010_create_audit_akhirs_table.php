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
        Schema::create('audit_akhirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_advokat_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['lulus_audit', 'dalam_proses', 'berkas_kurang'])->default('dalam_proses');
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_audit')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_akhirs');
    }
};
