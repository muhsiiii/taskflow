<?php

use App\Http\Controllers\Api\AttachmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\WorkspaceController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);

    // Workspaces
    Route::apiResource('workspaces', WorkspaceController::class);
    Route::post('workspaces/{workspace}/members',          [WorkspaceController::class, 'addMember']);
    Route::delete('workspaces/{workspace}/members/{user}', [WorkspaceController::class, 'removeMember']);

    // Projects
    Route::apiResource('workspaces/{workspace}/projects', ProjectController::class);

    // Tasks
    Route::apiResource('workspaces/{workspace}/projects/{project}/tasks', TaskController::class);
    Route::patch(
        'workspaces/{workspace}/projects/{project}/tasks/{task}/move',
        [TaskController::class, 'move']
    )->name('tasks.move');

    // Attachments
    Route::get(
        'workspaces/{workspace}/projects/{project}/tasks/{task}/attachments',
        [AttachmentController::class, 'index']
    )->name('attachments.index');

    Route::post(
        'workspaces/{workspace}/projects/{project}/tasks/{task}/attachments',
        [AttachmentController::class, 'store']
    )->name('attachments.store');

    Route::delete(
        'workspaces/{workspace}/projects/{project}/tasks/{task}/attachments/{attachment}',
        [AttachmentController::class, 'destroy']
    )->name('attachments.destroy');
});