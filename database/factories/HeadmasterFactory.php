<?php

namespace Database\Factories;

use App\Models\Headmaster;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Headmaster>
 */
class HeadmasterFactory extends Factory
{
    protected $model = Headmaster::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'rank' => fake()->randomElement([
                'Pembina Utama Muda (IV/c)',
                'Pembina Tingkat I (IV/b)',
                'Pembina (IV/a)',
            ]),
            'nip' => (string) fake()->unique()->numberBetween(100000000000000000, 999999999999999999),
            'is_active' => true,
        ];
    }
}

