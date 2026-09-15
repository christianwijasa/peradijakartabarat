<?php

namespace Tests\Feature\Admin;

use App\Models\CandidateAdvocate;
use App\Models\User;
use App\Support\CandidateVerificationChecklist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CandidateVerificationChecklistTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_candidate_verification_review_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin_dpc']);
        $user = User::factory()->create(['role' => 'calon_advokat']);
        $ca = CandidateAdvocate::create([
            'user_id' => $user->id,
            'candidate_code' => 'CA-2026-0098',
            'membership_status' => 'ACTIVE',
            'verification_status' => 'PENDING',
        ]);
        CandidateVerificationChecklist::seedFor($ca);

        $this->actingAs($admin)
            ->get(route('admin.verification.candidate.show', $ca))
            ->assertOk()
            ->assertSee('Review berkas admisi')
            ->assertSee('Setujui berkas ini');
    }

    public function test_admin_can_approve_and_reject_individual_checklist_items(): void
    {
        $admin = User::factory()->create(['role' => 'admin_dpc']);
        $user = User::factory()->create(['role' => 'calon_advokat']);
        $ca = CandidateAdvocate::create([
            'user_id' => $user->id,
            'candidate_code' => 'CA-2026-0099',
            'membership_status' => 'ACTIVE',
            'verification_status' => 'PENDING',
        ]);
        CandidateVerificationChecklist::seedFor($ca);

        $item = $ca->checklistItems()->where('label', 'Sertifikat PKPA cocok data admisi')->firstOrFail();
        $item->update(['document_url' => 'https://drive.google.com/file/d/example/view']);

        $this->actingAs($admin)
            ->from(route('admin.verification.candidate.show', $ca))
            ->post(route('admin.verification.checklist.tolak', $item), [
                'admin_note' => 'Nomor sertifikat tidak cocok dengan NIK.',
            ])
            ->assertRedirect();

        $item->refresh();
        $ca->refresh();
        $this->assertFalse($item->is_checked);
        $this->assertSame('Nomor sertifikat tidak cocok dengan NIK.', $item->admin_note);
        $this->assertSame('NEEDS_CORRECTION', $ca->verification_status);

        $this->actingAs($admin)
            ->post(route('admin.verification.checklist.setujui', $item))
            ->assertRedirect();

        $item->refresh();
        $this->assertTrue($item->is_checked);
        $this->assertNull($item->admin_note);
    }

    public function test_admin_cannot_verify_admission_until_all_items_approved(): void
    {
        $admin = User::factory()->create(['role' => 'admin_dpc']);
        $user = User::factory()->create(['role' => 'calon_advokat']);
        $ca = CandidateAdvocate::create([
            'user_id' => $user->id,
            'candidate_code' => 'CA-2026-0100',
            'national_id_number' => '3201010101010001',
            'university' => 'Universitas Indonesia',
            'membership_status' => 'ACTIVE',
            'verification_status' => 'PENDING',
        ]);
        CandidateVerificationChecklist::seedFor($ca);
        CandidateVerificationChecklist::syncProfileItem($ca->fresh());

        $this->actingAs($admin)
            ->post(route('admin.verification.candidate.setujui', $ca))
            ->assertStatus(422);

        $ca->checklistItems()->update(['is_checked' => true]);

        $this->actingAs($admin)
            ->post(route('admin.verification.candidate.setujui', $ca))
            ->assertRedirect();

        $this->assertSame('VERIFIED', $ca->fresh()->verification_status);
    }
}
