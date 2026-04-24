<?php

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

    // Tasks (nested under workspace + project)
    Route::apiResource('workspaces/{workspace}/projects/{project}/tasks', TaskController::class);

    // Kanban move endpoint
    Route::patch(
        'workspaces/{workspace}/projects/{project}/tasks/{task}/move',
        [TaskController::class, 'move']
    )->name('tasks.move');
});
