<?php

namespace App\Policies;

use App\Constants\RoleAndPermissions;
use App\Models\ContractorPersonnel;
use App\Models\User;

class ContractorPersonnelPolicy
{
    protected function canManage(User $user): bool
    {
        return $user->hasAnyRole([
            RoleAndPermissions::ADMIN,
            RoleAndPermissions::ORGANIZATION_ADMIN,
        ]);
    }

    protected function canViewAll(User $user): bool
    {
        return $user->hasAnyRole(RoleAndPermissions::SYSTEM_ROLES);
    }

    public function viewAny(User $user): bool
    {
        return $this->canViewAll($user) || $user->hasAnyRole([
            RoleAndPermissions::CONTRACTOR,
            RoleAndPermissions::CONSULTANT,
            RoleAndPermissions::CONTRACTOR_PERSONNEL,
        ]);
    }

    public function view(User $user, ContractorPersonnel $contractorPersonnel): bool
    {
        if ($this->canViewAll($user)) {
            return true;
        }

        if ($user->hasAnyRole([RoleAndPermissions::CONTRACTOR, RoleAndPermissions::CONSULTANT])) {
            return $contractorPersonnel->contractor?->user_id === $user->id;
        }

        if ($user->hasRole(RoleAndPermissions::CONTRACTOR_PERSONNEL)) {
            return ContractorPersonnel::query()
                ->where('user_id', $user->id)
                ->where('contractor_id', $contractorPersonnel->contractor_id)
                ->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    public function update(User $user, ContractorPersonnel $contractorPersonnel): bool
    {
        return $this->canManage($user);
    }

    public function delete(User $user, ContractorPersonnel $contractorPersonnel): bool
    {
        return $this->canManage($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function restore(User $user, ContractorPersonnel $contractorPersonnel): bool
    {
        return $this->canManage($user);
    }

    public function restoreAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function forceDelete(User $user, ContractorPersonnel $contractorPersonnel): bool
    {
        return $this->canManage($user);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function replicate(User $user, ContractorPersonnel $contractorPersonnel): bool
    {
        return $this->canManage($user);
    }

    public function reorder(User $user): bool
    {
        return $this->canManage($user);
    }
}
