<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use App\Models\Concerns\HasUserAudits;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class TaskImage extends Model
{
    use HasFactory, HasUlid, HasUserAudits, SoftDeletes;

    protected $fillable = [
        'ulid',
        'task_id',
        'task_ulid',
        'uploader_id',
        'name',
        'original_name',
        'mime_type',
        'size',
        'created_by_id',
        'deleted_by',
    ];

    protected static function booted(): void
    {
        static::saving(function (TaskImage $image): void {
            if ($image->task_id && blank($image->task_ulid)) {
                $image->task_ulid = Task::query()->whereKey($image->task_id)->value('ulid');
            }
        });
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    protected function url(): Attribute
    {
        return Attribute::get(fn (): string => Storage::disk('public')->url($this->name));
    }
}
