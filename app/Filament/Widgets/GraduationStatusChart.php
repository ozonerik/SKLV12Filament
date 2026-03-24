<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\InteractsWithAdminDashboardFilters;
use App\Models\Skl;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Cache;

class GraduationStatusChart extends ChartWidget
{
    use InteractsWithAdminDashboardFilters;

    protected ?string $pollingInterval = null;

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

        ['lulus' => $lulus, 'tidak_lulus' => $tidakLulus] = Cache::remember(
            "dashboard:graduation-status:{$schoolYearId}",
            now()->addMinutes(3),
            function () use ($schoolYearId): array {
                $result = Skl::query()
                    ->join('students', 'students.id', '=', 'skls.student_id')
                    ->where('students.school_year_id', $schoolYearId)
                    ->selectRaw("SUM(CASE WHEN skls.status = 'Lulus' THEN 1 ELSE 0 END) as lulus")
                    ->selectRaw("SUM(CASE WHEN LOWER(skls.status) = 'tidak lulus' THEN 1 ELSE 0 END) as tidak_lulus")
                    ->first();

                return [
                    'lulus' => (int) ($result?->lulus ?? 0),
                    'tidak_lulus' => (int) ($result?->tidak_lulus ?? 0),
                ];
            }
        );

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