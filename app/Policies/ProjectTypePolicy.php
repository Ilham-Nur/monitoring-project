<?php

namespace App\Policies;

use App\Models\ProjectType;
use App\Models\User;

class ProjectTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, ProjectType $projectType): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, ProjectType $projectType): bool
    {
        return $user->isAdmin() && ! $projectType->projects()->exists();
    }
}
