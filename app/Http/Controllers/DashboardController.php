<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectStage;
use App\Models\StageDocument;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $visibleProjects = Project::query()->visibleTo($user);

        $summary = [
            'active' => (clone $visibleProjects)->where('status', 'active')->count(),
            'completed' => (clone $visibleProjects)->where('status', 'completed')->count(),
            'overdue' => (clone $visibleProjects)->whereHas('stages', fn (Builder $query): Builder => $query
                ->where('is_completed', false)
                ->whereDate('planned_end', '<', today()))->count(),
            'overdue_stages' => ProjectStage::query()
                ->whereHas('project', fn (Builder $query): Builder => $query->visibleTo($user))
                ->where('is_completed', false)
                ->whereDate('planned_end', '<', today())
                ->count(),
        ];

        $projects = Project::query()
            ->visibleTo($user)
            ->with(['client', 'pics'])
            ->withCount(['stages', 'stages as completed_stages_count' => fn (Builder $query): Builder => $query->where('is_completed', true)])
            ->orderByRaw("case when status = 'active' then 0 else 1 end")
            ->orderBy('end_date')
            ->limit(8)
            ->get();

        $upcomingStages = ProjectStage::query()
            ->whereHas('project', fn (Builder $query): Builder => $query->visibleTo($user))
            ->with('project')
            ->where('is_completed', false)
            ->orderBy('planned_end')
            ->limit(6)
            ->get();

        $recentDocuments = StageDocument::query()
            ->whereHas('stage.project', fn (Builder $query): Builder => $query->visibleTo($user))
            ->with(['stage.project', 'uploader'])
            ->latest()
            ->limit(6)
            ->get();

        return view('dashboard.index', compact('summary', 'projects', 'upcomingStages', 'recentDocuments'));
    }
}
