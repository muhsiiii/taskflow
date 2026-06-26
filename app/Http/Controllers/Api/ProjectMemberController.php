<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectMember\StoreProjectMemberRequest;
use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectMemberController extends Controller
{
    public function store(StoreProjectMemberRequest $request, Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorizeWorkspaceAccess($request, $workspace);
        $this->ensureProjectBelongsToWorkspace($project, $workspace);

        $user = User::where('email', $request->email)->first();

        if (! $workspace->members()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'User must be a member of the workspace before joining the project.'], 422);
        }

        if ($project->members()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'User is already added to this project.'], 422);
        }

        $project->members()->attach($user->id, ['role' => $request->role]);

        return response()->json([
            'message' => 'Project member added successfully.',
            'data'    => [
                'id'   => $user->id,
                'name' => $user->name,
                'role' => $request->role,
            ],
        ], 201);
    }

    public function destroy(Request $request, Workspace $workspace, Project $project, User $member): JsonResponse
    {
        $this->authorizeWorkspaceAccess($request, $workspace);
        $this->ensureProjectBelongsToWorkspace($project, $workspace);

        if (! $project->members()->where('user_id', $member->id)->exists()) {
            return response()->json(['message' => 'User is not a member of this project.'], 404);
        }

        $project->members()->detach($member->id);

        return response()->json(['message' => 'Project member removed.']);
    }

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

    private function ensureProjectBelongsToWorkspace(Project $project, Workspace $workspace): void
    {
        if ($project->workspace_id !== $workspace->id) {
            abort(404, 'Project not found in this workspace.');
        }
    }
}
