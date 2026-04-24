<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'status'      => $this->status,
            'due_date'    => $this->due_date?->format('Y-m-d'),
            'workspace'   => [
                'id'   => $this->workspace->id,
                'name' => $this->workspace->name,
            ],
            'creator' => [
                'id'   => $this->creator->id,
                'name' => $this->creator->name,
            ],
            'tasks_count' => $this->whenCounted('tasks'),
            'created_at'  => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'  => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
