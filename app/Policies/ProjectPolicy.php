<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isClient()) {
            return $project->client_id === $user->id;
        }

        return $project->pics()->whereKey($user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Project $project): bool
    {
        return $user->isAdmin() || ($user->isPic() && $project->pics()->whereKey($user->id)->exists());
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->isAdmin();
    }
}
