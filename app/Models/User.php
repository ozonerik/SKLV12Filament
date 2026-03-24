<?php

namespace App\Models;

use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Services\ImageCompressionService;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable;

    protected static function booted(): void
    {
        static::saved(function (self $user): void {
            if (filled($user->photo)) {
                ImageCompressionService::compress($user->photo, 'public');
            }
        });

        static::updating(function (self $user): void {
            if (! $user->isDirty('photo')) {
                return;
            }

            $oldPath = $user->getOriginal('photo');
            $newPath = $user->photo;

            if (filled($oldPath) && $oldPath !== $newPath) {
                Storage::disk('public')->delete($oldPath);
            }
        });

        static::deleted(function (self $user): void {
            if (filled($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
        });
    }

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