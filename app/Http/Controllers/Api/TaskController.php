<?php

namespace App\Http\Controllers\Api;

use App\Jobs\SendTaskNotification;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * GET /api/workspaces/{workspace}/projects/{project}/tasks
     * List all ROOT tasks (no parent) with their subtasks
     */
    public function index(Request $request, Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorizeWorkspaceAccess($request, $workspace);
        $this->ensureProjectInWorkspace($project, $workspace);

        $tasks = $project->rootTasks()
            ->with([
                'assignee:id,name',
                'creator:id,name',
                'subtasks.assignee:id,name',   // load subtasks with their assignees
            ])
            ->withCount(['subtasks', 'attachments'])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => TaskResource::collection($tasks),
        ]);
    }

    /**
     * POST /api/workspaces/{workspace}/projects/{project}/tasks
     * Create a task (or subtask if parent_id is provided)
     */
    public function store(StoreTaskRequest $request, Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorizeWorkspaceAccess($request, $workspace);
        $this->ensureProjectInWorkspace($project, $workspace);

        // If parent_id given, ensure parent belongs to this project
        if ($request->parent_id) {
            $parent = Task::findOrFail($request->parent_id);
            if ($parent->project_id !== $project->id) {
                return response()->json(['message' => 'Parent task does not belong to this project.'], 422);
            }
        }

        $task = Task::create([
            'project_id'   => $project->id,
            'workspace_id' => $workspace->id,
            'created_by'   => $request->user()->id,
            'assignee_id'  => $request->assignee_id,
            'parent_id'    => $request->parent_id,
            'title'        => $request->title,
            'description'  => $request->description,
            'status'       => $request->status ?? 'todo',
            'priority'     => $request->priority ?? 'medium',
            'due_date'     => $request->due_date,
            'sort_order'   => $request->sort_order ?? 0,
        ]);

        $task->load(['assignee:id,name,email', 'creator:id,name', 'parent:id,title', 'subtasks']);

        // Dispatch email notification if task is assigned to someone
        if ($task->assignee_id) {
            $assignee = User::find($task->assignee_id);
            SendTaskNotification::dispatch($task, $assignee);
        }

        return response()->json([
            'message' => 'Task created successfully.',
            'data'    => new TaskResource($task),
        ], 201);
    }

    /**
     * GET /api/workspaces/{workspace}/projects/{project}/tasks/{task}
     * Show a single task with all subtasks
     */
    public function show(Request $request, Workspace $workspace, Project $project, Task $task): JsonResponse
    {
        $this->authorizeWorkspaceAccess($request, $workspace);
        $this->ensureTaskInProject($task, $project);

        $task->load([
            'assignee:id,name',
            'creator:id,name',
            'parent:id,title',
            'subtasks' => fn($q) => $q->with('assignee:id,name')->withCount('attachments'),
            'attachments',
        ])->loadCount(['subtasks', 'attachments']);

        return response()->json(['data' => new TaskResource($task)]);
    }

    /**
     * PUT /api/workspaces/{workspace}/projects/{project}/tasks/{task}
     * Update a task — also used for drag-and-drop status changes
     */
    public function update(UpdateTaskRequest $request, Workspace $workspace, Project $project, Task $task): JsonResponse
    {
        $this->authorizeWorkspaceAccess($request, $workspace);
        $this->ensureTaskInProject($task, $project);

        $task->update($request->validated());

        // Send notification if assignee changed
        if ($request->has('assignee_id') && $request->assignee_id) {
            $task->load(['project']);
            $assignee = User::find($request->assignee_id);
            SendTaskNotification::dispatch($task, $assignee);
        }
        $task->load(['assignee:id,name', 'creator:id,name', 'subtasks']);

        return response()->json([
            'message' => 'Task updated.',
            'data'    => new TaskResource($task),
        ]);
    }

    /**
     * DELETE /api/workspaces/{workspace}/projects/{project}/tasks/{task}
     * Delete task and all subtasks (cascades via DB)
     */
    public function destroy(Request $request, Workspace $workspace, Project $project, Task $task): JsonResponse
    {
        $this->authorizeWorkspaceAccess($request, $workspace);
        $this->ensureTaskInProject($task, $project);

        $task->delete();

        return response()->json(['message' => 'Task deleted.']);
    }

    /**
     * PATCH /api/workspaces/{workspace}/projects/{project}/tasks/{task}/move
     * Move task to a different status (used by Kanban board drag-and-drop)
     */
    public function move(Request $request, Workspace $workspace, Project $project, Task $task): JsonResponse
    {
        $this->authorizeWorkspaceAccess($request, $workspace);
        $this->ensureTaskInProject($task, $project);

        $request->validate([
            'status'     => ['required', 'in:todo,in_progress,in_review,done'],
            'sort_order' => ['sometimes', 'integer'],
        ]);

        $task->update([
            'status'     => $request->status,
            'sort_order' => $request->sort_order ?? $task->sort_order,
        ]);

        return response()->json([
            'message' => 'Task moved.',
            'data'    => new TaskResource($task),
        ]);
    }

    // ── Private helpers ───────────────────────────────────────────────

    private function authorizeWorkspaceAccess(Request $request, Workspace $workspace): void
    {
        $user = $request->user();
        if (
            $workspace->owner_id !== $user->id &&
            !$workspace->members()->where('user_id', $user->id)->exists()
        ) {
            abort(403, 'You are not a member of this workspace.');
        }
    }

    private function ensureProjectInWorkspace(Project $project, Workspace $workspace): void
    {
        if ($project->workspace_id !== $workspace->id) {
            abort(404, 'Project not found in this workspace.');
        }
    }

    private function ensureTaskInProject(Task $task, Project $project): void
    {
        if ($task->project_id !== $project->id) {
            abort(404, 'Task not found in this project.');
        }
    }
}
