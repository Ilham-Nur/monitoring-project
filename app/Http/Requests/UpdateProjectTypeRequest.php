<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150', Rule::unique('project_types')->ignore($this->route('projectType'))],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
            'stages' => ['required', 'array', 'min:1'],
            'stages.*.name' => ['required', 'string', 'max:150'],
            'stages.*.duration_days' => ['required', 'integer', 'min:1', 'max:365'],
        ];
    }
}
