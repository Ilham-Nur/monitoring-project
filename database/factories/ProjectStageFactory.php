<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectStage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectStage>
 */
class ProjectStageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => fake()->words(2, true),
            'sort_order' => 1,
            'duration_days' => 3,
            'planned_start' => '2026-10-01',
            'planned_end' => '2026-10-03',
            'is_completed' => false,
        ];
    }
}
