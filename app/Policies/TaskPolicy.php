<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use App\Support\ProjectAccess;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        return $task->milestone?->project && ProjectAccess::canViewProject($user, $task->milestone->project);
    }

    public function create(User $user): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function update(User $user, Task $task): bool
    {
        return $task->milestone?->project && ProjectAccess::canManageMilestone($user, $task->milestone->project);
    }

    public function delete(User $user, Task $task): bool
    {
        return $task->milestone?->project && ProjectAccess::canManageMilestone($user, $task->milestone->project);
    }

    public function deleteAny(User $user): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function restore(User $user, Task $task): bool
    {
        return $task->milestone?->project && ProjectAccess::canManageMilestone($user, $task->milestone->project);
    }

    public function restoreAny(User $user): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function forceDelete(User $user, Task $task): bool
    {
        return $task->milestone?->project && ProjectAccess::canManageMilestone($user, $task->milestone->project);
    }

    public function forceDeleteAny(User $user): bool
    {
        return ProjectAccess::canManageProject($user);
    }

    public function replicate(User $user, Task $task): bool
    {
        return $task->milestone?->project && ProjectAccess::canManageMilestone($user, $task->milestone->project);
    }

    public function reorder(User $user): bool
    {
        return ProjectAccess::canManageProject($user);
    }
}
