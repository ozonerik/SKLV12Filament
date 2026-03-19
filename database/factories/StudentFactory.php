<?php

namespace Database\Factories;

use App\Models\Major;
use App\Models\SchoolYear;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        $dob = fake()->dateTimeBetween('-19 years', '-15 years');
        $passwordPlain = $dob->format('dmY'); // ddmmyyyy

        return [
            'major_id' => Major::query()->inRandomOrder()->value('id') ?? Major::factory(),
            'school_year_id' => SchoolYear::query()->inRandomOrder()->value('id') ?? SchoolYear::factory(),
            'name' => fake()->name(),
            'pob' => fake()->city(),
            'dob' => $dob->format('Y-m-d'),
            'nis' => (string) fake()->unique()->numberBetween(1000000000, 9999999999),
            'nisn' => (string) fake()->unique()->numberBetween(1000000000, 9999999999),
            'father_name' => fake()->name('male'),
            // Login siswa: username=NISN, password=tanggal lahir ddmmyyyy (akan di-hash oleh cast).
            'password' => $passwordPlain,
        ];
    }
}

