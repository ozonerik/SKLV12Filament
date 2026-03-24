<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\GraduationStatusChart;
use App\Filament\Widgets\QuestionDistributionPieChart;
use App\Filament\Widgets\SklDownloadChart;
use App\Models\Answer;
use App\Models\Question;
use App\Models\School;
use App\Models\SchoolYear;
use App\Models\Skl;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AdminDashboard extends Dashboard
{
    use HasFiltersForm;

    protected static ?string $title = 'Dashboard';

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_year_id')
                    ->label('Tahun Pelajaran')
                    ->options(SchoolYear::query()->orderByDesc('id')->pluck('name', 'id')->all())
                    ->default(SchoolYear::query()->orderByDesc('id')->value('id'))
                    ->searchable()
                    ->preload()
                    ->selectablePlaceholder(false)
                    ->required(),
            ]);
    }

    public function getWidgets(): array
    {
        $widgets = [
            GraduationStatusChart::class,
            SklDownloadChart::class,
        ];

        $schoolYearId = $this->getSelectedSchoolYearId();

        if (! $schoolYearId) {
            return $widgets;
        }

        $questions = Question::query()
            ->where('type', 'pg')
            ->whereHas('questionnaire', fn (Builder $query) => $query->where('school_year_id', $schoolYearId))
            ->orderBy('order')
            ->get(['id', 'question_text'])
            ->values();

        foreach ($questions as $index => $question) {
            $widgets[] = QuestionDistributionPieChart::make([
                'questionId' => $question->id,
                'questionLabel' => 'Q' . ($index + 1) . ' - ' . Str::limit((string) $question->question_text, 90),
            ]);
        }

        return $widgets;
    }

    public function getColumns(): int | array
    {
        return [
            'md' => 2,
            'xl' => 2,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadReport')
                ->label('Download Laporan (PDF)')
                ->icon('heroicon-o-arrow-down-tray')
                ->action('downloadReportPdf'),
        ];
    }

    public function downloadReportPdf()
    {
        $schoolYearId = $this->getSelectedSchoolYearId();
        abort_unless($schoolYearId, 404);

        $schoolYearName = SchoolYear::query()->whereKey($schoolYearId)->value('name') ?? '-';

        $baseSklQuery = Skl::query()
            ->whereHas('student', fn (Builder $query) => $query->where('school_year_id', $schoolYearId));

        $lulus = (clone $baseSklQuery)->where('status', 'Lulus')->count();
        $tidakLulus = (clone $baseSklQuery)->whereIn('status', ['Tidak Lulus', 'Tidak lulus'])->count();
        $downloaded = (clone $baseSklQuery)
            ->where(function (Builder $query): void {
                $query->whereNotNull('downloaded_at')
                    ->orWhereNotNull('verification_code');
            })
            ->count();
        $notDownloaded = (clone $baseSklQuery)
            ->whereNull('downloaded_at')
            ->whereNull('verification_code')
            ->count();

        $questionDistributions = $this->getQuestionDistributions($schoolYearId);
        $school = School::query()->first();

        $pdf = Pdf::loadView('pdf.admin-dashboard-report', [
            'generatedAt' => now(),
            'school' => $school,
            'schoolYearName' => $schoolYearName,
            'graduationData' => [
                'Lulus' => $lulus,
                'Tidak Lulus' => $tidakLulus,
            ],
            'downloadData' => [
                'Sudah Download' => $downloaded,
                'Belum Download' => $notDownloaded,
            ],
            'questionDistributions' => $questionDistributions,
        ])->setPaper([0, 0, 595.28, 935.43], 'portrait');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'laporan-dashboard-' . str_replace('/', '-', $schoolYearName) . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    protected function getSelectedSchoolYearId(): ?int
    {
        $schoolYearId = $this->filters['school_year_id'] ?? null;

        if (filled($schoolYearId)) {
            return (int) $schoolYearId;
        }

        return SchoolYear::query()->orderByDesc('id')->value('id');
    }

    /**
     * @return Collection<int, array{question_label: string, question_text: string, options: Collection<int, array{option_label: string, option_text: string, total: int}>}>
     */
    protected function getQuestionDistributions(int $schoolYearId): Collection
    {
        $questions = Question::query()
            ->where('type', 'pg')
            ->whereHas('questionnaire', fn (Builder $query) => $query->where('school_year_id', $schoolYearId))
            ->with(['options' => fn ($query) => $query->orderBy('id')])
            ->orderBy('order')
            ->get()
            ->values();

        if ($questions->isEmpty()) {
            return collect();
        }

        $counts = Answer::query()
            ->selectRaw('answers.question_id, answers.question_option_id, COUNT(*) as total')
            ->join('questions', 'questions.id', '=', 'answers.question_id')
            ->join('questionnaires', 'questionnaires.id', '=', 'questions.questionnaire_id')
            ->join('students', 'students.id', '=', 'answers.student_id')
            ->whereNotNull('answers.question_option_id')
            ->where('questionnaires.school_year_id', $schoolYearId)
            ->where('students.school_year_id', $schoolYearId)
            ->groupBy('answers.question_id', 'answers.question_option_id')
            ->get()
            ->mapWithKeys(
                fn ($row) => [((string) $row->question_id . ':' . (string) $row->question_option_id) => (int) $row->total]
            );

        return $questions->map(function ($question, $index) use ($counts): array {
            return [
                'question_label' => 'Q' . ($index + 1),
                'question_text' => (string) $question->question_text,
                'options' => $question->options
                    ->values()
                    ->map(function ($option, $optionIndex) use ($question, $counts): array {
                        $key = (string) $question->id . ':' . (string) $option->id;

                        return [
                            'option_label' => 'Opsi ' . chr(65 + $optionIndex),
                            'option_text' => (string) $option->option_text,
                            'total' => (int) ($counts[$key] ?? 0),
                        ];
                    }),
            ];
        });
    }
}