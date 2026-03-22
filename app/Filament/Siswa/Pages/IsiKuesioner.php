<?php

namespace App\Filament\Siswa\Pages;

use App\Models\Answer;
use App\Models\Questionnaire;
use App\Models\Skl;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Panel;

class IsiKuesioner extends Page
{
    protected static string $routePath = '/kuesioner';

    protected static ?string $title = 'Isi Kuesioner';

    protected static bool $shouldRegisterNavigation = true;

    public ?array $data = [];

    public ?int $questionnaireId = null;

    /**
     * @var array<int, array{ id: int, type: string }>
     */
    public array $questionMeta = [];

    public function mount(): void
    {
        $student = Filament::auth()->user();

        abort_unless($student, 403);

        $skl = Skl::query()
            ->where('student_id', $student->getAuthIdentifier())
            ->first();

        if ($skl && $skl->is_questionnaire_completed) {
            $this->redirect(KelulusanDanSkl::getUrl(panel: 'siswa'));

            return;
        }

        $questionnaire = Questionnaire::query()
            ->where('school_year_id', $student->school_year_id)
            ->where('is_active', true)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->orderByDesc('id')
            ->first();

        abort_unless($questionnaire, 404);

        $this->questionnaireId = $questionnaire->id;

        $questions = $questionnaire->questions()->with('options')->get();

        $this->questionMeta = $questions
            ->map(fn ($q) => ['id' => $q->id, 'type' => $q->type])
            ->values()
            ->all();

        // Prefill jika sudah pernah menjawab sebagian.
        $existingAnswers = Answer::query()
            ->where('student_id', $student->getAuthIdentifier())
            ->whereIn('question_id', $questions->pluck('id'))
            ->get()
            ->keyBy('question_id');

        $prefill = [];
        foreach ($questions as $question) {
            $answer = $existingAnswers->get($question->id);
            $key = "q_{$question->id}";

            if (! $answer) {
                continue;
            }

            $prefill[$key] = $question->type === 'pg'
                ? $answer->question_option_id
                : $answer->answer_text;
        }

        $this->form->fill($prefill);
    }

    public static function getRoutePath(Panel $panel): string
    {
        return static::$routePath;
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        $questionnaire = $this->getQuestionnaire();

        return $schema
            ->components(
                $questionnaire
                    ->questions()
                    ->with('options')
                    ->orderBy('order')
                    ->get()
                    ->map(function ($question) {
                        $key = "q_{$question->id}";

                        $component = $question->type === 'pg'
                            ? Radio::make($key)
                                ->label($question->question_text)
                                ->options($question->options->pluck('option_text', 'id')->all())
                                ->required()
                            : Textarea::make($key)
                                ->label($question->question_text)
                                ->required()
                                ->rows(3)
                                ->columnSpanFull();

                        return Section::make()
                            ->compact()
                            ->schema([$component]);
                    })
                    ->all()
            );
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('kuesioner-form')
                ->livewireSubmitHandler('submit')
                ->footer([
                    Actions::make([$this->getSubmitAction()])->fullWidth(),
                ]),
        ]);
    }

    protected function getSubmitAction(): Action
    {
        return Action::make('submit')
            ->label('Kirim Kuesioner')
            ->submit('submit');
    }

    public function submit(): void
    {
        $student = Filament::auth()->user();

        abort_unless($student, 403);

        $questionnaire = $this->getQuestionnaire();
        $questions = $questionnaire->questions()->with('options')->get();

        $data = $this->form->getState();

        foreach ($questions as $question) {
            $key = "q_{$question->id}";
            $value = $data[$key] ?? null;

            if ($question->type === 'pg') {
                Answer::query()->updateOrCreate(
                    [
                        'student_id' => $student->getAuthIdentifier(),
                        'question_id' => $question->id,
                    ],
                    [
                        'question_option_id' => (int) $value,
                        'answer_text' => null,
                    ],
                );
            } else {
                Answer::query()->updateOrCreate(
                    [
                        'student_id' => $student->getAuthIdentifier(),
                        'question_id' => $question->id,
                    ],
                    [
                        'question_option_id' => null,
                        'answer_text' => (string) $value,
                    ],
                );
            }
        }

        Skl::query()
            ->where('student_id', $student->getAuthIdentifier())
            ->update(['is_questionnaire_completed' => true]);

        $this->redirect(KelulusanDanSkl::getUrl(panel: 'siswa'));
    }

    protected function getQuestionnaire(): Questionnaire
    {
        $questionnaireId = $this->questionnaireId;

        abort_unless($questionnaireId, 404);

        return Questionnaire::query()->findOrFail($questionnaireId);
    }
}

