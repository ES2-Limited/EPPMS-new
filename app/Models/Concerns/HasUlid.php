<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasUlid
{
    protected static function bootHasUlid(): void
    {
        static::creating(function ($model): void {
            if (blank($model->ulid)) {
                $model->ulid = (string) Str::ulid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }
}
