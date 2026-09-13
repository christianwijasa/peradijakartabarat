<?php

namespace Tests\Feature;

use App\Models\CalonAdvokat;
use App\Models\LogbookEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogbookRevisiTest extends TestCase
{
    use RefreshDatabase;

    public function test_calon_can_resubmit_revisi_entry(): void
    {
        $user = User::factory()->create(['role' => 'calon_advokat']);
        $calon = CalonAdvokat::factory()->create(['user_id' => $user->id]);

        $entry = LogbookEntry::create([
            'calon_advokat_id' => $calon->id,
            'tanggal' => now(),
            'jenis_kegiatan' => 'Riset hukum',
            'jam' => 4,
            'uraian' => 'Test entry',
            'status' => 'revisi',
            'catatan_revisi' => 'Mohon perbaiki',
        ]);

        $response = $this->actingAs($user)->patch(route('calon.logbook.update', $entry->id), [
            'jenis_kegiatan' => 'Riset hukum',
            'uraian' => 'Updated entry after revision',
            'jam' => 5,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('logbook_entries', [
            'id' => $entry->id,
            'status' => 'menunggu_ttd',
            'uraian' => 'Updated entry after revision',
            'catatan_revisi' => null,
        ]);
    }

    public function test_calon_cannot_edit_non_revisi_entry(): void
    {
        $user = User::factory()->create(['role' => 'calon_advokat']);
        $calon = CalonAdvokat::factory()->create(['user_id' => $user->id]);

        $entry = LogbookEntry::create([
            'calon_advokat_id' => $calon->id,
            'tanggal' => now(),
            'jenis_kegiatan' => 'Riset hukum',
            'jam' => 4,
            'uraian' => 'Test entry',
            'status' => 'disetujui',
        ]);

        $response = $this->actingAs($user)->patch(route('calon.logbook.update', $entry->id), [
            'jenis_kegiatan' => 'Riset hukum',
            'uraian' => 'Updated entry',
            'jam' => 5,
        ]);

        $response->assertStatus(403);
    }
}
