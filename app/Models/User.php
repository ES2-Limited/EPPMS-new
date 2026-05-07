<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function personnel(): HasOne
    {
        return $this->hasOne(Personnel::class);
    }

    public function contractor(): HasOne
    {
        return $this->hasOne(Contractor::class);
    }

    public function contractorPersonnel(): HasOne
    {
        return $this->hasOne(ContractorPersonnel::class);
    }

    public function projectChats(): HasMany
    {
        return $this->hasMany(ProjectChat::class, 'sender_id');
    }

    public function milestoneChatMessages(): HasMany
    {
        return $this->hasMany(MilestoneChatMessage::class, 'sender_id');
    }

    public function taskChatMessages(): HasMany
    {
        return $this->hasMany(TaskChatMessage::class, 'sender_id');
    }
}
