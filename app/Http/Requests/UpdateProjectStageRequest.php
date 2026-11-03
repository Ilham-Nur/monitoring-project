<?php

namespace App\Http\Requests;

use App\Models\ProjectStage;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectStageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $stage = $this->route('projectStage');

        return $stage instanceof ProjectStage && ($this->user()?->can('update', $stage) ?? false);
    }

    public function rules(): array
    {
        $adminRules = $this->user()?->isAdmin() ? ['required'] : ['prohibited'];

        return [
            'name' => [...$adminRules, 'string', 'max:150'],
            'duration_days' => [...$adminRules, 'integer', 'min:1', 'max:365'],
            'actual_start' => ['nullable', 'date'],
            'actual_end' => ['nullable', 'date', 'after_or_equal:actual_start'],
        ];
    }
}
