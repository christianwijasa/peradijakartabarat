<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supervising_lawyers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('law_firm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('bar_membership_number')->nullable();
            $table->boolean('bar_membership_active')->default(true);
            $table->unsignedInteger('years_of_experience')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supervising_lawyers');
    }
};
