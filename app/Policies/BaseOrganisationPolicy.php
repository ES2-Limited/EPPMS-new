<?php

namespace App\Policies;

use App\Constants\RoleAndPermissions;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

abstract class BaseOrganisationPolicy
{
    protected function canManage(User $user): bool
    {
        return $user->hasAnyRole([
            RoleAndPermissions::ADMIN,
            RoleAndPermissions::ORGANIZATION_ADMIN,
        ]);
    }

    protected function canViewModule(User $user): bool
    {
        return $user->hasAnyRole(RoleAndPermissions::SYSTEM_ROLES);
    }

    public function viewAny(User $user): bool
    {
        return $this->canViewModule($user);
    }

    public function view(User $user, Model $model): bool
    {
        return $this->canViewModule($user);
    }

    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    public function update(User $user, Model $model): bool
    {
        return $this->canManage($user);
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->canManage($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function restore(User $user, Model $model): bool
    {
        return $this->canManage($user);
    }

    public function restoreAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return $this->canManage($user);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function replicate(User $user, Model $model): bool
    {
        return $this->canManage($user);
    }

    public function reorder(User $user): bool
    {
        return $this->canManage($user);
    }
}
