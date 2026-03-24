<?php

namespace App\Models;

use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'photo',
        'password',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        // Jika panel ID-nya 'admin', cek kolom is_admin
        if ($panel->getId() === 'admin') {
            return (bool) $this->is_admin;
        }

        return true;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if (blank($this->photo)) {
            return null;
        }

        return asset('storage/' . ltrim($this->photo, '/'));
    }
}