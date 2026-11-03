<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectStageController;
use App\Http\Controllers\ProjectTypeController;
use App\Http\Controllers\StageCommentController;
use App\Http\Controllers\StageCompletionController;
use App\Http\Controllers\StageDocumentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('project-types', ProjectTypeController::class)
        ->parameters(['project-types' => 'projectType'])
        ->only(['index', 'store', 'update', 'destroy']);
    Route::resource('projects', ProjectController::class)->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::post('/projects/{project}/stages', [ProjectStageController::class, 'store'])->name('project-stages.store');
    Route::put('/project-stages/{projectStage}', [ProjectStageController::class, 'update'])->name('project-stages.update');
    Route::delete('/project-stages/{projectStage}', [ProjectStageController::class, 'destroy'])->name('project-stages.destroy');
    Route::post('/project-stages/{projectStage}/completion', [StageCompletionController::class, 'store'])->name('stage-completions.store');
    Route::delete('/project-stages/{projectStage}/completion', [StageCompletionController::class, 'destroy'])->name('stage-completions.destroy');
    Route::post('/project-stages/{projectStage}/documents', [StageDocumentController::class, 'store'])->name('stage-documents.store');
    Route::get('/stage-documents/{stageDocument}/download', [StageDocumentController::class, 'download'])->name('stage-documents.download');
    Route::delete('/stage-documents/{stageDocument}', [StageDocumentController::class, 'destroy'])->name('stage-documents.destroy');
    Route::post('/project-stages/{projectStage}/comments', [StageCommentController::class, 'store'])->name('stage-comments.store');

    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::put('/notifications', [NotificationController::class, 'update'])->name('notifications.read-all');
});
