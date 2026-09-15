<?php

namespace Tests\Feature\Admin;

use App\Models\CandidateAdvocate;
use App\Models\LawFirm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrantsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_registrants_directory(): void
    {
        $admin = User::factory()->create(['role' => 'admin_dpc']);
        $user = User::factory()->create(['role' => 'calon_advokat', 'name' => 'Budi Calon']);
        CandidateAdvocate::create([
            'user_id' => $user->id,
            'candidate_code' => 'CA-2026-0001',
            'verification_status' => 'PENDING',
        ]);
        LawFirm::create(['name' => 'Kantor Demo', 'verification_status' => 'PENDING']);

        $this->actingAs($admin)
            ->get(route('admin.registrants', ['tab' => 'candidate']))
            ->assertOk()
            ->assertSee('Budi Calon')
            ->assertSee('CA-2026-0001');

        $this->actingAs($admin)
            ->get(route('admin.registrants', ['tab' => 'firm']))
            ->assertOk()
            ->assertSee('Kantor Demo');
    }

    public function test_non_admin_cannot_view_registrants_directory(): void
    {
        $user = User::factory()->create(['role' => 'calon_advokat']);

        $this->actingAs($user)
            ->get(route('admin.registrants'))
            ->assertForbidden();
    }
}
