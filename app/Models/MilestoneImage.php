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

class MilestoneImage extends Model
{
    use HasFactory, HasUlid, HasUserAudits, SoftDeletes;

    protected $fillable = [
        'ulid',
        'milestone_id',
        'milestone_ulid',
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
        static::saving(function (MilestoneImage $image): void {
            if ($image->milestone_id && blank($image->milestone_ulid)) {
                $image->milestone_ulid = Milestone::query()->whereKey($image->milestone_id)->value('ulid');
            }
        });
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
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
