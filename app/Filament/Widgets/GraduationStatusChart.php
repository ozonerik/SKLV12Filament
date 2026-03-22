<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\InteractsWithAdminDashboardFilters;
use App\Models\Skl;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;

class GraduationStatusChart extends ChartWidget
{
    use InteractsWithAdminDashboardFilters;

    protected int | string | array $columnSpan = 1;

    protected ?string $heading = 'Distribusi Kelulusan';

    public function getDescription(): string | Htmlable | null
    {
        return 'Jumlah siswa lulus dan tidak lulus untuk ' . $this->getSelectedSchoolYearName() . '.';
    }

    protected function getData(): array
    {
        $schoolYearId = $this->getSelectedSchoolYearId();

        if (! $schoolYearId) {
            return [
                'labels' => ['Lulus', 'Tidak Lulus'],
                'datasets' => [[
                    'data' => [0, 0],
                    'backgroundColor' => ['#16a34a', '#dc2626'],
                ]],
            ];
        }

        $baseQuery = Skl::query()
            ->whereHas('student', fn (Builder $query) => $query->where('school_year_id', $schoolYearId));

        $lulus = (clone $baseQuery)
            ->where('status', 'Lulus')
            ->count();

        $tidakLulus = (clone $baseQuery)
            ->whereIn('status', ['Tidak Lulus', 'Tidak lulus'])
            ->count();

        return [
            'labels' => ['Lulus', 'Tidak Lulus'],
            'datasets' => [[
                'label' => 'Jumlah Siswa',
                'data' => [$lulus, $tidakLulus],
                'backgroundColor' => ['#16a34a', '#dc2626'],
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