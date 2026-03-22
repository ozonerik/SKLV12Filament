<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\InteractsWithAdminDashboardFilters;
use App\Models\Skl;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class SklDownloadChart extends ChartWidget
{
    use InteractsWithAdminDashboardFilters;

    protected int | string | array $columnSpan = 1;

    protected ?string $heading = 'Unduhan SKL';

    public function getDescription(): string | Htmlable | null
    {
        return 'Perbandingan siswa yang sudah dan belum mengunduh SKL pada ' . $this->getSelectedSchoolYearName() . '.';
    }

    protected function getData(): array
    {
        $schoolYearId = $this->getSelectedSchoolYearId();

        if (! $schoolYearId) {
            return [
                'labels' => ['Sudah Download', 'Belum Download'],
                'datasets' => [[
                    'label' => 'Jumlah Siswa',
                    'data' => [0, 0],
                    'backgroundColor' => ['#2563eb', '#cbd5e1'],
                ]],
            ];
        }

        $baseQuery = Skl::query()
            ->whereHas('student', fn (Builder $query) => $query->where('school_year_id', $schoolYearId));

        $downloaded = (clone $baseQuery)
            ->whereNotNull('downloaded_at')
            ->count();

        $notDownloaded = (clone $baseQuery)
            ->whereNull('downloaded_at')
            ->count();

        return [
            'labels' => ['Sudah Download', 'Belum Download'],
            'datasets' => [[
                'label' => 'Jumlah Siswa',
                'data' => [$downloaded, $notDownloaded],
                'backgroundColor' => ['#2563eb', '#cbd5e1'],
            ]],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'persistentDataLabels' => [
                    'enabled' => true,
                    'color' => '#111827',
                ],
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}