<?php

namespace Database\Factories;

use App\Models\Questionnaire;
use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Questionnaire>
 */
class QuestionnaireFactory extends Factory
{
    protected $model = Questionnaire::class;

    public function definition(): array
    {
        $start = now()->subDays(fake()->numberBetween(0, 10))->toDateString();
        $end = now()->addDays(fake()->numberBetween(5, 30))->toDateString();

        return [
            'title' => 'Kuesioner Kepuasan ' . fake()->randomElement(['Siswa', 'Layanan', 'Sekolah']),
            'description' => fake()->optional()->sentence(),
            'school_year_id' => SchoolYear::query()->inRandomOrder()->value('id') ?? SchoolYear::factory(),
            'start_date' => $start,
            'end_date' => $end,
            'is_active' => true,
        ];
    }
}

