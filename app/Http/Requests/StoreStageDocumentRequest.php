<?php

namespace App\Http\Requests;

use App\Models\ProjectStage;
use Illuminate\Foundation\Http\FormRequest;

class StoreStageDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $stage = $this->route('projectStage');

        return $stage instanceof ProjectStage && ($this->user()?->can('update', $stage) ?? false);
    }

    public function rules(): array
    {
        return [
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx', 'extensions:pdf,jpg,jpeg,png,doc,docx,xls,xlsx', 'max:20480'],
        ];
    }
}
