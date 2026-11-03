<?php

namespace App\Http\Requests;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('projects')->ignore($this->route('project'))],
            'name' => ['required', 'string', 'max:150'],
            'client_id' => ['required', Rule::exists('users', 'id')->where(fn (Builder $query) => $query->where('role', 'client')->where('is_active', true))],
            'description' => ['nullable', 'string', 'max:2000'],
            'start_date' => ['required', 'date'],
            'pic_ids' => ['required', 'array', 'min:1'],
            'pic_ids.*' => ['integer', 'distinct', Rule::exists('users', 'id')->where(fn (Builder $query) => $query->where('role', 'pic')->where('is_active', true))],
        ];
    }
}
