<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuestionOption>
 */
class QuestionOptionFactory extends Factory
{
    protected $model = QuestionOption::class;

    public function definition(): array
    {
        return [
            'question_id' => Question::query()->inRandomOrder()->value('id') ?? Question::factory()->state(['type' => 'pg']),
            'option_text' => fake()->randomElement([
                'Sangat Setuju',
                'Setuju',
                'Netral',
                'Tidak Setuju',
                'Sangat Tidak Setuju',
            ]),
        ];
    }
}

