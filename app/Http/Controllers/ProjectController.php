<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\User;
use App\Services\ProjectScheduleService;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Project::class);

        $projects = Project::query()
            ->visibleTo($request->user())
            ->with(['client', 'type', 'pics'])
            ->withCount(['stages', 'stages as completed_stages_count' => fn (Builder $query): Builder => $query->where('is_completed', true)])
            ->when($request->filled('search'), fn (Builder $query): Builder => $query->where(function (Builder $searchQuery) use ($request): void {
                $term = $request->string('search')->toString();
                $searchQuery->where('code', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%")
                    ->orWhereHas('client', fn (Builder $clientQuery): Builder => $clientQuery->where('company_name', 'like', "%{$term}%"));
            }))
            ->when($request->filled('status'), fn (Builder $query): Builder => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $clients = collect();
        $pics = collect();
        $projectTypes = collect();

        if ($request->user()->isAdmin()) {
            $clients = User::where('role', 'client')->where('is_active', true)->orderBy('company_name')->get();
            $pics = User::where('role', 'pic')->where('is_active', true)->orderBy('name')->get();
            $projectTypes = ProjectType::where('is_active', true)->with('stages')->orderBy('name')->get();
        }

        return view('project.index', compact('projects', 'clients', 'pics', 'projectTypes'));
    }

    public function store(StoreProjectRequest $request, ProjectScheduleService $schedule): RedirectResponse
    {
        $projectType = ProjectType::with('stages')->findOrFail($request->integer('project_type_id'));

        if ($projectType->stages->isEmpty()) {
            throw ValidationException::withMessages(['project_type_id' => 'Tipe project belum memiliki tahapan.']);
        }

        $project = DB::transaction(function () use ($request, $projectType, $schedule): Project {
            $project = Project::create([
                ...$request->safe()->only(['code', 'name', 'client_id', 'project_type_id', 'description', 'start_date']),
                'end_date' => $request->date('start_date'),
                'status' => 'active',
                'created_by' => $request->user()->id,
            ]);
            $project->pics()->sync($request->validated('pic_ids'));
            $schedule->copyTemplateStages($project, $projectType);
            ActivityLog::create([
                'project_id' => $project->id,
                'user_id' => $request->user()->id,
                'action' => 'project_created',
                'description' => 'Project dibuat dengan '.$projectType->stages->count().' tahapan.',
            ]);

            return $project;
        });

        return redirect()->route('projects.show', $project)->with('success', 'Project berhasil dibuat.');
    }

    public function show(Request $request, Project $project): View
    {
        abort_unless($request->user()->can('view', $project), 404);
        $project->load([
            'client', 'type', 'pics', 'creator',
            'stages.documents.uploader', 'stages.comments.user', 'activities.user',
        ])->loadCount(['stages', 'stages as completed_stages_count' => fn (Builder $query): Builder => $query->where('is_completed', true)]);

        $timelineStart = collect([
            $project->stages->min('planned_start'),
            $project->stages->min('actual_start'),
        ])->filter()->min() ?? $project->start_date;
        $timelineEnd = collect([
            $project->stages->max('planned_end'),
            $project->stages->max('actual_end'),
            $project->stages->max('actual_start'),
            $project->stages->whereNotNull('actual_start')->whereNull('actual_end')->isNotEmpty() ? today() : null,
        ])->filter()->max() ?? $project->end_date;
        $timelineDates = collect(CarbonPeriod::create($timelineStart, $timelineEnd));

        return view('project.show', compact('project', 'timelineStart', 'timelineDates'));
    }

    public function update(UpdateProjectRequest $request, Project $project, ProjectScheduleService $schedule): RedirectResponse
    {
        $this->authorize('update', $project);
        $startDateChanged = $project->start_date->toDateString() !== $request->date('start_date')->toDateString();

        DB::transaction(function () use ($request, $project, $schedule, $startDateChanged): void {
            $project->update($request->safe()->only(['code', 'name', 'client_id', 'description', 'start_date']));
            $project->pics()->sync($request->validated('pic_ids'));

            if ($startDateChanged) {
                $schedule->recalculate($project);
            }

            ActivityLog::create([
                'project_id' => $project->id,
                'user_id' => $request->user()->id,
                'action' => 'project_updated',
                'description' => 'Informasi project diperbarui.',
            ]);
        });

        return back()->with('success', 'Project berhasil diperbarui.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);
        $paths = $project->stages()->with('documents')->get()->flatMap->documents->pluck('file_path');
        $project->delete();
        Storage::disk('local')->delete($paths->all());

        return redirect()->route('projects.index')->with('success', 'Project dan seluruh datanya berhasil dihapus.');
    }
}
