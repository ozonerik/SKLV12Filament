<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Major>
 */
class MajorFactory extends Factory
{
    public function definition(): array
    {
        $program = fake()->randomElement([
            'Rekayasa Perangkat Lunak',
            'Teknik Komputer dan Jaringan',
            'Multimedia',
            'Akuntansi',
            'Perhotelan',
        ]);

        return [
            'bidang_keahlian' => fake()->randomElement([
                'Teknologi Informasi',
                'Bisnis dan Manajemen',
                'Pariwisata',
            ]),
            'program_keahlian' => $program,
            'konsentrasi_keahlian' => fake()->words(asText: true),
            'kode_jurusan' => strtoupper(substr($program, 0, 3)) . '-' . fake()->unique()->numberBetween(100, 999),
        ];
    }
}

