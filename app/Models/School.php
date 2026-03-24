<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ImageCompressionService;
use Illuminate\Support\Facades\Storage;

class School extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(function (self $school): void {
            foreach (['province_logo', 'school_logo', 'school_stamp'] as $attribute) {
                if (($school->wasChanged($attribute) || $school->wasRecentlyCreated) && filled($school->{$attribute})) {
                    ImageCompressionService::compress($school->{$attribute}, 'public');
                }
            }
        });

        static::updating(function (self $school): void {
            $fileAttributes = ['province_logo', 'school_logo', 'school_stamp'];

            foreach ($fileAttributes as $attribute) {
                if (! $school->isDirty($attribute)) {
                    continue;
                }

                $oldPath = $school->getOriginal($attribute);
                $newPath = $school->{$attribute};

                if (filled($oldPath) && $oldPath !== $newPath) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        });

        static::deleted(function (self $school): void {
            foreach (['province_logo', 'school_logo', 'school_stamp'] as $attribute) {
                if (filled($school->{$attribute})) {
                    Storage::disk('public')->delete($school->{$attribute});
                }
            }
        });
    }

    protected $fillable = [
        'name',
        'vision',
        'address',
        'city',
        'postal_code',
        'website',
        'email',
        'phone',
        'province',
        'kcd_wilayah',
        'province_logo',
        'school_logo',
        'school_stamp',
    ];
}
