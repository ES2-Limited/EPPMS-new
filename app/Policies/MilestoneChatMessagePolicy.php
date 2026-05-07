<?php

namespace App\Policies;

use App\Constants\RoleAndPermissions;
use App\Models\MilestoneChatMessage;
use App\Models\User;
use App\Support\ProjectAccess;

class MilestoneChatMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MilestoneChatMessage $milestoneChatMessage): bool
    {
        $project = $milestoneChatMessage->milestone?->project;

        return $project && ProjectAccess::canViewProject($user, $project);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, MilestoneChatMessage $milestoneChatMessage): bool
    {
        return false;
    }

    public function delete(User $user, MilestoneChatMessage $milestoneChatMessage): bool
    {
        return $user->id === $milestoneChatMessage->sender_id || $user->hasAnyRole([
            RoleAndPermissions::ADMIN,
            RoleAndPermissions::ORGANIZATION_ADMIN,
        ]);
    }
}
