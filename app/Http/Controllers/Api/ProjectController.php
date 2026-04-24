<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * GET /api/workspaces/{workspace}/projects
     * List all projects in a workspace
     */
    public function index(Request $request, Workspace $workspace): ProjectCollection
    {
        $this->authorizeWorkspaceAccess($request, $workspace);

        $projects = $workspace->projects()
            ->with(['creator:id,name', 'workspace:id,name'])
            ->withCount('tasks')
            ->latest()
            ->get();

        return new ProjectCollection($projects);
    }

    /**
     * POST /api/workspaces/{workspace}/projects
     * Create a new project inside a workspace
     */
    public function store(StoreProjectRequest $request, Workspace $workspace): JsonResponse
    {
        $this->authorizeWorkspaceAccess($request, $workspace);

        $project = $workspace->projects()->create([
            'created_by'  => $request->user()->id,
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => $request->status ?? 'active',
            'due_date'    => $request->due_date,
        ]);

        $project->load(['creator:id,name', 'workspace:id,name']);

        return response()->json([
            'message' => 'Project created successfully.',
            'data'    => new ProjectResource($project),
        ], 201);
    }

    /**
     * GET /api/workspaces/{workspace}/projects/{project}
     * Show a single project with its tasks
     */
    public function show(Request $request, Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorizeWorkspaceAccess($request, $workspace);
        $this->ensureProjectBelongsToWorkspace($project, $workspace);

        $project->load([
            'creator:id,name',
            'workspace:id,name',
            'rootTasks' => fn($q) => $q->with(['assignee:id,name', 'subtasks']),
        ])->loadCount('tasks');

        return response()->json(['data' => new ProjectResource($project)]);
    }

    /**
     * PUT /api/workspaces/{workspace}/projects/{project}
     * Update project details
     */
    public function update(UpdateProjectRequest $request, Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorizeWorkspaceAccess($request, $workspace);
        $this->ensureProjectBelongsToWorkspace($project, $workspace);

        $project->update($request->validated());

        return response()->json([
            'message' => 'Project updated.',
            'data'    => new ProjectResource($project->fresh(['creator:id,name', 'workspace:id,name'])),
        ]);
    }

    /**
     * DELETE /api/workspaces/{workspace}/projects/{project}
     * Delete a project and all its tasks
     */
    public function destroy(Request $request, Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorizeWorkspaceAccess($request, $workspace);
        $this->ensureProjectBelongsToWorkspace($project, $workspace);

        $project->delete();

        return response()->json(['message' => 'Project deleted.']);
    }

    // ── Private helpers ───────────────────────────────────────────────

    private function authorizeWorkspaceAccess(Request $request, Workspace $workspace): void
    {
        $user     = $request->user();
        $isOwner  = $workspace->owner_id === $user->id;
        $isMember = $workspace->members()->where('user_id', $user->id)->exists();

        if (!$isOwner && !$isMember) {
            abort(403, 'You are not a member of this workspace.');
        }
    }

    private function ensureProjectBelongsToWorkspace(Project $project, Workspace $workspace): void
    {
        if ($project->workspace_id !== $workspace->id) {
            abort(404, 'Project not found in this workspace.');
        }
    }
}
