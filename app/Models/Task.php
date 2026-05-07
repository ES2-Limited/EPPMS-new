<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use App\Models\Concerns\HasUserAudits;
use App\Support\ProjectAccess;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, HasUlid, HasUserAudits, SoftDeletes;

    protected $fillable = [
        'ulid',
        'milestone_id',
        'name',
        'description',
        'status',
        'start_date',
        'due_date',
        'cost',
        'created_by_id',
        'deleted_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(TaskImage::class);
    }

    public function latestImages(): HasMany
    {
        return $this->images()->latest();
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(TaskChatMessage::class);
    }

    public function mark_as_done(): void
    {
        if (! auth()->check() || ! self::canBeMarkedDoneBy(auth()->user(), $this)) {
            throw new AuthorizationException('You are not allowed to mark this task as done.');
        }

        $this->forceFill(['status' => 'done'])->save();
    }

    public static function canBeMarkedDoneBy(User $user, Task $task): bool
    {
        return ProjectAccess::canMarkTaskDone($user, $task);
    }
}
