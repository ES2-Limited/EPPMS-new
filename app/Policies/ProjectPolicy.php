<?php

namespace App\Policies;

use App\Constants\RoleAndPermissions;
use App\Models\Project;
use App\Models\User;
use App\Support\ProjectAccess;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            ...RoleAndPermissions::SYSTEM_ROLES,
            RoleAndPermissions::CONTRACTOR,
            RoleAndPermissions::CONTRACTOR_PERSONNEL,
            RoleAndPermissions::CONSULTANT,
        ]);
    }

    public function view(User $user, Project $project): bool
    {
        return ProjectAccess::canViewProject($user, $project);
    }

    public function create(User $user): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function update(User $user, Project $project): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function delete(User $user, Project $project): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function deleteAny(User $user): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function restore(User $user, Project $project): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function restoreAny(User $user): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function forceDelete(User $user, Project $project): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function forceDeleteAny(User $user): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function replicate(User $user, Project $project): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function reorder(User $user): bool
    {
        return ProjectAccess::canManageProject($user);
    }
}
