<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStageCommentRequest;
use App\Models\ActivityLog;
use App\Models\ProjectStage;
use App\Models\User;
use App\Notifications\ProjectActivityNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;

class StageCommentController extends Controller
{
    public function store(StoreStageCommentRequest $request, ProjectStage $projectStage): RedirectResponse
    {
        $projectStage->comments()->create([
            'user_id' => $request->user()->id,
            'comment' => $request->string('comment')->toString(),
        ]);
        ActivityLog::create([
            'project_id' => $projectStage->project_id,
            'user_id' => $request->user()->id,
            'action' => 'comment_added',
            'description' => 'Komentar ditambahkan pada tahapan '.$projectStage->name.'.',
        ]);

        $project = $projectStage->project->loadMissing(['client', 'pics']);
        $recipients = $request->user()->isClient()
            ? $project->pics->merge(User::where('role', 'admin')->where('is_active', true)->get())
            : collect([$project->client]);
        $recipients = $recipients->filter(fn (User $user): bool => $user->isNot($request->user()))->unique('id');
        Notification::send($recipients, new ProjectActivityNotification(
            'Komentar baru',
            $request->user()->name.' mengomentari tahapan '.$projectStage->name.'.',
            route('projects.show', $project).'#stage-'.$projectStage->id,
            'bi-chat-dots',
        ));

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}
