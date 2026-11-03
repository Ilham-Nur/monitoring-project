<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ProjectStage;
use App\Models\User;
use App\Notifications\ProjectActivityNotification;
use App\Services\ProjectScheduleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class StageCompletionController extends Controller
{
    public function store(Request $request, ProjectStage $projectStage, ProjectScheduleService $schedule): RedirectResponse
    {
        $this->authorize('update', $projectStage);
        $projectStage->update([
            'actual_start' => $projectStage->actual_start ?? today(),
            'actual_end' => $projectStage->actual_end ?? today(),
            'is_completed' => true,
            'completed_at' => now(),
            'completed_by' => $request->user()->id,
        ]);
        $schedule->refreshProjectStatus($projectStage->project);
        ActivityLog::create([
            'project_id' => $projectStage->project_id,
            'user_id' => $request->user()->id,
            'action' => 'stage_completed',
            'description' => 'Tahapan '.$projectStage->name.' ditandai selesai.',
        ]);

        $recipients = $this->recipients($projectStage, $request->user());
        Notification::send($recipients, new ProjectActivityNotification(
            'Tahapan selesai',
            $projectStage->name.' pada '.$projectStage->project->name.' telah selesai.',
            route('projects.show', $projectStage->project).'#stage-'.$projectStage->id,
            'bi-check-circle',
        ));

        return back()->with('success', 'Tahapan ditandai selesai. Progres project diperbarui.');
    }

    public function destroy(Request $request, ProjectStage $projectStage, ProjectScheduleService $schedule): RedirectResponse
    {
        $this->authorize('update', $projectStage);
        $projectStage->update([
            'is_completed' => false,
            'completed_at' => null,
            'completed_by' => null,
        ]);
        $schedule->refreshProjectStatus($projectStage->project);
        ActivityLog::create([
            'project_id' => $projectStage->project_id,
            'user_id' => $request->user()->id,
            'action' => 'stage_reopened',
            'description' => 'Status selesai tahapan '.$projectStage->name.' dibatalkan.',
        ]);

        return back()->with('success', 'Status selesai tahapan dibatalkan.');
    }

    private function recipients(ProjectStage $stage, User $actor): Collection
    {
        $project = $stage->project->loadMissing(['client', 'pics']);

        return collect([$project->client])
            ->merge($project->pics)
            ->merge(User::where('role', 'admin')->where('is_active', true)->get())
            ->filter(fn (User $user): bool => $user->isNot($actor))
            ->unique('id');
    }
}
