<?php

namespace Database\Seeders;

use App\Models\AdvokatPendamping;
use App\Models\AuditAkhir;
use App\Models\BerkasSumpah;
use App\Models\CalonAdvokat;
use App\Models\Lamaran;
use App\Models\LawFirm;
use App\Models\LogbookEntry;
use App\Models\LogbookRekapBulanan;
use App\Models\Lowongan;
use App\Models\User;
use App\Models\VerifikasiChecklist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        // ---- Law firms (verified, provide lowongan, appear in monitoring) ----
        $wibisono = LawFirm::create([
            'nama' => 'Wibisono & Rekan', 'alamat' => 'Jl. Panjang No. 12, Kebon Jeruk, Jakarta Barat',
            'sk_kemenkumham' => 'AHU-0041223.AH.01.01', 'kuota_maks' => 10,
            'status_verifikasi' => 'terverifikasi', 'diverifikasi_pada' => now()->subMonths(20),
        ]);
        $santika = LawFirm::create([
            'nama' => 'Santika, Hartono & Partners', 'alamat' => 'Jl. Kebon Jeruk Raya No. 8, Jakarta Barat',
            'sk_kemenkumham' => 'AHU-0038812.AH.01.01', 'kuota_maks' => 10,
            'status_verifikasi' => 'terverifikasi', 'diverifikasi_pada' => now()->subMonths(18),
        ]);
        $ardiansyah = LawFirm::create([
            'nama' => 'Kantor Hukum Ardiansyah', 'alamat' => 'Jl. Puri Kembangan No. 21, Jakarta Barat',
            'sk_kemenkumham' => 'AHU-0029931.AH.01.01', 'kuota_maks' => 10,
            'status_verifikasi' => 'terverifikasi', 'diverifikasi_pada' => now()->subMonths(15),
        ]);
        $lbhTrisakti = LawFirm::create([
            'nama' => 'LBH Kampus Universitas Trisakti', 'alamat' => 'Jl. Kyai Tapa No. 1, Grogol, Jakarta Barat',
            'sk_kemenkumham' => null, 'setara_kantor_advokat' => true, 'kuota_maks' => 10,
            'status_verifikasi' => 'terverifikasi', 'diverifikasi_pada' => now()->subMonths(24),
        ]);

        // ---- Law firms pending Admin DPC verification ----
        $wijaya = LawFirm::create([
            'nama' => 'Wijaya Legal Consult', 'alamat' => 'Jl. Tanjung Duren Raya No. 45, Jakarta Barat',
            'sk_kemenkumham' => 'AHU-0055102.AH.01.01', 'kuota_maks' => 10,
            'status_verifikasi' => 'menunggu',
        ]);
        VerifikasiChecklist::insert([
            ['checkable_type' => LawFirm::class, 'checkable_id' => $wijaya->id, 'label' => 'Domisili kantor di wilayah DPC Jakbar', 'is_checked' => true, 'created_at' => now(), 'updated_at' => now()],
            ['checkable_type' => LawFirm::class, 'checkable_id' => $wijaya->id, 'label' => 'KTA pendamping aktif', 'is_checked' => true, 'created_at' => now(), 'updated_at' => now()],
            ['checkable_type' => LawFirm::class, 'checkable_id' => $wijaya->id, 'label' => 'Bukti pengalaman praktik dilampirkan', 'is_checked' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $kusuma = LawFirm::create([
            'nama' => 'Kusuma & Associates', 'alamat' => 'Jl. Cengkareng Raya No. 9, Jakarta Barat',
            'sk_kemenkumham' => 'AHU-0061187.AH.01.01', 'kuota_maks' => 8,
            'status_verifikasi' => 'perlu_perbaikan',
        ]);
        VerifikasiChecklist::insert([
            ['checkable_type' => LawFirm::class, 'checkable_id' => $kusuma->id, 'label' => 'Domisili kantor terverifikasi', 'is_checked' => true, 'created_at' => now(), 'updated_at' => now()],
            ['checkable_type' => LawFirm::class, 'checkable_id' => $kusuma->id, 'label' => 'KTA pendamping aktif', 'is_checked' => true, 'created_at' => now(), 'updated_at' => now()],
            ['checkable_type' => LawFirm::class, 'checkable_id' => $kusuma->id, 'label' => 'Bukti pengalaman praktik belum lengkap', 'is_checked' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ---- Advokat pendamping ----
        $userHendra = User::create([
            'name' => 'Dr. Hendra Wibisono', 'email' => 'hendra@peradijakbar.test',
            'password' => $password, 'role' => 'law_firm', 'email_verified_at' => now(),
        ]);
        $hendra = AdvokatPendamping::create([
            'law_firm_id' => $wibisono->id, 'user_id' => $userHendra->id, 'nama' => 'Dr. Hendra Wibisono, S.H., M.H.',
            'kta_nomor' => 'KTA-DPC-JB-00214', 'kta_aktif' => true, 'pengalaman_tahun' => 14,
        ]);
        $pendampingSantika = AdvokatPendamping::create([
            'law_firm_id' => $santika->id, 'nama' => 'Ratna Santika, S.H., M.Kn.',
            'kta_nomor' => 'KTA-DPC-JB-00187', 'kta_aktif' => true, 'pengalaman_tahun' => 11,
        ]);

        // ---- Lowongan (only from verified firms) ----
        Lowongan::create([
            'law_firm_id' => $santika->id, 'judul' => 'Magang Calon Advokat — Litigasi Korporasi',
            'deskripsi' => 'Pendampingan perkara PKPU dan sengketa kontrak di PN Jakarta Barat.',
            'bidang' => ['Litigasi', 'Kepailitan', 'Full-time'], 'kuota' => 10,
        ]);
        Lowongan::create([
            'law_firm_id' => $ardiansyah->id, 'judul' => 'Magang Calon Advokat — Hukum Keluarga',
            'deskripsi' => 'Riset dan penyusunan gugatan perceraian, waris, dan permohonan penetapan.',
            'bidang' => ['Perdata', 'Keluarga', 'Hybrid'], 'kuota' => 10,
        ]);
        Lowongan::create([
            'law_firm_id' => $lbhTrisakti->id, 'judul' => 'Magang Calon Advokat — Bantuan Hukum',
            'deskripsi' => 'Pendampingan klien prodeo. Setara kantor advokat sesuai Peraturan PERADI No. 1/2015.',
            'bidang' => ['Pidana', 'Prodeo', 'Full-time'], 'kuota' => 10,
        ]);
        $lowonganWibisono = Lowongan::create([
            'law_firm_id' => $wibisono->id, 'judul' => 'Magang Calon Advokat — Litigasi Perdata & Kepailitan',
            'deskripsi' => 'Pendampingan perkara litigasi perdata dan kepailitan bersama advokat pendamping.',
            'bidang' => ['Litigasi', 'Korporasi', 'Full-time'], 'kuota' => 10,
        ]);

        // ---- Helper to create a calon advokat + login user ----
        $makeCalon = function (
            string $name, string $email, string $kode, array $attrs = []
        ) use ($password) {
            $user = User::create([
                'name' => $name, 'email' => $email, 'password' => $password,
                'role' => 'calon_advokat', 'email_verified_at' => now(),
            ]);

            return CalonAdvokat::create(array_merge([
                'user_id' => $user->id,
                'kode_ca' => $kode,
                'status_keanggotaan' => 'aktif',
                'status_verifikasi' => 'terverifikasi',
            ], $attrs));
        };

        // Helper: generate a month of logbook entries with a given approval ratio.
        $seedLogbook = function (CalonAdvokat $ca, int $months, float $approvalRatio, ?AdvokatPendamping $pendamping) {
            $jenis = ['Riset hukum', 'Pendampingan sidang', 'Drafting dokumen', 'Konsultasi internal'];
            for ($m = $months - 1; $m >= 0; $m--) {
                $monthDate = now()->subMonths($m);
                $entriesThisMonth = random_int(10, 16);
                $approvedInMonth = 0;
                for ($i = 0; $i < $entriesThisMonth; $i++) {
                    $approved = $m > 0 ? true : ($i / $entriesThisMonth) < $approvalRatio;
                    LogbookEntry::create([
                        'calon_advokat_id' => $ca->id,
                        'tanggal' => $monthDate->copy()->startOfMonth()->addDays($i),
                        'jenis_kegiatan' => $jenis[array_rand($jenis)],
                        'jam' => random_int(2, 7),
                        'uraian' => 'Catatan kegiatan magang harian di bidang '.strtolower($ca->bidang_penempatan ?? 'hukum').'.',
                        'status' => $approved ? 'disetujui' : 'menunggu_ttd',
                    ]);
                    if ($approved) {
                        $approvedInMonth++;
                    }
                }
                LogbookRekapBulanan::create([
                    'calon_advokat_id' => $ca->id,
                    'bulan' => $monthDate->month,
                    'tahun' => $monthDate->year,
                    'ditandatangani_oleh' => $approvedInMonth === $entriesThisMonth ? $pendamping?->id : null,
                    'status' => $approvedInMonth === $entriesThisMonth ? 'ditandatangani' : ($m === 0 ? 'menunggu_ttd' : 'berjalan'),
                    'tanggal_ttd' => $approvedInMonth === $entriesThisMonth ? $monthDate->copy()->endOfMonth() : null,
                ]);
            }
        };

        $berkasTemplate = fn (CalonAdvokat $ca, array $overrides = []) => array_merge([
            ['jenis' => 'sertifikat_pkpa', 'sumber' => 'Admisi · terbit 14 Feb 2025', 'status' => 'lengkap', 'ukuran' => '1,2 MB'],
            ['jenis' => 'sertifikat_lulus_upa', 'sumber' => 'Admisi · '.$ca->upa_gelombang, 'status' => 'lengkap', 'ukuran' => '0,9 MB'],
            ['jenis' => 'ijazah_transkrip', 'sumber' => 'Admisi · auto-populate profil', 'status' => 'lengkap', 'ukuran' => '3,4 MB'],
            ['jenis' => 'rekap_logbook', 'sumber' => 'Magang · '.$ca->bulanBerjalan().' bulan terekam', 'status' => 'berjalan', 'ukuran' => null],
            ['jenis' => 'sertifikat_selesai_magang', 'sumber' => 'Magang · diterbitkan kantor hukum', 'status' => 'menunggu', 'ukuran' => null],
            ['jenis' => 'surat_rekomendasi_dpc', 'sumber' => 'Magang · setelah audit akhir', 'status' => 'menunggu', 'ukuran' => null],
        ], $overrides);

        // ---- Andi Prasetyo (main demo login, 14/24 bulan) ----
        $andi = $makeCalon('Andi Prasetyo, S.H.', 'andi@peradijakbar.test', 'CA-2025-0417', [
            'nik' => '3171012345670001', 'universitas' => 'Universitas Indonesia', 'ipk' => 3.55,
            'upa_gelombang' => 'Gelombang II 2025', 'tahun_lulus_upa' => 2025,
            'law_firm_id' => $wibisono->id, 'advokat_pendamping_id' => $hendra->id,
            'bidang_penempatan' => 'Litigasi Perdata', 'tanggal_mulai_magang' => now()->subMonths(14),
        ]);
        $seedLogbook($andi, 14, 0.5, $hendra);
        foreach ($berkasTemplate($andi) as $b) {
            BerkasSumpah::create(array_merge(['calon_advokat_id' => $andi->id], $b));
        }

        // ---- Rizky Alamsyah (22/24 bulan, lengkap, audit dalam proses) ----
        $rizky = $makeCalon('Rizky Alamsyah, S.H.', 'rizky@peradijakbar.test', 'CA-2024-0288', [
            'nik' => '3171019876540002', 'universitas' => 'Universitas Trisakti', 'ipk' => 3.48,
            'upa_gelombang' => 'Gelombang I 2024', 'tahun_lulus_upa' => 2024,
            'law_firm_id' => $wibisono->id, 'advokat_pendamping_id' => $hendra->id,
            'bidang_penempatan' => 'Kepailitan', 'tanggal_mulai_magang' => now()->subMonths(22),
        ]);
        $seedLogbook($rizky, 22, 1.0, $hendra);
        AuditAkhir::create(['calon_advokat_id' => $rizky->id, 'status' => 'dalam_proses', 'catatan' => '1 rekap belum ditandatangani.']);

        // ---- Nadia Kusuma (11/24 bulan, lengkap) ----
        $nadia = $makeCalon('Nadia Kusuma, S.H.', 'nadia@peradijakbar.test', 'CA-2025-0512', [
            'nik' => '3171015566770003', 'universitas' => 'Universitas Tarumanagara', 'ipk' => 3.62,
            'upa_gelombang' => 'Gelombang II 2025', 'tahun_lulus_upa' => 2025,
            'law_firm_id' => $wibisono->id, 'advokat_pendamping_id' => $hendra->id,
            'bidang_penempatan' => 'Kontrak', 'tanggal_mulai_magang' => now()->subMonths(11),
        ]);
        $seedLogbook($nadia, 11, 1.0, $hendra);

        // ---- Fajar Nugroho (3/24 bulan, logbook terlambat 6 hari) ----
        $fajar = $makeCalon('Fajar Nugroho, S.H.', 'fajar@peradijakbar.test', 'CA-2026-0031', [
            'nik' => '3171013344550004', 'universitas' => 'Universitas Trisakti', 'ipk' => 3.30,
            'upa_gelombang' => 'Gelombang I 2026', 'tahun_lulus_upa' => 2026,
            'law_firm_id' => $wibisono->id, 'advokat_pendamping_id' => $hendra->id,
            'bidang_penempatan' => 'Litigasi Perdata', 'tanggal_mulai_magang' => now()->subMonths(3),
        ]);
        LogbookEntry::create([
            'calon_advokat_id' => $fajar->id, 'tanggal' => now()->subDays(7),
            'jenis_kegiatan' => 'Riset hukum', 'jam' => 4, 'uraian' => 'Riset awal perkara litigasi perdata.',
            'status' => 'disetujui',
        ]);
        LogbookRekapBulanan::create([
            'calon_advokat_id' => $fajar->id, 'bulan' => now()->month, 'tahun' => now()->year,
            'status' => 'menunggu_ttd',
        ]);

        // ---- Applicants pending both Admin verification and Firm review ----
        $maya = $makeCalon('Maya Ramadhani, S.H.', 'maya@peradijakbar.test', 'CA-2026-0058', [
            'nik' => '3172014455660005', 'universitas' => 'Universitas Indonesia', 'ipk' => 3.61,
            'upa_gelombang' => 'Gelombang II 2025', 'tahun_lulus_upa' => 2025, 'status_verifikasi' => 'menunggu',
        ]);
        VerifikasiChecklist::insert([
            ['checkable_type' => CalonAdvokat::class, 'checkable_id' => $maya->id, 'label' => 'Data profil auto-populate dari admisi', 'is_checked' => true, 'created_at' => now(), 'updated_at' => now()],
            ['checkable_type' => CalonAdvokat::class, 'checkable_id' => $maya->id, 'label' => 'Status keanggotaan DPC aktif', 'is_checked' => true, 'created_at' => now(), 'updated_at' => now()],
            ['checkable_type' => CalonAdvokat::class, 'checkable_id' => $maya->id, 'label' => 'Sertifikat Lulus UPA terverifikasi', 'is_checked' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
        Lamaran::create(['calon_advokat_id' => $maya->id, 'lowongan_id' => $lowonganWibisono->id, 'status' => 'interview', 'tanggal_lamar' => now()->subDays(4)]);

        $bagus = $makeCalon('Bagus Setiawan, S.H.', 'bagus@peradijakbar.test', 'CA-2025-0349', [
            'nik' => '3174012233440006', 'universitas' => 'Universitas Trisakti', 'ipk' => 3.42,
            'upa_gelombang' => 'Gelombang II 2025', 'tahun_lulus_upa' => 2025, 'status_verifikasi' => 'perlu_perbaikan',
        ]);
        VerifikasiChecklist::insert([
            ['checkable_type' => CalonAdvokat::class, 'checkable_id' => $bagus->id, 'label' => 'Sertifikat PKPA cocok data admisi', 'is_checked' => true, 'created_at' => now(), 'updated_at' => now()],
            ['checkable_type' => CalonAdvokat::class, 'checkable_id' => $bagus->id, 'label' => 'Sertifikat Lulus UPA terverifikasi', 'is_checked' => true, 'created_at' => now(), 'updated_at' => now()],
            ['checkable_type' => CalonAdvokat::class, 'checkable_id' => $bagus->id, 'label' => 'Pasfoto latar merah tidak sesuai', 'is_checked' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
        Lamaran::create(['calon_advokat_id' => $bagus->id, 'lowongan_id' => $lowonganWibisono->id, 'status' => 'review_cv', 'tanggal_lamar' => now()->subDays(6)]);

        $laras = $makeCalon('Laras Nuraini, S.H.', 'laras@peradijakbar.test', 'CA-2026-0072', [
            'nik' => '3173015566770007', 'universitas' => 'Universitas Tarumanagara', 'ipk' => 3.70,
            'upa_gelombang' => 'Gelombang I 2026', 'tahun_lulus_upa' => 2026, 'status_verifikasi' => 'menunggu',
        ]);
        VerifikasiChecklist::insert([
            ['checkable_type' => CalonAdvokat::class, 'checkable_id' => $laras->id, 'label' => 'Sertifikat PKPA cocok data admisi', 'is_checked' => true, 'created_at' => now(), 'updated_at' => now()],
            ['checkable_type' => CalonAdvokat::class, 'checkable_id' => $laras->id, 'label' => 'Sertifikat Lulus UPA terverifikasi', 'is_checked' => true, 'created_at' => now(), 'updated_at' => now()],
            ['checkable_type' => CalonAdvokat::class, 'checkable_id' => $laras->id, 'label' => 'Ijazah S.H. terbaca jelas', 'is_checked' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
        Lamaran::create(['calon_advokat_id' => $laras->id, 'lowongan_id' => $lowonganWibisono->id, 'status' => 'review_cv', 'tanggal_lamar' => now()->subDays(2)]);

        // ---- Prasetya & Co. applicant, not accepted ----
        $rina = $makeCalon('Rina Marlina, S.H.', 'rina@peradijakbar.test', 'CA-2025-0201', [
            'nik' => '3171019988770008', 'universitas' => 'Universitas Trisakti', 'ipk' => 3.20,
            'upa_gelombang' => 'Gelombang I 2025', 'tahun_lulus_upa' => 2025,
        ]);
        Lamaran::create(['calon_advokat_id' => $rina->id, 'lowongan_id' => $lowonganWibisono->id, 'status' => 'tidak_lanjut', 'tanggal_lamar' => now()->subDays(30)]);

        // ---- Andi's own application history (for "Lamaran Saya") ----
        Lamaran::create(['calon_advokat_id' => $andi->id, 'lowongan_id' => $lowonganWibisono->id, 'status' => 'diterima', 'tanggal_lamar' => now()->subMonths(14)->subDays(3)]);

        // ---- Santika, Hartono & Partners pemagang (for compliance %) ----
        $indra = $makeCalon('Indra Kurniawan, S.H.', 'indra@peradijakbar.test', 'CA-2025-0155', [
            'nik' => '3171017788990009', 'universitas' => 'Universitas Indonesia', 'ipk' => 3.50,
            'upa_gelombang' => 'Gelombang I 2025', 'tahun_lulus_upa' => 2025,
            'law_firm_id' => $santika->id, 'advokat_pendamping_id' => $pendampingSantika->id,
            'bidang_penempatan' => 'Litigasi Korporasi', 'tanggal_mulai_magang' => now()->subMonths(16),
        ]);
        $seedLogbook($indra, 16, 0.87, $pendampingSantika);

        // ---- Dewi Anggraini: lulus audit, 24/24 bulan ----
        $dewi = $makeCalon('Dewi Anggraini, S.H.', 'dewi@peradijakbar.test', 'CA-2024-0090', [
            'nik' => '3171011122330010', 'universitas' => 'Universitas Indonesia', 'ipk' => 3.75,
            'upa_gelombang' => 'Gelombang I 2024', 'tahun_lulus_upa' => 2024,
            'law_firm_id' => $santika->id, 'advokat_pendamping_id' => $pendampingSantika->id,
            'bidang_penempatan' => 'Litigasi Korporasi', 'tanggal_mulai_magang' => now()->subMonths(24),
            'status_keanggotaan' => 'nonaktif',
        ]);
        $seedLogbook($dewi, 24, 1.0, $pendampingSantika);
        AuditAkhir::create(['calon_advokat_id' => $dewi->id, 'status' => 'lulus_audit', 'catatan' => 'Logbook lengkap, sertifikat terbit.', 'tanggal_audit' => now()->subDays(5)]);
        foreach ($berkasTemplate($dewi, [
            5 => ['jenis' => 'sertifikat_selesai_magang', 'sumber' => 'Magang · diterbitkan kantor hukum', 'status' => 'lengkap', 'ukuran' => '0,4 MB'],
            6 => ['jenis' => 'surat_rekomendasi_dpc', 'sumber' => 'Magang · setelah audit akhir', 'status' => 'lengkap', 'ukuran' => '0,3 MB'],
        ]) as $b) {
            BerkasSumpah::create(array_merge(['calon_advokat_id' => $dewi->id], $b));
        }

        // ---- Yoga Permana: 24/24 bulan, berkas kurang ----
        $yoga = $makeCalon('Yoga Permana, S.H.', 'yoga@peradijakbar.test', 'CA-2024-0102', [
            'nik' => '3171014455660011', 'universitas' => 'Universitas Trisakti', 'ipk' => 3.40,
            'upa_gelombang' => 'Gelombang II 2024', 'tahun_lulus_upa' => 2024,
            'law_firm_id' => $ardiansyah->id, 'bidang_penempatan' => 'Hukum Keluarga',
            'tanggal_mulai_magang' => now()->subMonths(24), 'status_keanggotaan' => 'nonaktif',
        ]);
        $seedLogbook($yoga, 24, 0.64, null);
        AuditAkhir::create(['calon_advokat_id' => $yoga->id, 'status' => 'berkas_kurang', 'catatan' => 'Sertifikat selesai magang belum diunggah.']);

        // ---- LBH Trisakti pemagang (for compliance %) ----
        $sinta = $makeCalon('Sinta Wulandari, S.H.', 'sinta@peradijakbar.test', 'CA-2025-0233', [
            'nik' => '3171016677880012', 'universitas' => 'Universitas Trisakti', 'ipk' => 3.45,
            'upa_gelombang' => 'Gelombang II 2025', 'tahun_lulus_upa' => 2025,
            'law_firm_id' => $lbhTrisakti->id, 'bidang_penempatan' => 'Bantuan Hukum',
            'tanggal_mulai_magang' => now()->subMonths(9),
        ]);
        $seedLogbook($sinta, 9, 0.78, null);

        // ---- Admin DPC login ----
        User::create([
            'name' => 'Sekretariat DPC', 'email' => 'admin@peradijakbar.test',
            'password' => $password, 'role' => 'admin_dpc', 'email_verified_at' => now(),
        ]);
    }
}
