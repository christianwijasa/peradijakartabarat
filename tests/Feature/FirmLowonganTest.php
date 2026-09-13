<?php

namespace Tests\Feature;

use App\Models\AdvokatPendamping;
use App\Models\LawFirm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FirmLowonganTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_firm_can_create_lowongan(): void
    {
        $firm = LawFirm::factory()->create(['status_verifikasi' => 'terverifikasi']);
        $pendamping = AdvokatPendamping::factory()->create(['law_firm_id' => $firm->id]);
        $user = User::factory()->create(['role' => 'law_firm']);
        $pendamping->update(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('firm.lowongan.store'), [
            'judul' => 'Magang Test',
            'deskripsi' => 'Deskripsi test',
            'bidang' => ['Litigasi', 'Perdata'],
            'kuota' => 5,
        ]);

        $response->assertRedirect(route('firm.lowongan.index'));
        $this->assertDatabaseHas('lowongans', [
            'law_firm_id' => $firm->id,
            'judul' => 'Magang Test',
            'kuota' => 5,
            'status' => 'aktif',
        ]);
    }

    public function test_unverified_firm_cannot_create_lowongan(): void
    {
        $firm = LawFirm::factory()->create(['status_verifikasi' => 'menunggu']);
        $pendamping = AdvokatPendamping::factory()->create(['law_firm_id' => $firm->id]);
        $user = User::factory()->create(['role' => 'law_firm']);
        $pendamping->update(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('firm.lowongan.store'), [
            'judul' => 'Magang Test',
            'deskripsi' => 'Deskripsi test',
            'bidang' => ['Litigasi'],
            'kuota' => 5,
        ]);

        $response->assertStatus(403);
    }

    public function test_firm_can_toggle_lowongan_status(): void
    {
        $firm = LawFirm::factory()->create(['status_verifikasi' => 'terverifikasi']);
        $pendamping = AdvokatPendamping::factory()->create(['law_firm_id' => $firm->id]);
        $user = User::factory()->create(['role' => 'law_firm']);
        $pendamping->update(['user_id' => $user->id]);

        $lowongan = $firm->lowongans()->create([
            'judul' => 'Test',
            'deskripsi' => 'Test',
            'kuota' => 5,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($user)->post(route('firm.lowongan.toggle', $lowongan));

        $response->assertRedirect();
        $this->assertDatabaseHas('lowongans', [
            'id' => $lowongan->id,
            'status' => 'nonaktif',
        ]);
    }
}
