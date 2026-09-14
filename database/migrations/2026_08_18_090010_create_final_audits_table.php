<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('final_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_advocate_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['PASSED', 'IN_PROGRESS', 'INCOMPLETE_DOCUMENTS'])->default('IN_PROGRESS');
            $table->text('notes')->nullable();
            $table->timestamp('audited_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('final_audits');
    }
};
