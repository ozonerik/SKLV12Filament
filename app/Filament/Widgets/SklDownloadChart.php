<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\InteractsWithAdminDashboardFilters;
use App\Models\Skl;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Cache;

class SklDownloadChart extends ChartWidget
{
    use InteractsWithAdminDashboardFilters;

    protected ?string $pollingInterval = null;

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

        ['downloaded' => $downloaded, 'not_downloaded' => $notDownloaded] = Cache::remember(
            "dashboard:skl-download:{$schoolYearId}",
            now()->addMinutes(3),
            function () use ($schoolYearId): array {
                $result = Skl::query()
                    ->join('students', 'students.id', '=', 'skls.student_id')
                    ->where('students.school_year_id', $schoolYearId)
                    ->selectRaw("SUM(CASE WHEN skls.downloaded_at IS NOT NULL OR skls.verification_code IS NOT NULL THEN 1 ELSE 0 END) as downloaded")
                    ->selectRaw("SUM(CASE WHEN skls.downloaded_at IS NULL AND skls.verification_code IS NULL THEN 1 ELSE 0 END) as not_downloaded")
                    ->first();

                return [
                    'downloaded' => (int) ($result?->downloaded ?? 0),
                    'not_downloaded' => (int) ($result?->not_downloaded ?? 0),
                ];
            }
        );

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