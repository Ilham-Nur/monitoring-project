<?php

namespace App\Models;

use Database\Factories\StageTemplateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['project_type_id', 'name', 'duration_days', 'sort_order'])]
class StageTemplate extends Model
{
    /** @use HasFactory<StageTemplateFactory> */
    use HasFactory;

    public function projectType(): BelongsTo
    {
        return $this->belongsTo(ProjectType::class);
    }
}
