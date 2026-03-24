<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\InteractsWithAdminDashboardFilters;
use App\Models\Answer;
use App\Models\Question;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Cache;

class QuestionDistributionPieChart extends ChartWidget
{
    use InteractsWithAdminDashboardFilters;

    protected ?string $pollingInterval = null;

    protected int | string | array $columnSpan = 1;

    public ?int $questionId = null;

    public ?string $questionLabel = null;

    public function getHeading(): string | Htmlable | null
    {
        return $this->questionLabel ?? 'Distribusi Jawaban';
    }

    public function getDescription(): string | Htmlable | null
    {
        return 'Distribusi jawaban siswa per opsi.';
    }

    protected function getData(): array
    {
        $schoolYearId = $this->getSelectedSchoolYearId();

        if (! $schoolYearId || ! $this->questionId) {
            return [
                'labels' => ['Belum ada data'],
                'datasets' => [[
                    'label' => 'Jumlah Jawaban',
                    'data' => [0],
                    'backgroundColor' => ['#cbd5e1'],
                ]],
            ];
        }

        return Cache::remember(
            "dashboard:question-distribution:{$schoolYearId}:{$this->questionId}",
            now()->addMinutes(3),
            function () use ($schoolYearId): array {
                $question = Question::query()
                    ->whereKey($this->questionId)
                    ->where('type', 'pg')
                    ->whereHas('questionnaire', fn ($query) => $query->where('school_year_id', $schoolYearId))
                    ->with(['options' => fn ($query) => $query->orderBy('id')])
                    ->first();

                if (! $question) {
                    return [
                        'labels' => ['Pertanyaan tidak tersedia'],
                        'datasets' => [[
                            'label' => 'Jumlah Jawaban',
                            'data' => [0],
                            'backgroundColor' => ['#cbd5e1'],
                        ]],
                    ];
                }

                $counts = Answer::query()
                    ->selectRaw('answers.question_option_id, COUNT(*) as total')
                    ->join('students', 'students.id', '=', 'answers.student_id')
                    ->where('answers.question_id', $question->id)
                    ->where('students.school_year_id', $schoolYearId)
                    ->whereNotNull('answers.question_option_id')
                    ->groupBy('answers.question_option_id')
                    ->pluck('total', 'question_option_id');

                $labels = [];
                $data = [];

                foreach ($question->options->values() as $optionIndex => $option) {
                    $labels[] = 'Opsi ' . chr(65 + $optionIndex) . ' - ' . $option->option_text;
                    $data[] = (int) ($counts[$option->id] ?? 0);
                }

                if (count($labels) === 0) {
                    return [
                        'labels' => ['Belum ada opsi'],
                        'datasets' => [[
                            'label' => 'Jumlah Jawaban',
                            'data' => [0],
                            'backgroundColor' => ['#cbd5e1'],
                        ]],
                    ];
                }

                $palette = ['#2563eb', '#16a34a', '#f59e0b', '#dc2626', '#7c3aed', '#0ea5e9', '#ec4899', '#14b8a6', '#84cc16', '#f97316'];

                return [
                    'labels' => $labels,
                    'datasets' => [[
                        'label' => 'Jumlah Jawaban',
                        'data' => $data,
                        'backgroundColor' => collect($labels)->values()->map(fn ($label, $index) => $palette[$index % count($palette)])->all(),
                    ]],
                ];
            }
        );
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
