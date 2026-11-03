<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ProjectProgressTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_progress_uses_completed_stage_count_and_project_finishes_at_one_hundred_percent(): void
    {
        $pic = User::factory()->pic()->create();
        $project = Project::factory()->create();
        $project->pics()->attach($pic);
        $first = $project->stages()->create(['name' => 'Pertama', 'sort_order' => 1, 'duration_days' => 3, 'planned_start' => '2026-10-01', 'planned_end' => '2026-10-03']);
        $second = $project->stages()->create(['name' => 'Kedua', 'sort_order' => 2, 'duration_days' => 2, 'planned_start' => '2026-10-04', 'planned_end' => '2026-10-05']);

        $firstResponse = $this->actingAs($pic)->post(route('stage-completions.store', $first));

        $firstResponse->assertRedirect();
        $this->assertSame(50, $project->fresh()->progressPercentage());
        $this->assertSame('active', $project->fresh()->status);

        $secondResponse = $this->actingAs($pic)->post(route('stage-completions.store', $second));

        $secondResponse->assertRedirect();
        $this->assertSame(100, $project->fresh()->progressPercentage());
        $this->assertSame('completed', $project->fresh()->status);
    }

    public function test_reopening_stage_reduces_progress_and_reactivates_project(): void
    {
        $pic = User::factory()->pic()->create();
        $project = Project::factory()->create(['status' => 'completed']);
        $project->pics()->attach($pic);
        $stage = $project->stages()->create(['name' => 'Tahapan', 'sort_order' => 1, 'duration_days' => 1, 'planned_start' => '2026-10-01', 'planned_end' => '2026-10-01', 'is_completed' => true]);

        $response = $this->actingAs($pic)->delete(route('stage-completions.destroy', $stage));

        $response->assertRedirect();
        $this->assertSame(0, $project->fresh()->progressPercentage());
        $this->assertSame('active', $project->fresh()->status);
    }
}
