<?php

namespace App\Policies;

use App\Constants\RoleAndPermissions;
use App\Models\ProjectChat;
use App\Models\User;
use App\Support\ProjectAccess;

class ProjectChatPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ProjectChat $projectChat): bool
    {
        return $projectChat->project && ProjectAccess::canViewProject($user, $projectChat->project);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ProjectChat $projectChat): bool
    {
        return false;
    }

    public function delete(User $user, ProjectChat $projectChat): bool
    {
        return $user->id === $projectChat->sender_id || $user->hasAnyRole([
            RoleAndPermissions::ADMIN,
            RoleAndPermissions::ORGANIZATION_ADMIN,
        ]);
    }
}
