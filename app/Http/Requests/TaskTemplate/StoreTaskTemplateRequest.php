<?php

namespace App\Http\Requests\TaskTemplate;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'max:150'],
            'description'      => ['nullable', 'string'],
            'status'           => ['sometimes', 'in:todo,in_progress,in_review,done'],
            'estimate_minutes' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
