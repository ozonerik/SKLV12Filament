<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Skl extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'letter_number', 'verification_code', 'status',
        'letter_date', 'published_at', 'downloaded_at', 'download_count', 'is_questionnaire_completed'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'downloaded_at' => 'datetime',
        'download_count' => 'integer',
        'letter_date' => 'date',
        'is_questionnaire_completed' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $skl): void {
            if (filled($skl->verification_code)) {
                return;
            }

            $skl->verification_code = self::generateVerificationCode($skl->id);
        });
    }

    public function student() { return $this->belongsTo(Student::class); }

    public static function generateVerificationCode(?int $ignoreId = null): string
    {
        do {
            $code = strtoupper(Str::random(12));
            $exists = self::query()
                ->where('verification_code', $code)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists();
        } while ($exists);

        return $code;
    }

    public function ensureVerificationCode(): string
    {
        if (filled($this->verification_code)) {
            return (string) $this->verification_code;
        }

        $this->verification_code = self::generateVerificationCode($this->id);
        $this->save();

        return (string) $this->verification_code;
    }
    
    // Helper untuk cek apakah sudah bisa diakses
    public function isPublished(): bool
    {
        return now()->greaterThanOrEqualTo($this->published_at);
    }

    public function hasBeenDownloaded(): bool
    {
        return filled($this->downloaded_at) || ((int) $this->download_count > 0);
    }
}
