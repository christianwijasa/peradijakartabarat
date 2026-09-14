<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('law_firms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('ministry_registration_number')->nullable();
            $table->boolean('is_equivalent_law_firm')->default(false);
            $table->unsignedInteger('max_quota')->default(10);
            $table->enum('verification_status', ['PENDING', 'VERIFIED', 'NEEDS_CORRECTION'])->default('PENDING');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('law_firms');
    }
};
