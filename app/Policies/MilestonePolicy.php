<?php

namespace App\Policies;

use App\Models\Milestone;
use App\Models\Project;
use App\Models\User;
use App\Support\ProjectAccess;

class MilestonePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Milestone $milestone): bool
    {
        return $milestone->project && ProjectAccess::canViewProject($user, $milestone->project);
    }

    public function create(User $user): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function update(User $user, Milestone $milestone): bool
    {
        return $milestone->project && ProjectAccess::canManageMilestone($user, $milestone->project);
    }

    public function delete(User $user, Milestone $milestone): bool
    {
        return $milestone->project && ProjectAccess::canManageMilestone($user, $milestone->project);
    }

    public function deleteAny(User $user): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function restore(User $user, Milestone $milestone): bool
    {
        return $milestone->project && ProjectAccess::canManageMilestone($user, $milestone->project);
    }

    public function restoreAny(User $user): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function forceDelete(User $user, Milestone $milestone): bool
    {
        return $milestone->project && ProjectAccess::canManageMilestone($user, $milestone->project);
    }

    public function forceDeleteAny(User $user): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function replicate(User $user, Milestone $milestone): bool
    {
        return $milestone->project && ProjectAccess::canManageMilestone($user, $milestone->project);
    }

    public function reorder(User $user): bool
    {
        return ProjectAccess::canManageProject($user);
    }
}
