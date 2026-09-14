<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logbook_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_advocate_id')->constrained()->cascadeOnDelete();
            $table->date('entry_date');
            $table->string('activity_type');
            $table->decimal('hours', 4, 1)->default(0);
            $table->text('description');
            $table->enum('status', ['PENDING_SIGNATURE', 'APPROVED', 'REVISION'])->default('PENDING_SIGNATURE');
            $table->text('revision_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbook_entries');
    }
};
