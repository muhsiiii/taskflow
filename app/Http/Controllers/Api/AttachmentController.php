<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Project;
use App\Models\Task;
use App\Models\Workspace;
use App\Services\S3UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    public function __construct(private S3UploadService $uploadService) {}

    /**
     * POST /api/workspaces/{workspace}/projects/{project}/tasks/{task}/attachments
     * Upload a file and attach it to a task
     */
    public function store(
        Request $request,
        Workspace $workspace,
        Project $project,
        Task $task
    ): JsonResponse {
        $this->authorizeWorkspaceAccess($request, $workspace);

        $request->validate([
            'file' => ['required', 'file', 'max:51200'], // 50MB max
        ]);

        $file = $request->file('file');

        // Upload to S3 / local disk
        $key = $this->uploadService->upload($file, "attachments/{$workspace->id}/{$task->id}");

        // Save record in database
        $attachment = Attachment::create([
            'task_id'     => $task->id,
            'uploaded_by' => $request->user()->id,
            'filename'    => $file->getClientOriginalName(),
            's3_key'      => $key,
            'mime_type'   => $file->getMimeType(),
            'size_bytes'  => $file->getSize(),
        ]);

        // Generate a signed URL for immediate download
        $signedUrl = $this->uploadService->getSignedUrl($key);

        return response()->json([
            'message' => 'File uploaded successfully.',
            'data'    => [
                'id'         => $attachment->id,
                'filename'   => $attachment->filename,
                'mime_type'  => $attachment->mime_type,
                'size_bytes' => $attachment->size_bytes,
                'url'        => $signedUrl,
                'expires_in' => '30 minutes',
            ],
        ], 201);
    }

    /**
     * GET /api/workspaces/{workspace}/projects/{project}/tasks/{task}/attachments
     * List all attachments for a task with fresh signed URLs
     */
    public function index(
        Request $request,
        Workspace $workspace,
        Project $project,
        Task $task
    ): JsonResponse {
        $this->authorizeWorkspaceAccess($request, $workspace);

        $attachments = $task->attachments()
            ->with('uploader:id,name')
            ->latest()
            ->get()
            ->map(fn($att) => [
                'id'          => $att->id,
                'filename'    => $att->filename,
                'mime_type'   => $att->mime_type,
                'size_bytes'  => $att->size_bytes,
                'size_human'  => $this->humanFileSize($att->size_bytes),
                'url'         => $this->uploadService->getSignedUrl($att->s3_key),
                'uploaded_by' => $att->uploader->name,
                'uploaded_at' => $att->created_at->format('Y-m-d H:i:s'),
            ]);

        return response()->json(['data' => $attachments]);
    }

    /**
     * DELETE /api/workspaces/{workspace}/projects/{project}/tasks/{task}/attachments/{attachment}
     * Delete a file from S3 and remove the DB record
     */
    public function destroy(
        Request $request,
        Workspace $workspace,
        Project $project,
        Task $task,
        Attachment $attachment
    ): JsonResponse {
        $this->authorizeWorkspaceAccess($request, $workspace);

        // Only uploader or workspace owner can delete
        $user = $request->user();
        if ($attachment->uploaded_by !== $user->id && $workspace->owner_id !== $user->id) {
            return response()->json(['message' => 'Not authorized to delete this file.'], 403);
        }

        // Delete from S3 / local disk
        $this->uploadService->delete($attachment->s3_key);

        // Delete DB record
        $attachment->delete();

        return response()->json(['message' => 'Attachment deleted.']);
    }

    private function humanFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
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
