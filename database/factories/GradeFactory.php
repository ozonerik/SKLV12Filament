<?php

namespace Database\Factories;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Grade>
 */
class GradeFactory extends Factory
{
    protected $model = Grade::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::query()->inRandomOrder()->value('id') ?? Student::factory(),
            'subject_id' => Subject::query()->inRandomOrder()->value('id') ?? Subject::factory(),
            'score' => fake()->numberBetween(60, 100),
        ];
    }
}

