<?php

namespace App\Policies;

use App\Constants\RoleAndPermissions;
use App\Models\MilestoneImage;
use App\Models\User;
use App\Support\ProjectAccess;

class MilestoneImagePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MilestoneImage $milestoneImage): bool
    {
        $project = $milestoneImage->milestone?->project;

        return $project && ProjectAccess::canViewProject($user, $project);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, MilestoneImage $milestoneImage): bool
    {
        return false;
    }

    public function delete(User $user, MilestoneImage $milestoneImage): bool
    {
        return $user->id === $milestoneImage->uploader_id || $user->hasAnyRole([
            RoleAndPermissions::ADMIN,
            RoleAndPermissions::ORGANIZATION_ADMIN,
        ]);
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasAnyRole([RoleAndPermissions::ADMIN, RoleAndPermissions::ORGANIZATION_ADMIN]);
    }

    public function restore(User $user, MilestoneImage $milestoneImage): bool
    {
        return $user->hasAnyRole([RoleAndPermissions::ADMIN, RoleAndPermissions::ORGANIZATION_ADMIN]);
    }

    public function restoreAny(User $user): bool
    {
        return $user->hasAnyRole([RoleAndPermissions::ADMIN, RoleAndPermissions::ORGANIZATION_ADMIN]);
    }

    public function forceDelete(User $user, MilestoneImage $milestoneImage): bool
    {
        return $user->hasAnyRole([RoleAndPermissions::ADMIN, RoleAndPermissions::ORGANIZATION_ADMIN]);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->hasAnyRole([RoleAndPermissions::ADMIN, RoleAndPermissions::ORGANIZATION_ADMIN]);
    }

    public function replicate(User $user, MilestoneImage $milestoneImage): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }
}
