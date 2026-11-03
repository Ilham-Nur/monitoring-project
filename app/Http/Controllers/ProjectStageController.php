<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectStageRequest;
use App\Http\Requests\UpdateProjectStageRequest;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\ProjectStage;
use App\Services\ProjectScheduleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectStageController extends Controller
{
    public function store(StoreProjectStageRequest $request, Project $project, ProjectScheduleService $schedule): RedirectResponse
    {
        $this->authorize('update', $project);

        DB::transaction(function () use ($request, $project, $schedule): void {
            $project->stages()->create([
                ...$request->validated(),
                'sort_order' => ((int) $project->stages()->max('sort_order')) + 1,
                'planned_start' => $project->end_date,
                'planned_end' => $project->end_date,
            ]);
            $schedule->recalculate($project);
            $schedule->refreshProjectStatus($project);
            ActivityLog::create([
                'project_id' => $project->id,
                'user_id' => $request->user()->id,
                'action' => 'stage_created',
                'description' => 'Tahapan baru ditambahkan: '.$request->string('name').'.',
            ]);
        });

        return back()->with('success', 'Tahapan berhasil ditambahkan dan timeline dihitung ulang.');
    }

    public function update(UpdateProjectStageRequest $request, ProjectStage $projectStage, ProjectScheduleService $schedule): RedirectResponse
    {
        $durationChanged = $request->user()->isAdmin()
            && $projectStage->duration_days !== $request->integer('duration_days');

        DB::transaction(function () use ($request, $projectStage, $schedule, $durationChanged): void {
            $data = $request->safe()->only(['actual_start', 'actual_end']);

            if ($request->user()->isAdmin()) {
                $data = [...$data, ...$request->safe()->only(['name', 'duration_days'])];
            }

            $projectStage->update($data);

            if ($durationChanged) {
                $schedule->recalculate($projectStage->project);
            }

            ActivityLog::create([
                'project_id' => $projectStage->project_id,
                'user_id' => $request->user()->id,
                'action' => 'stage_updated',
                'description' => 'Tahapan '.$projectStage->name.' diperbarui.',
            ]);
        });

        return back()->with('success', 'Tahapan berhasil diperbarui.');
    }

    public function destroy(ProjectStage $projectStage, ProjectScheduleService $schedule): RedirectResponse
    {
        $this->authorize('delete', $projectStage);
        $project = $projectStage->project;
        $paths = $projectStage->documents()->pluck('file_path');

        DB::transaction(function () use ($projectStage, $project, $schedule): void {
            $stageName = $projectStage->name;
            $projectStage->delete();

            foreach ($project->stages()->orderBy('sort_order')->get() as $index => $stage) {
                $stage->update(['sort_order' => $index + 1]);
            }

            $schedule->recalculate($project);
            $schedule->refreshProjectStatus($project);
            ActivityLog::create([
                'project_id' => $project->id,
                'user_id' => request()->user()->id,
                'action' => 'stage_deleted',
                'description' => 'Tahapan '.$stageName.' dihapus.',
            ]);
        });

        Storage::disk('local')->delete($paths->all());

        return back()->with('success', 'Tahapan berhasil dihapus dan timeline dihitung ulang.');
    }
}
