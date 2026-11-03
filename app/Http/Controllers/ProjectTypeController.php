<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectTypeRequest;
use App\Http\Requests\UpdateProjectTypeRequest;
use App\Models\ProjectType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProjectTypeController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', ProjectType::class);

        $projectTypes = ProjectType::with('stages')->withCount('projects')->latest()->get();

        return view('project-type.index', compact('projectTypes'));
    }

    public function store(StoreProjectTypeRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $projectType = ProjectType::create([
                ...$request->safe()->only(['name', 'description']),
                'is_active' => $request->boolean('is_active', true),
            ]);

            foreach ($request->validated('stages') as $index => $stage) {
                $projectType->stages()->create([
                    ...$stage,
                    'sort_order' => $index + 1,
                ]);
            }
        });

        return back()->with('success', 'Tipe project dan tahapan berhasil ditambahkan.');
    }

    public function update(UpdateProjectTypeRequest $request, ProjectType $projectType): RedirectResponse
    {
        DB::transaction(function () use ($request, $projectType): void {
            $projectType->update([
                ...$request->safe()->only(['name', 'description']),
                'is_active' => $request->boolean('is_active'),
            ]);
            $projectType->stages()->delete();

            foreach ($request->validated('stages') as $index => $stage) {
                $projectType->stages()->create([
                    ...$stage,
                    'sort_order' => $index + 1,
                ]);
            }
        });

        return back()->with('success', 'Tipe project berhasil diperbarui. Project lama tidak terpengaruh.');
    }

    public function destroy(ProjectType $projectType): RedirectResponse
    {
        $this->authorize('delete', $projectType);
        $projectType->delete();

        return back()->with('success', 'Tipe project berhasil dihapus.');
    }
}
