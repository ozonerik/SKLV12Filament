<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Answer>
 */
class AnswerFactory extends Factory
{
    protected $model = Answer::class;

    public function definition(): array
    {
        $questionId = Question::query()->inRandomOrder()->value('id') ?? Question::factory();

        return [
            'student_id' => Student::query()->inRandomOrder()->value('id') ?? Student::factory(),
            'question_id' => $questionId,
            'question_option_id' => null,
            'answer_text' => fake()->sentence(),
        ];
    }
}

