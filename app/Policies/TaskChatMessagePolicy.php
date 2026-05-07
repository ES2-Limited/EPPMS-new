<?php

namespace App\Policies;

use App\Constants\RoleAndPermissions;
use App\Models\TaskChatMessage;
use App\Models\User;
use App\Support\ProjectAccess;

class TaskChatMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TaskChatMessage $taskChatMessage): bool
    {
        $project = $taskChatMessage->task?->milestone?->project;

        return $project && ProjectAccess::canViewProject($user, $project);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, TaskChatMessage $taskChatMessage): bool
    {
        return false;
    }

    public function delete(User $user, TaskChatMessage $taskChatMessage): bool
    {
        return $user->id === $taskChatMessage->sender_id || $user->hasAnyRole([
            RoleAndPermissions::ADMIN,
            RoleAndPermissions::ORGANIZATION_ADMIN,
        ]);
    }
}
