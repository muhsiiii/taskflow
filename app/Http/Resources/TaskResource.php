<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'status'      => $this->status,
            'priority'    => $this->priority,
            'due_date'    => $this->due_date?->format('Y-m-d'),
            'sort_order'  => $this->sort_order,
            'is_subtask'  => !is_null($this->parent_id),

            // Who is assigned
            'assignee' => $this->whenLoaded('assignee', fn() => [
                'id'   => $this->assignee->id,
                'name' => $this->assignee->name,
            ]),

            // Who created it
            'creator' => $this->whenLoaded('creator', fn() => [
                'id'   => $this->creator->id,
                'name' => $this->creator->name,
            ]),

            // Parent task (if this is a subtask)
            'parent' => $this->whenLoaded('parent', fn() => [
                'id'    => $this->parent->id,
                'title' => $this->parent->title,
            ]),

            // Child subtasks
            'subtasks'       => TaskResource::collection($this->whenLoaded('subtasks')),
            'subtasks_count' => $this->whenCounted('subtasks'),

            // Attachments count
            'attachments_count' => $this->whenCounted('attachments'),

            'project_id'   => $this->project_id,
            'workspace_id' => $this->workspace_id,
            'created_at'   => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'   => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
