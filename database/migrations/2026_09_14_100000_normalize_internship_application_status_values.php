<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const NEW_STATUSES = ['SUBMITTED', 'CV_REVIEW', 'INTERVIEW', 'ACCEPTED', 'REJECTED'];

    /** @var array<string, string> */
    private const LEGACY_STATUS_MAP = [
        'terkirim' => 'SUBMITTED',
        'review_cv' => 'CV_REVIEW',
        'interview' => 'INTERVIEW',
        'diterima' => 'ACCEPTED',
        'tidak_lanjut' => 'REJECTED',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('internship_applications')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            foreach (self::LEGACY_STATUS_MAP as $legacy => $modern) {
                DB::table('internship_applications')
                    ->where('status', $legacy)
                    ->update(['status' => $modern]);
            }

            return;
        }

        // MySQL ENUM comparisons are case-insensitive, so legacy `interview` and `INTERVIEW`
        // cannot coexist in one ENUM alter. Use VARCHAR, rewrite values, then restore ENUM.
        DB::statement(
            "ALTER TABLE internship_applications MODIFY COLUMN status VARCHAR(32) NOT NULL DEFAULT 'SUBMITTED'"
        );

        foreach (self::LEGACY_STATUS_MAP as $legacy => $modern) {
            DB::table('internship_applications')
                ->whereRaw('LOWER(status) = ?', [$legacy])
                ->update(['status' => $modern]);
        }

        $newList = implode("','", self::NEW_STATUSES);

        DB::statement(
            "ALTER TABLE internship_applications MODIFY COLUMN status ENUM('{$newList}') NOT NULL DEFAULT 'SUBMITTED'"
        );
    }

    public function down(): void
    {
        // Irreversible: legacy enum values are not restored.
    }
};
