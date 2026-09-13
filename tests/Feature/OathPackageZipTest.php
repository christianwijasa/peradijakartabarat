<?php

namespace Tests\Feature;

use App\Models\AuditAkhir;
use App\Models\BerkasSumpah;
use App\Models\CalonAdvokat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OathPackageZipTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_download_package_without_lulus_audit(): void
    {
        $user = User::factory()->create(['role' => 'calon_advokat']);
        $calon = CalonAdvokat::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('calon.berkas.package.download'));

        $response->assertStatus(403);
    }

    public function test_cannot_download_package_without_required_docs(): void
    {
        $user = User::factory()->create(['role' => 'calon_advokat']);
        $calon = CalonAdvokat::factory()->create(['user_id' => $user->id]);

        AuditAkhir::create([
            'calon_advokat_id' => $calon->id,
            'status' => 'lulus_audit',
            'catatan' => 'Lulus',
            'tanggal_audit' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('calon.berkas.package.download'));

        $response->assertStatus(403);
    }

    public function test_can_download_package_when_lulus_audit_and_docs_complete(): void
    {
        $user = User::factory()->create(['role' => 'calon_advokat']);
        $calon = CalonAdvokat::factory()->create(['user_id' => $user->id]);

        AuditAkhir::create([
            'calon_advokat_id' => $calon->id,
            'status' => 'lulus_audit',
            'catatan' => 'Lulus',
            'tanggal_audit' => now(),
        ]);

        \Storage::fake('local');
        
        foreach (['sertifikat_pkpa', 'sertifikat_lulus_upa', 'ijazah_transkrip'] as $jenis) {
            $path = 'berkas/test-'.$jenis.'.pdf';
            \Storage::put($path, 'test content');
            
            BerkasSumpah::create([
                'calon_advokat_id' => $calon->id,
                'jenis' => $jenis,
                'status' => 'lengkap',
                'file_path' => $path,
                'sumber' => 'Test',
                'ukuran' => '1 KB',
            ]);
        }

        $response = $this->actingAs($user)->get(route('calon.berkas.package.download'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/zip');
    }

    public function test_package_visibility_gate_on_berkas_page(): void
    {
        $user = User::factory()->create(['role' => 'calon_advokat']);
        $calon = CalonAdvokat::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('calon.berkas'));
        $response->assertStatus(200);
        $response->assertSee('belum tersedia');

        AuditAkhir::create([
            'calon_advokat_id' => $calon->id,
            'status' => 'lulus_audit',
            'catatan' => 'Lulus',
            'tanggal_audit' => now(),
        ]);

        \Storage::fake('local');
        
        foreach (['sertifikat_pkpa', 'sertifikat_lulus_upa', 'ijazah_transkrip'] as $jenis) {
            $path = 'berkas/test-'.$jenis.'.pdf';
            \Storage::put($path, 'test content');
            
            BerkasSumpah::create([
                'calon_advokat_id' => $calon->id,
                'jenis' => $jenis,
                'status' => 'lengkap',
                'file_path' => $path,
                'sumber' => 'Test',
                'ukuran' => '1 KB',
            ]);
        }

        $response = $this->actingAs($user)->get(route('calon.berkas'));
        $response->assertStatus(200);
        $response->assertSee('Unduh paket ZIP');
    }
}
