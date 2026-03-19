<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\SchoolYear;
use App\Models\Skl;
use App\Models\Student;
use Illuminate\Database\Seeder;

class SklSeeder extends Seeder
{
    public function run(): void
    {
        //$schoolYearId = SchoolYear::query()->inRandomOrder()->value('id');

        Student::query()->get()->each(function (Student $student) : void {
            Skl::query()->updateOrCreate(
                ['student_id' => $student->id],
                [
                    //'major_id' => $student->major_id,
                    //'school_year_id' => $schoolYearId,
                    'letter_number' => sprintf('%03d/SKL/%s/%s', $student->id, now()->format('m'), now()->format('Y')),
                    'status' => 'Lulus',
                    'letter_date' => now()->toDateString(),
                    'published_at' => now()->subMinutes(10),
                    'is_questionnaire_completed' => Answer::query()->where('student_id', $student->id)->exists(),
                ],
            );
        });
    }
}

