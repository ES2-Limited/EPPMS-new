<?php

namespace App\Policies;

use App\Models\ProjectPersonnel;
use App\Models\User;
use App\Support\ProjectAccess;

class ProjectPersonnelPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ProjectPersonnel $projectPersonnel): bool
    {
        return $projectPersonnel->project && ProjectAccess::canViewProject($user, $projectPersonnel->project);
    }

    public function create(User $user): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function update(User $user, ProjectPersonnel $projectPersonnel): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function delete(User $user, ProjectPersonnel $projectPersonnel): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function deleteAny(User $user): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function restore(User $user, ProjectPersonnel $projectPersonnel): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function restoreAny(User $user): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function forceDelete(User $user, ProjectPersonnel $projectPersonnel): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function forceDeleteAny(User $user): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function replicate(User $user, ProjectPersonnel $projectPersonnel): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function reorder(User $user): bool
    {
        return ProjectAccess::canManageProject($user);
    }
}
