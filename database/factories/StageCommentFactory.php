<?php

namespace Database\Factories;

use App\Models\ProjectStage;
use App\Models\StageComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StageComment>
 */
class StageCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_stage_id' => ProjectStage::factory(),
            'user_id' => User::factory(),
            'comment' => fake()->sentence(),
        ];
    }
}
