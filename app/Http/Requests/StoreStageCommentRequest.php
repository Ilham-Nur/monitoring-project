<?php

namespace App\Http\Requests;

use App\Models\ProjectStage;
use Illuminate\Foundation\Http\FormRequest;

class StoreStageCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $stage = $this->route('projectStage');

        return $stage instanceof ProjectStage && ($this->user()?->can('view', $stage) ?? false);
    }

    public function rules(): array
    {
        return ['comment' => ['required', 'string', 'max:2000']];
    }
}
