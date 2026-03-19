<?php

namespace Database\Factories;

use App\Models\Major;
use App\Models\SchoolYear;
use App\Models\Skl;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Skl>
 */
class SklFactory extends Factory
{
    protected $model = Skl::class;

    public function definition(): array
    {
        $letterDate = now()->subDays(fake()->numberBetween(0, 30))->toDateString();

        return [
            'student_id' => Student::query()->inRandomOrder()->value('id') ?? Student::factory(),
            'major_id' => Major::query()->inRandomOrder()->value('id') ?? Major::factory(),
            'school_year_id' => SchoolYear::query()->inRandomOrder()->value('id') ?? SchoolYear::factory(),
            'letter_number' => sprintf('%03d/SKL/%s/%s', fake()->numberBetween(1, 999), now()->format('m'), now()->format('Y')),
            'status' => fake()->randomElement(['Lulus', 'Tidak Lulus']),
            'letter_date' => $letterDate,
            'published_at' => now()->subMinutes(fake()->numberBetween(0, 60 * 24)),
            'is_questionnaire_completed' => fake()->boolean(70),
        ];
    }
}

