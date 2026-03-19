<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Questionnaire;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'questionnaire_id' => Questionnaire::query()->inRandomOrder()->value('id') ?? Questionnaire::factory(),
            'question_text' => fake()->sentence() . '?',
            'type' => fake()->randomElement(['pg', 'essay']),
            'weight' => fake()->numberBetween(1, 5),
            'order' => fake()->numberBetween(1, 50),
        ];
    }
}

