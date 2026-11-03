<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = Carbon::instance(fake()->dateTimeBetween('-1 month', '+1 month'))->startOfDay();

        return [
            'code' => 'PRJ-'.fake()->unique()->numerify('####'),
            'name' => fake()->words(3, true),
            'client_id' => User::factory()->client(),
            'project_type_id' => ProjectType::factory(),
            'description' => fake()->sentence(),
            'start_date' => $startDate,
            'end_date' => $startDate->copy()->addDays(9),
            'status' => 'active',
            'created_by' => User::factory()->admin(),
        ];
    }
}
