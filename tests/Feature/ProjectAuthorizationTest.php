<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ProjectAuthorizationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_client_can_view_own_project(): void
    {
        $client = User::factory()->client()->create();
        $project = Project::factory()->for($client, 'client')->create();

        $response = $this->actingAs($client)->get(route('projects.show', $project));

        $response->assertOk()->assertSee($project->name);
    }

    public function test_client_receives_not_found_for_another_clients_project(): void
    {
        $client = User::factory()->client()->create();
        $otherClient = User::factory()->client()->create();
        $project = Project::factory()->for($otherClient, 'client')->create();

        $response = $this->actingAs($client)->get(route('projects.show', $project));

        $response->assertNotFound();
    }

    public function test_pic_cannot_update_unassigned_project_stage(): void
    {
        $pic = User::factory()->pic()->create();
        $project = Project::factory()->create();
        $stage = $project->stages()->create(['name' => 'Tahapan', 'sort_order' => 1, 'duration_days' => 2, 'planned_start' => '2026-10-01', 'planned_end' => '2026-10-02']);

        $response = $this->actingAs($pic)->put(route('project-stages.update', $stage), [
            'actual_start' => '2026-10-01',
            'actual_end' => '2026-10-02',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('project_stages', [
            'id' => $stage->id,
            'actual_start' => '2026-10-01',
        ]);
    }

    public function test_assigned_pic_can_update_actual_dates_but_cannot_change_plan(): void
    {
        $pic = User::factory()->pic()->create();
        $project = Project::factory()->create();
        $project->pics()->attach($pic);
        $stage = $project->stages()->create(['name' => 'Tahapan', 'sort_order' => 1, 'duration_days' => 2, 'planned_start' => '2026-10-01', 'planned_end' => '2026-10-02']);

        $response = $this->actingAs($pic)->put(route('project-stages.update', $stage), [
            'name' => 'Nama Diubah',
            'duration_days' => 10,
            'actual_start' => '2026-10-01',
            'actual_end' => '2026-10-03',
        ]);

        $response->assertSessionHasErrors(['name', 'duration_days']);
        $this->assertSame('Tahapan', $stage->fresh()->name);
    }
}
