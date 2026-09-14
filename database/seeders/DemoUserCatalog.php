<?php

namespace Database\Seeders;

/**
 * Demo login accounts created by DatabaseSeeder (all share the same password).
 */
final class DemoUserCatalog
{
    public const PASSWORD = 'password';

    /**
     * Primary logins — one per role for smoke testing.
     *
     * @return list<array{role: string, email: string, name: string, highlight: string}>
     */
    public static function primary(): array
    {
        return [
            [
                'role' => 'calon_advokat',
                'email' => 'andi@peradijakbar.test',
                'name' => 'Andi Prasetyo, S.H.',
                'highlight' => 'Main demo: 14/24 bulan magang, Wibisono & Rekan, logbook & berkas',
            ],
            [
                'role' => 'law_firm',
                'email' => 'hendra@peradijakbar.test',
                'name' => 'Dr. Hendra Wibisono',
                'highlight' => 'Advokat pendamping Wibisono & Rekan — pelamar, logbook, tanda tangan',
            ],
            [
                'role' => 'admin_dpc',
                'email' => 'admin@peradijakbar.test',
                'name' => 'Sekretariat DPC',
                'highlight' => 'Verifikasi calon/kantor, monitoring kepatuhan',
            ],
        ];
    }

    /**
     * Additional calon advokat logins (same password).
     *
     * @return list<array{email: string, name: string, code: string, scenario: string}>
     */
    public static function calonAdvocates(): array
    {
        return [
            ['email' => 'andi@peradijakbar.test', 'name' => 'Andi Prasetyo, S.H.', 'code' => 'CA-2025-0417', 'scenario' => 'Magang berjalan 14 bulan, lamaran diterima'],
            ['email' => 'rizky@peradijakbar.test', 'name' => 'Rizky Alamsyah, S.H.', 'code' => 'CA-2024-0288', 'scenario' => '22/24 bulan, audit akhir dalam proses'],
            ['email' => 'nadia@peradijakbar.test', 'name' => 'Nadia Kusuma, S.H.', 'code' => 'CA-2025-0512', 'scenario' => '11/24 bulan, logbook lengkap'],
            ['email' => 'fajar@peradijakbar.test', 'name' => 'Fajar Nugroho, S.H.', 'code' => 'CA-2026-0031', 'scenario' => '3/24 bulan, rekap menunggu tanda tangan'],
            ['email' => 'maya@peradijakbar.test', 'name' => 'Maya Ramadhani, S.H.', 'code' => 'CA-2026-0058', 'scenario' => 'Verifikasi admin pending, lamaran interview'],
            ['email' => 'bagus@peradijakbar.test', 'name' => 'Bagus Setiawan, S.H.', 'code' => 'CA-2025-0349', 'scenario' => 'Verifikasi perlu perbaikan, lamaran CV review'],
            ['email' => 'laras@peradijakbar.test', 'name' => 'Laras Nuraini, S.H.', 'code' => 'CA-2026-0072', 'scenario' => 'Verifikasi pending, lamaran CV review'],
            ['email' => 'rina@peradijakbar.test', 'name' => 'Rina Marlina, S.H.', 'code' => 'CA-2025-0201', 'scenario' => 'Lamaran ditolak (tidak lanjut)'],
            ['email' => 'indra@peradijakbar.test', 'name' => 'Indra Kurniawan, S.H.', 'code' => 'CA-2025-0155', 'scenario' => 'Magang Santika & Partners, kepatuhan ~87%'],
            ['email' => 'dewi@peradijakbar.test', 'name' => 'Dewi Anggraini, S.H.', 'code' => 'CA-2024-0090', 'scenario' => '24/24 bulan, audit lulus, berkas sumpah lengkap'],
            ['email' => 'yoga@peradijakbar.test', 'name' => 'Yoga Permana, S.H.', 'code' => 'CA-2024-0102', 'scenario' => '24/24 bulan, audit berkas kurang'],
            ['email' => 'sinta@peradijakbar.test', 'name' => 'Sinta Wulandari, S.H.', 'code' => 'CA-2025-0233', 'scenario' => 'LBH Trisakti, 9 bulan magang'],
        ];
    }
}
