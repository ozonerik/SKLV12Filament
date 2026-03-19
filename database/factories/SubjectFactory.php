<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Bahasa Indonesia',
                'Matematika',
                'Bahasa Inggris',
                'Pendidikan Agama',
                'PPKn',
                'Sejarah Indonesia',
                'Informatika',
                'Produktif Kejuruan',
                'Mulok',
            ]) . ' ' . fake()->randomElement(['', '1', '2', '3']),
            'category' => fake()->randomElement(['Umum', 'Kejuruan', 'Pilihan', 'Mulok']),
        ];
    }
}

