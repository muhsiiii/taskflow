<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskTemplate\StoreTaskTemplateRequest;
use App\Models\TaskTemplate;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskTemplateController extends Controller
{
    public function index(Request $request, Workspace $workspace): JsonResponse
    {
        $this->authorizeWorkspaceAccess($request, $workspace);

        $templates = $workspace->taskTemplates()->latest()->get();

        return response()->json(['data' => $templates]);
    }

    public function store(StoreTaskTemplateRequest $request, Workspace $workspace): JsonResponse
    {
        $this->authorizeWorkspaceAccess($request, $workspace);

        $template = $workspace->taskTemplates()->create([
            'name'             => $request->name,
            'description'      => $request->description,
            'status'           => $request->status ?? 'todo',
            'estimate_minutes' => $request->estimate_minutes,
        ]);

        return response()->json([
            'message' => 'Template created successfully.',
            'data'    => $template,
        ], 201);
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
}
