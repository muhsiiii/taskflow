<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Workspace\StoreWorkspaceRequest;
use App\Http\Requests\Workspace\UpdateWorkspaceRequest;
use App\Models\Workspace;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $workspaces = Workspace::where('owner_id', $user->id)
            ->orWhereHas('members', fn($q) => $q->where('user_id', $user->id))
            ->with(['owner:id,name,email'])
            ->withCount('members', 'projects')
            ->latest()
            ->get();

        return response()->json(['data' => $workspaces]);
    }

    public function store(StoreWorkspaceRequest $request): JsonResponse
    {
        $user = $request->user();

        $workspace = Workspace::create([
            'name'     => $request->name,
            'slug'     => $request->slug,
            'owner_id' => $user->id,
        ]);

        $workspace->members()->attach($user->id, ['role' => 'admin']);

        return response()->json([
            'message' => 'Workspace created successfully.',
            'data'    => $workspace->load('owner:id,name,email'),
        ], 201);
    }

    public function show(Workspace $workspace): JsonResponse
    {
        $this->authorize('view', $workspace);

        $workspace->load([
            'owner:id,name,email',
            'members:id,name,email',
            'projects' => fn($q) => $q->withCount('tasks'),
        ]);

        return response()->json(['data' => $workspace]);
    }

    public function update(UpdateWorkspaceRequest $request, Workspace $workspace): JsonResponse
    {
        $this->authorize('update', $workspace);

        $workspace->update($request->validated());

        return response()->json([
            'message' => 'Workspace updated.',
            'data'    => $workspace,
        ]);
    }

    public function destroy(Workspace $workspace): JsonResponse
    {
        $this->authorize('delete', $workspace);

        $workspace->delete();

        return response()->json(['message' => 'Workspace deleted.']);
    }

    public function addMember(Request $request, Workspace $workspace): JsonResponse
    {
        $this->authorize('manageMember', $workspace);

        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'role'  => ['required', 'in:admin,manager,member'],
        ]);

        $user = User::where('email', $request->email)->first();

        if ($workspace->members()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'User is already a member.'], 422);
        }

        $workspace->members()->attach($user->id, ['role' => $request->role]);

        return response()->json(['message' => 'Member added successfully.']);
    }

    public function removeMember(Workspace $workspace, User $user): JsonResponse
    {
        $this->authorize('manageMember', $workspace);

        if ($workspace->owner_id === $user->id) {
            return response()->json(['message' => 'Cannot remove the workspace owner.'], 422);
        }

        $workspace->members()->detach($user->id);

        return response()->json(['message' => 'Member removed.']);
    }
}
