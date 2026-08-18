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
        Schema::create('law_firms', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('alamat')->nullable();
            $table->string('sk_kemenkumham')->nullable();
            $table->boolean('setara_kantor_advokat')->default(false);
            $table->unsignedInteger('kuota_maks')->default(10);
            $table->enum('status_verifikasi', ['menunggu', 'terverifikasi', 'perlu_perbaikan'])->default('menunggu');
            $table->timestamp('diverifikasi_pada')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('law_firms');
    }
};
