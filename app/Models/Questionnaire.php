<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Questionnaire extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'school_year_id', 'start_date', 'end_date', 'is_active'];

    public function questions() { 
        return $this->hasMany(Question::class)->orderBy('order'); 
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public static function hasActiveOverlapForSchoolYear(
        int $schoolYearId,
        string $startDate,
        string $endDate,
        ?int $ignoreId = null,
    ): bool {
        return self::query()
            ->where('is_active', true)
            ->where('school_year_id', $schoolYearId)
            ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate)
            ->exists();
    }
}
