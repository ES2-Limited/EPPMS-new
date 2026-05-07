<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use App\Models\Concerns\HasUserAudits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contractor extends Model
{
    use HasFactory, HasUlid, HasUserAudits, SoftDeletes;

    public const TYPE_CONTRACTOR = 1;

    public const TYPE_CONSULTANT = 2;

    protected $fillable = [
        'ulid',
        'user_id',
        'firm_type_id',
        'phone',
        'website',
        'logo',
        'created_by_id',
        'deleted_by',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function personnel(): HasMany
    {
        return $this->hasMany(ContractorPersonnel::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function isConsultant(): bool
    {
        return (int) $this->firm_type_id === self::TYPE_CONSULTANT;
    }

    public function firmTypeLabel(): string
    {
        return $this->isConsultant() ? 'Consultant' : 'Contractor';
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'contractor_id');
    }

    public function consultantProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'consultant_id');
    }
}
