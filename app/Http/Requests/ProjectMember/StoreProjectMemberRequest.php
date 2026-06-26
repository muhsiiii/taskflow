<?php

namespace App\Http\Requests\ProjectMember;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'role'  => ['required', 'in:owner,manager,contributor'],
        ];
    }
}
