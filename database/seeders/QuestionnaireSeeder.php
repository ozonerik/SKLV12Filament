<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionOption;
use App\Models\Student;
use Illuminate\Database\Seeder;

class QuestionnaireSeeder extends Seeder
{
    public function run(): void
    {
        $questionnaire = Questionnaire::query()->firstOrCreate(
            ['title' => 'Kuesioner Kepuasan Siswa'],
            [
                'description' => 'Kuesioner untuk mengukur kepuasan siswa.',
                'start_date' => now()->subDays(3)->toDateString(),
                'end_date' => now()->addDays(14)->toDateString(),
                'is_active' => true,
            ],
        );

        if (! $questionnaire->questions()->exists()) {
            // 5 pilihan ganda
            foreach (range(1, 5) as $i) {
                $question = Question::create([
                    'questionnaire_id' => $questionnaire->id,
                    'question_text' => "Seberapa puas kamu dengan layanan sekolah? (Q{$i})",
                    'type' => 'pg',
                    'weight' => 1,
                    'order' => $i,
                ]);

                foreach (['Sangat Setuju', 'Setuju', 'Netral', 'Tidak Setuju', 'Sangat Tidak Setuju'] as $optionText) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $optionText,
                    ]);
                }
            }

            // 2 essay
            foreach (range(6, 7) as $i) {
                Question::create([
                    'questionnaire_id' => $questionnaire->id,
                    'question_text' => "Masukan/saran untuk sekolah (Q{$i})",
                    'type' => 'essay',
                    'weight' => 1,
                    'order' => $i,
                ]);
            }
        }

        $questions = $questionnaire->questions()->with('options')->get();
        $students = Student::query()->get();

        foreach ($students as $student) {
            foreach ($questions as $question) {
                $base = [
                    'student_id' => $student->id,
                    'question_id' => $question->id,
                ];

                if ($question->type === 'pg') {
                    $optionId = $question->options->random()->id;

                    Answer::query()->firstOrCreate(
                        $base,
                        [
                            'question_option_id' => $optionId,
                            'answer_text' => null,
                        ],
                    );
                } else {
                    Answer::query()->firstOrCreate(
                        $base,
                        [
                            'question_option_id' => null,
                            'answer_text' => fake()->sentences(asText: true),
                        ],
                    );
                }
            }
        }
    }
}

