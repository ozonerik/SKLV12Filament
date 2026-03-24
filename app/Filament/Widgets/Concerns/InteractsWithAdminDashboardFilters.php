<?php

namespace App\Filament\Widgets\Concerns;

use App\Models\SchoolYear;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\Cache;

trait InteractsWithAdminDashboardFilters
{
    use InteractsWithPageFilters;

    protected function getSelectedSchoolYearId(): ?int
    {
        $schoolYearId = $this->pageFilters['school_year_id'] ?? null;

        if (filled($schoolYearId)) {
            return (int) $schoolYearId;
        }

        return Cache::remember('school_year:latest_id', now()->addMinutes(10), fn () =>
            SchoolYear::query()->orderByDesc('id')->value('id')
        );
    }

    protected function getSelectedSchoolYearName(): string
    {
        $schoolYearId = $this->getSelectedSchoolYearId();

        if (! $schoolYearId) {
            return 'Belum ada tahun pelajaran';
        }

        return Cache::remember("school_year:name:{$schoolYearId}", now()->addMinutes(10), fn () =>
            SchoolYear::query()->whereKey($schoolYearId)->value('name') ?? 'Tahun pelajaran tidak ditemukan'
        );
    }
}