<?php

namespace Database\Factories;

use App\Models\ProjectType;
use App\Models\StageTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StageTemplate>
 */
class StageTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_type_id' => ProjectType::factory(),
            'name' => fake()->words(2, true),
            'duration_days' => fake()->numberBetween(1, 10),
            'sort_order' => 1,
        ];
    }
}
