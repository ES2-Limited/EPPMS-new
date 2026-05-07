<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskChatMessage extends Model
{
    use HasFactory, HasUlid;

    protected $fillable = [
        'ulid',
        'task_id',
        'sender_id',
        'message',
        'created_by_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (TaskChatMessage $message): void {
            if (blank($message->created_by_id) && auth()->check()) {
                $message->created_by_id = auth()->id();
            }
        });
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
