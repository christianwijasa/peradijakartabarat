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
        Schema::create('calon_advokats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('kode_ca')->unique();
            $table->string('nik')->nullable();
            $table->string('universitas')->nullable();
            $table->decimal('ipk', 3, 2)->nullable();
            $table->string('upa_gelombang')->nullable();
            $table->unsignedSmallInteger('tahun_lulus_upa')->nullable();
            $table->enum('status_keanggotaan', ['aktif', 'nonaktif'])->default('aktif');
            $table->enum('status_verifikasi', ['menunggu', 'terverifikasi', 'perlu_perbaikan'])->default('menunggu');
            $table->foreignId('law_firm_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('advokat_pendamping_id')->nullable()->constrained()->nullOnDelete();
            $table->string('bidang_penempatan')->nullable();
            $table->date('tanggal_mulai_magang')->nullable();
            $table->unsignedSmallInteger('masa_magang_bulan')->default(24);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calon_advokats');
    }
};
