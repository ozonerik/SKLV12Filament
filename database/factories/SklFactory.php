<?php

namespace Database\Factories;

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
        $publishedAt = now()->subMinutes(fake()->numberBetween(0, 60 * 24));
        $hasDownloaded = fake()->boolean(60);
        $downloadedAt = $hasDownloaded
            ? $publishedAt->copy()->addMinutes(fake()->numberBetween(1, 240))->min(now())
            : null;

        return [
            'student_id' => Student::query()->inRandomOrder()->value('id') ?? Student::factory(),
            'letter_number' => sprintf('%03d/SKL/%s/%s', fake()->numberBetween(1, 999), now()->format('m'), now()->format('Y')),
            'status' => fake()->randomElement(['Lulus', 'Tidak Lulus']),
            'letter_date' => $letterDate,
            'published_at' => $publishedAt,
            'downloaded_at' => $downloadedAt,
            'download_count' => $hasDownloaded ? fake()->numberBetween(1, 3) : 0,
            'is_questionnaire_completed' => fake()->boolean(70),
        ];
    }
}

