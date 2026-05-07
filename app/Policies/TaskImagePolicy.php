<?php

namespace App\Policies;

use App\Constants\RoleAndPermissions;
use App\Models\TaskImage;
use App\Models\User;
use App\Support\ProjectAccess;

class TaskImagePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TaskImage $taskImage): bool
    {
        $project = $taskImage->task?->milestone?->project;

        return $project && ProjectAccess::canViewProject($user, $project);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, TaskImage $taskImage): bool
    {
        return false;
    }

    public function delete(User $user, TaskImage $taskImage): bool
    {
        return $user->id === $taskImage->uploader_id || $user->hasAnyRole([
            RoleAndPermissions::ADMIN,
            RoleAndPermissions::ORGANIZATION_ADMIN,
        ]);
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasAnyRole([RoleAndPermissions::ADMIN, RoleAndPermissions::ORGANIZATION_ADMIN]);
    }

    public function restore(User $user, TaskImage $taskImage): bool
    {
        return $user->hasAnyRole([RoleAndPermissions::ADMIN, RoleAndPermissions::ORGANIZATION_ADMIN]);
    }

    public function restoreAny(User $user): bool
    {
        return $user->hasAnyRole([RoleAndPermissions::ADMIN, RoleAndPermissions::ORGANIZATION_ADMIN]);
    }

    public function forceDelete(User $user, TaskImage $taskImage): bool
    {
        return $user->hasAnyRole([RoleAndPermissions::ADMIN, RoleAndPermissions::ORGANIZATION_ADMIN]);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->hasAnyRole([RoleAndPermissions::ADMIN, RoleAndPermissions::ORGANIZATION_ADMIN]);
    }

    public function replicate(User $user, TaskImage $taskImage): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }
}
