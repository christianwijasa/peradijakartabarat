<?php

namespace Tests\Feature;

use App\Models\AdvokatPendamping;
use App\Models\CalonAdvokat;
use App\Models\Lamaran;
use App\Models\LawFirm;
use App\Models\Lowongan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LamaranStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_firm_can_move_lamaran_through_statuses(): void
    {
        $firm = LawFirm::factory()->create(['status_verifikasi' => 'terverifikasi']);
        $pendamping = AdvokatPendamping::factory()->create(['law_firm_id' => $firm->id]);
        $user = User::factory()->create(['role' => 'law_firm']);
        $pendamping->update(['user_id' => $user->id]);

        $lowongan = $firm->lowongans()->create([
            'judul' => 'Test',
            'deskripsi' => 'Test',
            'kuota' => 5,
        ]);

        $calon = CalonAdvokat::factory()->create();
        $lamaran = Lamaran::create([
            'calon_advokat_id' => $calon->id,
            'lowongan_id' => $lowongan->id,
            'status' => 'terkirim',
            'tanggal_lamar' => now(),
        ]);

        // Move to review_cv
        $response = $this->actingAs($user)->post(route('firm.pelamar.status', $lamaran), [
            'status' => 'review_cv',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('lamarans', ['id' => $lamaran->id, 'status' => 'review_cv']);

        // Move to interview
        $response = $this->actingAs($user)->post(route('firm.pelamar.status', $lamaran), [
            'status' => 'interview',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('lamarans', ['id' => $lamaran->id, 'status' => 'interview']);
    }

    public function test_cannot_accept_when_quota_full(): void
    {
        $firm = LawFirm::factory()->create(['status_verifikasi' => 'terverifikasi']);
        $pendamping = AdvokatPendamping::factory()->create(['law_firm_id' => $firm->id]);
        $user = User::factory()->create(['role' => 'law_firm']);
        $pendamping->update(['user_id' => $user->id]);

        $lowongan = $firm->lowongans()->create([
            'judul' => 'Test',
            'deskripsi' => 'Test',
            'kuota' => 1,
        ]);

        $calon1 = CalonAdvokat::factory()->create();
        Lamaran::create([
            'calon_advokat_id' => $calon1->id,
            'lowongan_id' => $lowongan->id,
            'status' => 'diterima',
            'tanggal_lamar' => now(),
        ]);

        $calon2 = CalonAdvokat::factory()->create();
        $lamaran2 = Lamaran::create([
            'calon_advokat_id' => $calon2->id,
            'lowongan_id' => $lowongan->id,
            'status' => 'interview',
            'tanggal_lamar' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('firm.pelamar.status', $lamaran2), [
            'status' => 'diterima',
        ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseHas('lamarans', ['id' => $lamaran2->id, 'status' => 'interview']);
    }
}
