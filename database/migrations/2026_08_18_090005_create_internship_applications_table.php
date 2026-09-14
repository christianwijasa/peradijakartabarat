<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internship_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_advocate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_posting_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['SUBMITTED', 'CV_REVIEW', 'INTERVIEW', 'ACCEPTED', 'REJECTED'])->default('SUBMITTED');
            $table->date('applied_on');
            $table->timestamps();
            $table->unique(['candidate_advocate_id', 'job_posting_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internship_applications');
    }
};
