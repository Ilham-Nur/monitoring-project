<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectType;
use Illuminate\Support\Carbon;

class ProjectScheduleService
{
    public function copyTemplateStages(Project $project, ProjectType $projectType): void
    {
        $plannedStart = $project->start_date->copy();

        foreach ($projectType->stages as $templateStage) {
            $plannedEnd = $plannedStart->copy()->addDays($templateStage->duration_days - 1);

            $project->stages()->create([
                'name' => $templateStage->name,
                'sort_order' => $templateStage->sort_order,
                'duration_days' => $templateStage->duration_days,
                'planned_start' => $plannedStart,
                'planned_end' => $plannedEnd,
            ]);

            $plannedStart = $plannedEnd->copy()->addDay();
        }

        $this->updateProjectEndDate($project);
    }

    public function recalculate(Project $project): void
    {
        $plannedStart = Carbon::parse($project->start_date);

        foreach ($project->stages()->orderBy('sort_order')->get() as $stage) {
            $plannedEnd = $plannedStart->copy()->addDays($stage->duration_days - 1);
            $stage->update([
                'planned_start' => $plannedStart,
                'planned_end' => $plannedEnd,
            ]);
            $plannedStart = $plannedEnd->copy()->addDay();
        }

        $this->updateProjectEndDate($project);
    }

    public function refreshProjectStatus(Project $project): void
    {
        $hasStages = $project->stages()->exists();
        $hasOpenStages = $project->stages()->where('is_completed', false)->exists();
        $project->update(['status' => $hasStages && ! $hasOpenStages ? 'completed' : 'active']);
    }

    private function updateProjectEndDate(Project $project): void
    {
        $lastPlannedEnd = $project->stages()->max('planned_end');
        $project->update(['end_date' => $lastPlannedEnd ?? $project->start_date]);
    }
}
