<?php

namespace App\Support;

use App\Constants\RoleAndPermissions;
use App\Models\Project;
use App\Models\ProjectPersonnel;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ProjectAccess
{
    public static function scopeProjects(Builder $query, User $user): Builder
    {
        if ($user->hasAnyRole([
            RoleAndPermissions::ADMIN,
            RoleAndPermissions::ORGANIZATION_ADMIN,
            RoleAndPermissions::MANAGEMENT_ADMIN,
            RoleAndPermissions::AUDITOR,
        ])) {
            return $query;
        }

        if ($user->hasRole(RoleAndPermissions::DIRECTORATE_ADMIN)) {
            $directorateId = $user->personnel?->directorate_id;

            return $directorateId
                ? $query->where(fn (Builder $projects): Builder => $projects
                    ->where('directorate_id', $directorateId)
                    ->orWhereHas('department', fn (Builder $department): Builder => $department->where('directorate_id', $directorateId)))
                : $query->whereRaw('1 = 0');
        }

        if ($user->hasRole(RoleAndPermissions::REGIONAL_ADMIN)) {
            $officeId = $user->personnel?->office_id;

            return $officeId ? $query->where('office_id', $officeId) : $query->whereRaw('1 = 0');
        }

        if ($user->hasAnyRole([RoleAndPermissions::DEPARTMENT_ADMIN, RoleAndPermissions::HEAD_OF_UNIT])) {
            $departmentId = $user->personnel?->department_id;

            return $departmentId ? $query->where('department_id', $departmentId) : $query->whereRaw('1 = 0');
        }

        if ($user->hasRole(RoleAndPermissions::ORGANIZATION_PERSONNEL)) {
            $personnelId = $user->personnel?->id;

            return $personnelId
                ? $query->whereHas('projectPersonnel', fn (Builder $assignment): Builder => $assignment->where('personnel_id', $personnelId))
                : $query->whereRaw('1 = 0');
        }

        if ($user->hasRole(RoleAndPermissions::CONTRACTOR)) {
            $contractorId = $user->contractor?->id;

            return $contractorId ? $query->where('contractor_id', $contractorId) : $query->whereRaw('1 = 0');
        }

        if ($user->hasRole(RoleAndPermissions::CONTRACTOR_PERSONNEL)) {
            $contractorId = $user->contractorPersonnel?->contractor_id;

            return $contractorId ? $query->where('contractor_id', $contractorId) : $query->whereRaw('1 = 0');
        }

        if ($user->hasRole(RoleAndPermissions::CONSULTANT)) {
            $consultantId = $user->contractor?->id;

            return $consultantId ? $query->where('consultant_id', $consultantId) : $query->whereRaw('1 = 0');
        }

        return $query->whereRaw('1 = 0');
    }

    public static function canViewProject(User $user, Project $project): bool
    {
        return self::scopeProjects(Project::query()->whereKey($project->id), $user)->exists();
    }

    public static function canManageProject(User $user): bool
    {
        return $user->hasAnyRole([RoleAndPermissions::ADMIN, RoleAndPermissions::ORGANIZATION_ADMIN]);
    }

    public static function isProjectManager(User $user, Project $project): bool
    {
        $personnelId = $user->personnel?->id;

        if (! $personnelId) {
            return false;
        }

        return ProjectPersonnel::query()
            ->where('project_id', $project->id)
            ->where('personnel_id', $personnelId)
            ->where('project_role', RoleAndPermissions::PROJECT_MANAGER)
            ->exists();
    }

    public static function canManageMilestone(User $user, Project $project): bool
    {
        return self::canManageProject($user) || self::isProjectManager($user, $project);
    }

    public static function canMarkTaskDone(User $user, Task $task): bool
    {
        if ($user->hasAnyRole([RoleAndPermissions::ADMIN, RoleAndPermissions::ORGANIZATION_ADMIN])) {
            return true;
        }

        if (! $user->hasRole(RoleAndPermissions::ORGANIZATION_PERSONNEL)) {
            return false;
        }

        $project = $task->milestone?->project;

        if (! $project) {
            return false;
        }

        $personnelId = $user->personnel?->id;

        if (! $personnelId) {
            return false;
        }

        return ProjectPersonnel::query()
            ->where('project_id', $project->id)
            ->where('personnel_id', $personnelId)
            ->whereIn('project_role', [RoleAndPermissions::PROJECT_MANAGER, RoleAndPermissions::PROJECT_MEMBER])
            ->exists();
    }
}
