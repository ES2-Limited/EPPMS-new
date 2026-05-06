<?php

namespace App\Models;

use App\Constants\RoleAndPermissions;
use App\Models\Concerns\HasUlid;
use App\Models\Concerns\HasUserAudits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;

class ProjectPersonnel extends Model
{
    use HasFactory, HasUlid, HasUserAudits, SoftDeletes;

    protected $table = 'project_personnel';

    protected $fillable = [
        'ulid',
        'project_id',
        'project_ulid',
        'personnel_id',
        'role_id',
        'project_role',
        'created_by_id',
        'deleted_by',
    ];

    protected static function booted(): void
    {
        static::saving(function (ProjectPersonnel $projectPersonnel): void {
            if ($projectPersonnel->project_id && blank($projectPersonnel->project_ulid)) {
                $projectPersonnel->project_ulid = Project::query()->whereKey($projectPersonnel->project_id)->value('ulid');
            }

            if ($projectPersonnel->project_role && blank($projectPersonnel->role_id)) {
                $projectPersonnel->role_id = Role::query()->where('name', $projectPersonnel->project_role)->value('id');
            }
        });

        static::created(function (ProjectPersonnel $projectPersonnel): void {
            if (in_array($projectPersonnel->project_role, [RoleAndPermissions::PROJECT_MANAGER, RoleAndPermissions::PROJECT_MEMBER], true)) {
                $projectPersonnel->personnel?->user?->assignRole($projectPersonnel->project_role);
            }
        });

        static::deleting(function (ProjectPersonnel $projectPersonnel): void {
            if (method_exists($projectPersonnel, 'isForceDeleting') && $projectPersonnel->isForceDeleting()) {
                return;
            }

            if (in_array($projectPersonnel->project_role, [RoleAndPermissions::PROJECT_MANAGER, RoleAndPermissions::PROJECT_MEMBER], true)) {
                $projectPersonnel->personnel?->user?->removeRole($projectPersonnel->project_role);
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function personnel(): BelongsTo
    {
        return $this->belongsTo(Personnel::class);
    }
}
