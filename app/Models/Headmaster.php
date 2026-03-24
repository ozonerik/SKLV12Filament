<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\ImageCompressionService;
use Illuminate\Support\Facades\Storage;

class Headmaster extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(function (self $headmaster): void {
            if (($headmaster->wasChanged('ttd') || $headmaster->wasRecentlyCreated) && filled($headmaster->ttd)) {
                ImageCompressionService::compress($headmaster->ttd, 'public');
            }
        });

        static::updating(function (self $headmaster): void {
            if (! $headmaster->isDirty('ttd')) {
                return;
            }

            $oldPath = $headmaster->getOriginal('ttd');
            $newPath = $headmaster->ttd;

            if (filled($oldPath) && $oldPath !== $newPath) {
                Storage::disk('public')->delete($oldPath);
            }
        });

        static::deleted(function (self $headmaster): void {
            if (filled($headmaster->ttd)) {
                Storage::disk('public')->delete($headmaster->ttd);
            }
        });
    }

    protected $fillable = ['name', 'rank', 'nip', 'ttd', 'is_active'];

    public function schoolYears(): HasMany
    {
        return $this->hasMany(SchoolYear::class);
    }
}
