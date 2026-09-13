<?php

namespace Tests\Feature;

use App\Models\LawFirm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FirmSelfRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_firm_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register/firm');

        $response->assertStatus(200);
    }

    public function test_new_firm_can_register(): void
    {
        $response = $this->post('/register/firm', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'nama_firm' => 'Test Law Firm',
            'alamat' => 'Jl. Test No. 123',
            'sk_kemenkumham' => 'AHU-123456',
            'kuota_maks' => 10,
            'nama_pendamping' => 'Test Pendamping, S.H.',
            'kta_nomor' => 'KTA-TEST-123',
            'pengalaman_tahun' => 10,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('firm.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'law_firm',
        ]);

        $this->assertDatabaseHas('law_firms', [
            'nama' => 'Test Law Firm',
            'status_verifikasi' => 'menunggu',
        ]);

        $this->assertDatabaseHas('advokat_pendampings', [
            'nama' => 'Test Pendamping, S.H.',
            'kta_nomor' => 'KTA-TEST-123',
        ]);

        $firm = LawFirm::where('nama', 'Test Law Firm')->first();
        $this->assertDatabaseHas('verifikasi_checklists', [
            'checkable_type' => LawFirm::class,
            'checkable_id' => $firm->id,
        ]);
    }

    public function test_unverified_firm_cannot_create_lowongan(): void
    {
        $this->post('/register/firm', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'nama_firm' => 'Test Law Firm',
            'alamat' => 'Jl. Test No. 123',
            'kuota_maks' => 10,
            'nama_pendamping' => 'Test Pendamping, S.H.',
            'kta_nomor' => 'KTA-TEST-123',
            'pengalaman_tahun' => 10,
        ]);

        $response = $this->post(route('firm.lowongan.store'), [
            'judul' => 'Test Lowongan',
            'deskripsi' => 'Test description',
            'bidang' => ['Litigasi'],
            'kuota' => 5,
        ]);

        $response->assertStatus(403);
    }

    public function test_verified_firm_can_create_lowongan(): void
    {
        $user = User::factory()->create(['role' => 'law_firm']);
        $firm = LawFirm::factory()->create(['status_verifikasi' => 'terverifikasi']);
        $pendamping = $firm->advokatPendampings()->create([
            'user_id' => $user->id,
            'nama' => 'Test Pendamping',
            'kta_nomor' => 'KTA-123',
            'kta_aktif' => true,
            'pengalaman_tahun' => 10,
        ]);

        $response = $this->actingAs($user)->post(route('firm.lowongan.store'), [
            'judul' => 'Test Lowongan',
            'deskripsi' => 'Test description',
            'bidang' => ['Litigasi'],
            'kuota' => 5,
        ]);

        $response->assertRedirect(route('firm.lowongan.index'));
        $this->assertDatabaseHas('lowongans', [
            'law_firm_id' => $firm->id,
            'judul' => 'Test Lowongan',
        ]);
    }
}
