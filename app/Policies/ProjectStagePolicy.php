<?php

namespace App\Policies;

use App\Models\ProjectStage;
use App\Models\User;

class ProjectStagePolicy
{
    public function view(User $user, ProjectStage $projectStage): bool
    {
        return $user->can('view', $projectStage->project);
    }

    public function update(User $user, ProjectStage $projectStage): bool
    {
        return $user->can('update', $projectStage->project);
    }

    public function delete(User $user, ProjectStage $projectStage): bool
    {
        return $user->isAdmin();
    }
}
