<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('verification_checklists', function (Blueprint $table) {
            $table->string('document_url', 2048)->nullable()->after('file_size_label');
        });
    }

    public function down(): void
    {
        Schema::table('verification_checklists', function (Blueprint $table) {
            $table->dropColumn('document_url');
        });
    }
};
