<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\InteractsWithAdminDashboardFilters;
use App\Models\Answer;
use App\Models\Question;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class QuestionnaireResultsChart extends ChartWidget
{
    use InteractsWithAdminDashboardFilters;

    protected int | string | array $columnSpan = 'full';

    protected ?string $heading = 'Hasil Kuesioner';

    public function getDescription(): string | Htmlable | null
    {
        return 'Distribusi jawaban opsi (A/B/C/...) untuk setiap pertanyaan pada ' . $this->getSelectedSchoolYearName() . '.';
    }

    protected function getData(): array
    {
        $schoolYearId = $this->getSelectedSchoolYearId();

        if (! $schoolYearId) {
            return [
                'labels' => ['Belum ada pertanyaan'],
                'datasets' => [[
                    'label' => 'Opsi A',
                    'data' => [0],
                    'backgroundColor' => ['#cbd5e1'],
                ]],
            ];
        }

        $questions = Question::query()
            ->where('type', 'pg')
            ->whereHas('questionnaire', fn ($query) => $query->where('school_year_id', $schoolYearId))
            ->with(['options' => fn ($query) => $query->orderBy('id')])
            ->orderBy('order')
            ->get();

        if ($questions->isEmpty()) {
            return [
                'labels' => ['Belum ada pertanyaan pilihan ganda'],
                'datasets' => [[
                    'label' => 'Opsi A',
                    'data' => [0],
                    'backgroundColor' => ['#cbd5e1'],
                ]],
            ];
        }

        $counts = Answer::query()
            ->selectRaw('answers.question_id, question_options.option_text as option_text, COUNT(*) as total')
            ->join('questions', 'questions.id', '=', 'answers.question_id')
            ->join('questionnaires', 'questionnaires.id', '=', 'questions.questionnaire_id')
            ->join('students', 'students.id', '=', 'answers.student_id')
            ->join('question_options', 'question_options.id', '=', 'answers.question_option_id')
            ->whereNotNull('answers.question_option_id')
            ->where('questionnaires.school_year_id', $schoolYearId)
            ->where('students.school_year_id', $schoolYearId)
            ->groupBy('answers.question_id', 'question_options.option_text')
            ->get();

        $countsMap = $counts->mapWithKeys(
            fn ($row) => [((string) $row->question_id . ':' . (string) $row->option_text) => (int) $row->total]
        );

        $optionTexts = $questions
            ->flatMap(fn ($question) => $question->options->pluck('option_text'))
            ->filter(fn ($optionText) => filled($optionText))
            ->unique()
            ->values();

        if ($optionTexts->isEmpty()) {
            return [
                'labels' => ['Belum ada opsi jawaban'],
                'datasets' => [[
                    'label' => 'Opsi',
                    'data' => [0],
                    'backgroundColor' => ['#cbd5e1'],
                ]],
            ];
        }

        $palette = ['#2563eb', '#16a34a', '#f59e0b', '#dc2626', '#7c3aed', '#0ea5e9', '#ec4899', '#14b8a6', '#84cc16', '#f97316'];

        $labels = $questions
            ->values()
            ->map(fn ($question, $index) => 'Q' . ($index + 1) . ' - ' . Str::limit((string) $question->question_text, 28));

        $datasets = $optionTexts
            ->values()
            ->map(function (string $optionText, int $optionIndex) use ($questions, $countsMap, $palette): array {
                $optionCode = chr(65 + $optionIndex);

                return [
                    'label' => 'Opsi ' . $optionCode . ' - ' . $optionText,
                    'data' => $questions
                        ->map(function ($question) use ($optionText, $countsMap): int {
                            $key = (string) $question->id . ':' . $optionText;

                            return (int) ($countsMap[$key] ?? 0);
                        })
                        ->all(),
                    'backgroundColor' => $palette[$optionIndex % count($palette)],
                ];
            })
            ->all();

        return [
            'labels' => $labels->all(),
            'datasets' => $datasets,
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
            'scales' => [
                'x' => [
                    'stacked' => false,
                ],
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}