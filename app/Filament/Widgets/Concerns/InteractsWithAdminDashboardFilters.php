<?php

namespace App\Filament\Widgets\Concerns;

use App\Models\SchoolYear;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

trait InteractsWithAdminDashboardFilters
{
    use InteractsWithPageFilters;

    protected function getSelectedSchoolYearId(): ?int
    {
        $schoolYearId = $this->pageFilters['school_year_id'] ?? null;

        if (filled($schoolYearId)) {
            return (int) $schoolYearId;
        }

        return SchoolYear::query()->orderByDesc('id')->value('id');
    }

    protected function getSelectedSchoolYearName(): string
    {
        $schoolYearId = $this->getSelectedSchoolYearId();

        if (! $schoolYearId) {
            return 'Belum ada tahun pelajaran';
        }

        return SchoolYear::query()->whereKey($schoolYearId)->value('name') ?? 'Tahun pelajaran tidak ditemukan';
    }
}