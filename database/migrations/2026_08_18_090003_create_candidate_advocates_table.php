<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_advocates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('candidate_code')->unique();
            $table->string('national_id_number')->nullable();
            $table->string('university')->nullable();
            $table->decimal('gpa', 3, 2)->nullable();
            $table->string('bar_exam_cohort')->nullable();
            $table->unsignedSmallInteger('bar_exam_graduation_year')->nullable();
            $table->enum('membership_status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->enum('verification_status', ['PENDING', 'VERIFIED', 'NEEDS_CORRECTION'])->default('PENDING');
            $table->foreignId('law_firm_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supervising_lawyer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('placement_area')->nullable();
            $table->date('internship_started_on')->nullable();
            $table->unsignedSmallInteger('internship_months')->default(24);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_advocates');
    }
};
