<?php

namespace Database\Factories;

use App\Models\ProjectStage;
use App\Models\StageDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StageDocument>
 */
class StageDocumentFactory extends Factory
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
            'original_name' => 'dokumen.pdf',
            'file_path' => 'projects/testing/dokumen.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'uploaded_by' => User::factory()->pic(),
        ];
    }
}
