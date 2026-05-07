<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use App\Models\Concerns\HasUserAudits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Milestone extends Model
{
    use HasFactory, HasUlid, HasUserAudits, SoftDeletes;

    protected $fillable = [
        'ulid',
        'project_id',
        'project_ulid',
        'name',
        'amount',
        'description',
        'created_by_id',
        'deleted_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (Milestone $milestone): void {
            if ($milestone->project_id && blank($milestone->project_ulid)) {
                $milestone->project_ulid = Project::query()->whereKey($milestone->project_id)->value('ulid');
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(MilestoneImage::class);
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(MilestoneChatMessage::class);
    }
}
