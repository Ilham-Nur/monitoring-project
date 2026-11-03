<?php

namespace App\Models;

use Database\Factories\StageDocumentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['project_stage_id', 'original_name', 'file_path', 'mime_type', 'file_size', 'uploaded_by'])]
class StageDocument extends Model
{
    /** @use HasFactory<StageDocumentFactory> */
    use HasFactory;

    public function stage(): BelongsTo
    {
        return $this->belongsTo(ProjectStage::class, 'project_stage_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
