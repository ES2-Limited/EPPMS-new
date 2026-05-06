<?php

namespace App\Models\Concerns;

trait HasUserAudits
{
    protected static function bootHasUserAudits(): void
    {
        static::creating(function ($model): void {
            if (blank($model->created_by_id) && auth()->check()) {
                $model->created_by_id = auth()->id();
            }
        });

        static::deleting(function ($model): void {
            if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
                return;
            }

            if (auth()->check()) {
                $model->forceFill(['deleted_by' => auth()->id()])->saveQuietly();
            }
        });
    }
}
