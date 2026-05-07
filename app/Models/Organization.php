<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'address',
        'phone',
        'email',
        'website',
    ];

    protected static function booted(): void
    {
        static::creating(function (Organization $organization): void {
            if (static::query()->exists()) {
                throw new \RuntimeException('Only one organization record may exist.');
            }
        });
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo) {
            return null;
        }

        $path = 'organisation/'.basename($this->logo);

        return Storage::disk('public')->exists($path)
            ? asset('storage/'.$path)
            : null;
    }
}
