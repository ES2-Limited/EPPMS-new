<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use App\Models\Concerns\HasUserAudits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, HasUlid, HasUserAudits, SoftDeletes;

    protected $fillable = [
        'ulid',
        'name',
        'directorate_id',
        'created_by_id',
        'deleted_by',
    ];

    public function directorate(): BelongsTo
    {
        return $this->belongsTo(Directorate::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function personnel(): HasMany
    {
        return $this->hasMany(Personnel::class);
    }
}
