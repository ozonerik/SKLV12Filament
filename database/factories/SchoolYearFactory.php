<?php

namespace Database\Factories;

use App\Models\Headmaster;
use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SchoolYear>
 */
class SchoolYearFactory extends Factory
{
    protected $model = SchoolYear::class;

    public function definition(): array
    {
        $startYear = (int) fake()->numberBetween((int) now()->subYears(2)->format('Y'), (int) now()->format('Y'));
        $endYear = $startYear + 1;
        $kode = mb_substr((string) $startYear, -2) . mb_substr((string) $endYear, -2);

        return [
            'kode' => $kode,
            'name' => "{$startYear}/{$endYear}",
            'headmaster_id' => Headmaster::query()->where('is_active', true)->value('id') ?? Headmaster::factory(),
        ];
    }
}

