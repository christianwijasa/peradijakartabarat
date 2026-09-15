<?php

namespace Tests\Feature\Calon;

use App\Models\CandidateAdvocate;
use App\Models\User;
use App\Support\CandidateVerificationChecklist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificationLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_calon_can_save_verification_document_link(): void
    {
        $user = User::factory()->create(['role' => 'calon_advokat']);
        $ca = CandidateAdvocate::create([
            'user_id' => $user->id,
            'candidate_code' => 'CA-2026-0001',
            'membership_status' => 'ACTIVE',
            'verification_status' => 'PENDING',
        ]);
        CandidateVerificationChecklist::seedFor($ca);

        $item = $ca->checklistItems()->where('label', 'Sertifikat PKPA cocok data admisi')->firstOrFail();

        $response = $this->actingAs($user)->post(route('calon.verifikasi.link', $item), [
            'document_url' => 'https://drive.google.com/file/d/example/view',
        ]);

        $response->assertRedirect();
        $this->assertSame(
            'https://drive.google.com/file/d/example/view',
            $item->fresh()->document_url
        );
    }
}
