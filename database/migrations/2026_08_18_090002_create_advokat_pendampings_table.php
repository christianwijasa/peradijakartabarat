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
        Schema::create('advokat_pendampings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('law_firm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nama');
            $table->string('kta_nomor')->nullable();
            $table->boolean('kta_aktif')->default(true);
            $table->unsignedInteger('pengalaman_tahun')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advokat_pendampings');
    }
};
