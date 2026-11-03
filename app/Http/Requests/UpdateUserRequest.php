<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->route('user'))],
            'role' => ['required', Rule::in(['admin', 'pic', 'client'])],
            'company_name' => ['nullable', 'required_if:role,client', 'string', 'max:150'],
            'is_active' => ['nullable', 'boolean'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ];
    }
}
