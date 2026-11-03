<?php

namespace App\Models;

use Database\Factories\StageCommentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['project_stage_id', 'user_id', 'comment'])]
class StageComment extends Model
{
    /** @use HasFactory<StageCommentFactory> */
    use HasFactory;

    public function stage(): BelongsTo
    {
        return $this->belongsTo(ProjectStage::class, 'project_stage_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
