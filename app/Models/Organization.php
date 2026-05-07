<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
