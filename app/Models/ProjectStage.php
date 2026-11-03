<?php

namespace App\Models;

use Database\Factories\ProjectStageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'project_id', 'name', 'sort_order', 'duration_days', 'planned_start', 'planned_end',
    'actual_start', 'actual_end', 'is_completed', 'completed_at', 'completed_by',
])]
class ProjectStage extends Model
{
    /** @use HasFactory<ProjectStageFactory> */
    use HasFactory;

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StageDocument::class)->latest();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(StageComment::class)->oldest();
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function timingStatus(): string
    {
        if ($this->actual_start === null) {
            return today()->greaterThan($this->planned_end) ? 'overdue' : 'not_started';
        }

        $comparisonDate = $this->actual_end ?? today();

        if ($comparisonDate->greaterThan($this->planned_end)) {
            return 'late';
        }

        if ($this->actual_start->lessThan($this->planned_start)) {
            return 'early';
        }

        return 'on_track';
    }

    public function timingLabel(): string
    {
        return match ($this->timingStatus()) {
            'overdue' => 'Terlambat dimulai',
            'late' => 'Melewati rencana',
            'early' => 'Dimulai lebih awal',
            'on_track' => 'Sesuai rencana',
            default => 'Belum dimulai',
        };
    }

    protected function casts(): array
    {
        return [
            'planned_start' => 'date',
            'planned_end' => 'date',
            'actual_start' => 'date',
            'actual_end' => 'date',
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }
}
