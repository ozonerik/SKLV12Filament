<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Skl;
use App\Models\Student;
use Illuminate\Database\Seeder;

class SklSeeder extends Seeder
{
    public function run(): void
    {
        //$schoolYearId = SchoolYear::query()->inRandomOrder()->value('id');

        Student::query()->get()->each(function (Student $student) : void {
            $hasAnsweredQuestionnaire = Answer::query()->where('student_id', $student->id)->exists();
            $hasDownloaded = $hasAnsweredQuestionnaire && fake()->boolean(65);

            Skl::query()->updateOrCreate(
                ['student_id' => $student->id],
                [
                    //'major_id' => $student->major_id,
                    //'school_year_id' => $schoolYearId,
                    'letter_number' => sprintf('%03d/SKL/%s/%s', $student->id, now()->format('m'), now()->format('Y')),
                    'status' => fake()->randomElement(['Lulus', 'Tidak Lulus']),
                    'letter_date' => now()->toDateString(),
                    'published_at' => now()->subMinutes(10),
                    'verification_code' => $hasDownloaded ? Skl::generateVerificationCode() : null,
                    'downloaded_at' => $hasDownloaded ? now()->subMinutes(fake()->numberBetween(1, 120)) : null,
                    'is_questionnaire_completed' => $hasAnsweredQuestionnaire ? true : null,
                ],
            );
        });
    }
}

