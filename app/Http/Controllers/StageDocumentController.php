<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStageDocumentRequest;
use App\Models\ActivityLog;
use App\Models\ProjectStage;
use App\Models\StageDocument;
use App\Models\User;
use App\Notifications\ProjectActivityNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StageDocumentController extends Controller
{
    public function store(StoreStageDocumentRequest $request, ProjectStage $projectStage): RedirectResponse
    {
        $file = $request->file('document');
        $path = $file->store('projects/'.$projectStage->project_id.'/stages/'.$projectStage->id, 'local');
        $document = $projectStage->documents()->create([
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => $request->user()->id,
        ]);
        ActivityLog::create([
            'project_id' => $projectStage->project_id,
            'user_id' => $request->user()->id,
            'action' => 'document_uploaded',
            'description' => 'Dokumen '.$document->original_name.' diunggah pada tahapan '.$projectStage->name.'.',
        ]);

        $recipients = collect([$projectStage->project->client])
            ->merge($projectStage->project->pics)
            ->filter(fn (User $user): bool => $user->isNot($request->user()))
            ->unique('id');
        Notification::send($recipients, new ProjectActivityNotification(
            'Dokumen baru',
            $document->original_name.' ditambahkan pada tahapan '.$projectStage->name.'.',
            route('projects.show', $projectStage->project).'#stage-'.$projectStage->id,
            'bi-file-earmark-arrow-down',
        ));

        return back()->with('success', 'Dokumen berhasil diunggah.');
    }

    public function download(Request $request, StageDocument $stageDocument): StreamedResponse
    {
        abort_unless($request->user()->can('view', $stageDocument->stage), 404);

        abort_unless(Storage::disk('local')->exists($stageDocument->file_path), 404);

        return Storage::disk('local')->download($stageDocument->file_path, $stageDocument->original_name, [
            'Content-Type' => $stageDocument->mime_type ?? 'application/octet-stream',
        ]);
    }

    public function destroy(Request $request, StageDocument $stageDocument): RedirectResponse
    {
        $this->authorize('update', $stageDocument->stage);
        $projectId = $stageDocument->stage->project_id;
        $stageName = $stageDocument->stage->name;
        $name = $stageDocument->original_name;
        Storage::disk('local')->delete($stageDocument->file_path);
        $stageDocument->delete();
        ActivityLog::create([
            'project_id' => $projectId,
            'user_id' => $request->user()->id,
            'action' => 'document_deleted',
            'description' => 'Dokumen '.$name.' dihapus dari tahapan '.$stageName.'.',
        ]);

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}
