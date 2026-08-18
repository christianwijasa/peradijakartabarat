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
        Schema::create('lamarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_advokat_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lowongan_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['terkirim', 'review_cv', 'interview', 'diterima', 'tidak_lanjut'])->default('terkirim');
            $table->date('tanggal_lamar');
            $table->timestamps();
            $table->unique(['calon_advokat_id', 'lowongan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lamarans');
    }
};
