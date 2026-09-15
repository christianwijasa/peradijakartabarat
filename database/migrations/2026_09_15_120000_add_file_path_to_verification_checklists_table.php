<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('verification_checklists', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('label');
            $table->string('file_size_label')->nullable()->after('file_path');
        });
    }

    public function down(): void
    {
        Schema::table('verification_checklists', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'file_size_label']);
        });
    }
};
