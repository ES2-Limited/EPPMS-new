<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectChat extends Model
{
    use HasFactory, HasUlid;

    protected $fillable = [
        'ulid',
        'project_id',
        'sender_id',
        'message',
        'created_by_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (ProjectChat $chat): void {
            if (blank($chat->created_by_id) && auth()->check()) {
                $chat->created_by_id = auth()->id();
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
