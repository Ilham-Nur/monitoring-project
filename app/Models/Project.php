<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'name', 'client_id', 'project_type_id', 'description', 'start_date', 'end_date', 'status', 'created_by'])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ProjectType::class, 'project_type_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pics(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(ProjectStage::class)->orderBy('sort_order');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ActivityLog::class)->latest();
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isClient()) {
            return $query->whereBelongsTo($user, 'client');
        }

        return $query->whereHas('pics', fn (Builder $picQuery): Builder => $picQuery->whereKey($user->id));
    }

    public function progressPercentage(): int
    {
        $totalStages = $this->stages_count ?? $this->stages()->count();
        $completedStages = $this->completed_stages_count ?? $this->stages()->where('is_completed', true)->count();

        return $totalStages === 0 ? 0 : (int) round(($completedStages / $totalStages) * 100);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'completed' => 'Selesai',
            'pending' => 'Belum Dimulai',
            default => 'Berjalan',
        };
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }
}
