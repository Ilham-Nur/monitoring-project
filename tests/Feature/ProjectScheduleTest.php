<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectType;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ProjectScheduleTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_creating_project_calculates_consecutive_calendar_day_schedule(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();
        $pic = User::factory()->pic()->create();
        $type = ProjectType::factory()->create();
        $type->stages()->createMany([
            ['name' => 'Persiapan', 'duration_days' => 3, 'sort_order' => 1],
            ['name' => 'Pemeriksaan', 'duration_days' => 2, 'sort_order' => 2],
            ['name' => 'Pelaksanaan', 'duration_days' => 4, 'sort_order' => 3],
        ]);

        $response = $this->actingAs($admin)->post(route('projects.store'), [
            'code' => 'PRJ-2026-001',
            'name' => 'Project Kalender',
            'client_id' => $client->id,
            'project_type_id' => $type->id,
            'start_date' => '2026-10-01',
            'pic_ids' => [$pic->id],
        ]);

        $project = Project::where('code', 'PRJ-2026-001')->firstOrFail();
        $response->assertRedirect(route('projects.show', $project));
        $this->assertSame('2026-10-09', $project->end_date->toDateString());
        $this->assertSame([
            ['name' => 'Persiapan', 'planned_start' => '2026-10-01', 'planned_end' => '2026-10-03'],
            ['name' => 'Pemeriksaan', 'planned_start' => '2026-10-04', 'planned_end' => '2026-10-05'],
            ['name' => 'Pelaksanaan', 'planned_start' => '2026-10-06', 'planned_end' => '2026-10-09'],
        ], $project->stages()->get()->map(fn ($stage): array => [
            'name' => $stage->name,
            'planned_start' => $stage->planned_start->toDateString(),
            'planned_end' => $stage->planned_end->toDateString(),
        ])->all());
    }

    public function test_updating_duration_recalculates_following_stages_and_project_end(): void
    {
        $admin = User::factory()->admin()->create();
        $project = Project::factory()->create(['start_date' => '2026-10-01', 'end_date' => '2026-10-05']);
        $first = $project->stages()->create(['name' => 'Pertama', 'sort_order' => 1, 'duration_days' => 3, 'planned_start' => '2026-10-01', 'planned_end' => '2026-10-03']);
        $project->stages()->create(['name' => 'Kedua', 'sort_order' => 2, 'duration_days' => 2, 'planned_start' => '2026-10-04', 'planned_end' => '2026-10-05']);

        $response = $this->actingAs($admin)->put(route('project-stages.update', $first), [
            'name' => 'Pertama',
            'duration_days' => 5,
            'actual_start' => null,
            'actual_end' => null,
        ]);

        $response->assertRedirect();
        $this->assertSame('2026-10-07', $project->fresh()->end_date->toDateString());
        $this->assertDatabaseHas('project_stages', [
            'project_id' => $project->id,
            'sort_order' => 2,
            'planned_start' => '2026-10-06 00:00:00',
            'planned_end' => '2026-10-07 00:00:00',
        ]);
    }

    public function test_timeline_includes_actual_dates_and_renders_status_colors(): void
    {
        $admin = User::factory()->admin()->create();
        $project = Project::factory()->create([
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-05',
        ]);
        $project->stages()->create([
            'name' => 'Persiapan',
            'sort_order' => 1,
            'duration_days' => 3,
            'planned_start' => '2026-10-01',
            'planned_end' => '2026-10-03',
            'actual_start' => '2026-09-30',
            'actual_end' => '2026-10-04',
        ]);
        $project->stages()->create([
            'name' => 'Penutupan',
            'sort_order' => 2,
            'duration_days' => 1,
            'planned_start' => '2026-10-05',
            'planned_end' => '2026-10-05',
        ]);

        $response = $this->actingAs($admin)->get(route('projects.show', $project));

        $response->assertOk();
        $this->assertSame('2026-09-30', $response->viewData('timelineStart')->toDateString());
        $response->assertSee('timeline-block-action-early', false);
        $response->assertSee('timeline-block-action-in', false);
        $response->assertSee('timeline-block-action-late', false);
        $response->assertSee('timeline-block-plan', false);
    }
}
