<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_logbook_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_advocate_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->foreignId('signed_by_supervising_lawyer_id')->nullable()->constrained('supervising_lawyers')->nullOnDelete();
            $table->enum('status', ['IN_PROGRESS', 'PENDING_SIGNATURE', 'SIGNED'])->default('IN_PROGRESS');
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();
            $table->unique(['candidate_advocate_id', 'month', 'year'], 'mls_candidate_period_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_logbook_summaries');
    }
};
