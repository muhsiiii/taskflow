<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['sometimes', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'status'      => ['sometimes', 'in:todo,in_progress,in_review,done'],
            'priority'    => ['sometimes', 'in:low,medium,high,urgent'],
            'due_date'    => ['nullable', 'date'],
            'assignee_id' => ['nullable', 'exists:users,id'],
            'parent_id'   => ['nullable', 'exists:tasks,id'],
            'sort_order'  => ['sometimes', 'integer'],
        ];
    }
}
