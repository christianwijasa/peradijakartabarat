<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logbook_entries', function (Blueprint $table) {
            $table->string('ditandatangani_oleh')->nullable()->after('catatan_revisi');
            $table->string('kta_penandatangan')->nullable()->after('ditandatangani_oleh');
            $table->timestamp('tanggal_ttd')->nullable()->after('kta_penandatangan');
            $table->text('signature_data')->nullable()->after('tanggal_ttd');
            $table->string('payload_hash')->nullable()->after('signature_data');
        });

        Schema::table('logbook_rekap_bulanans', function (Blueprint $table) {
            $table->string('kta_penandatangan')->nullable()->after('ditandatangani_oleh');
            $table->text('signature_data')->nullable()->after('tanggal_ttd');
            $table->string('payload_hash')->nullable()->after('signature_data');
        });
    }

    public function down(): void
    {
        Schema::table('logbook_entries', function (Blueprint $table) {
            $table->dropColumn(['ditandatangani_oleh', 'kta_penandatangan', 'tanggal_ttd', 'signature_data', 'payload_hash']);
        });

        Schema::table('logbook_rekap_bulanans', function (Blueprint $table) {
            $table->dropColumn(['kta_penandatangan', 'signature_data', 'payload_hash']);
        });
    }
};
