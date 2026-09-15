<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteMaintenanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_site_is_reachable_when_maintenance_env_is_off(): void
    {
        config(['app.site_maintenance' => false]);

        $this->get('/login')->assertOk();
    }

    public function test_site_returns_503_for_all_requests_when_maintenance_env_is_on(): void
    {
        config([
            'app.site_maintenance' => true,
            'app.site_maintenance_message' => 'Deploy sedang berjalan.',
        ]);

        $user = User::factory()->create(['role' => 'admin_dpc']);

        $this->get('/login')
            ->assertStatus(503)
            ->assertSee('Deploy sedang berjalan.');

        $this->actingAs($user)
            ->get(route('admin.verification'))
            ->assertStatus(503);
    }
}
