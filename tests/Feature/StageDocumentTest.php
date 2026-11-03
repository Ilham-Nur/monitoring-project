<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\StageDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StageDocumentTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_assigned_pic_uploads_document_and_client_downloads_it(): void
    {
        Storage::fake('local');
        $client = User::factory()->client()->create();
        $pic = User::factory()->pic()->create();
        $project = Project::factory()->for($client, 'client')->create();
        $project->pics()->attach($pic);
        $stage = $project->stages()->create(['name' => 'Tahapan', 'sort_order' => 1, 'duration_days' => 2, 'planned_start' => '2026-10-01', 'planned_end' => '2026-10-02']);

        $uploadResponse = $this->actingAs($pic)->post(route('stage-documents.store', $stage), [
            'document' => UploadedFile::fake()->create('laporan.pdf', 250, 'application/pdf'),
        ]);

        $document = StageDocument::firstOrFail();
        $uploadResponse->assertRedirect();
        Storage::disk('local')->assertExists($document->file_path);
        $this->actingAs($client)->get(route('stage-documents.download', $document))->assertOk()->assertDownload('laporan.pdf');
    }

    public function test_other_client_cannot_download_document(): void
    {
        Storage::fake('local');
        $otherClient = User::factory()->client()->create();
        $document = StageDocument::factory()->create();
        Storage::disk('local')->put($document->file_path, 'content');

        $response = $this->actingAs($otherClient)->get(route('stage-documents.download', $document));

        $response->assertNotFound();
    }

    public function test_deleting_project_removes_document_record_and_file(): void
    {
        Storage::fake('local');
        $admin = User::factory()->admin()->create();
        $project = Project::factory()->create();
        $stage = $project->stages()->create(['name' => 'Tahapan', 'sort_order' => 1, 'duration_days' => 1, 'planned_start' => '2026-10-01', 'planned_end' => '2026-10-01']);
        $document = StageDocument::factory()->for($stage, 'stage')->create();
        Storage::disk('local')->put($document->file_path, 'content');

        $response = $this->actingAs($admin)->delete(route('projects.destroy', $project));

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        $this->assertDatabaseMissing('stage_documents', ['id' => $document->id]);
        Storage::disk('local')->assertMissing($document->file_path);
    }
}
