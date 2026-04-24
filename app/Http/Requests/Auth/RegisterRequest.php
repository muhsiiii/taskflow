<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // anyone can register
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            // 'confirmed' means the request must also have 'password_confirmation' field
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'         => 'An account with this email already exists.',
            'password.confirmed'   => 'Password confirmation does not match.',
            'password.min'         => 'Password must be at least 8 characters.',
        ];
    }
}
