<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Headmaster;
use App\Models\Major;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class AcademicSeeder extends Seeder
{
    public function run(): void
    {
        // Headmaster
        $headmaster = Headmaster::query()->firstOrCreate(
            ['nip' => '197001012005011001'],
            [
                'name' => 'Kepala Sekolah',
                'rank' => 'Pembina (IV/a)',
                'ttd' => 'path/to/signature.png',
                'is_active' => true,
            ],
        );

        // School year(s)
        $currentStartYear = (int) now()->format('Y');
        $years = [
            [$currentStartYear - 1, $currentStartYear],
            [$currentStartYear, $currentStartYear + 1],
        ];

        // 1. Capture the school years after creating them
        $schoolYears = [];
        foreach ($years as [$startYear, $endYear]) {
            $kode = mb_substr((string) $startYear, -2) . mb_substr((string) $endYear, -2);

            $schoolYears[] = SchoolYear::query()->updateOrCreate(
                ['kode' => $kode],
                [
                    'name' => "{$startYear}/{$endYear}",
                    'headmaster_id' => $headmaster->id,
                ],
            );
        }
        // Majors (for students)
        if (! Major::query()->exists()) {
            Major::factory()->count(3)->create();
        }

        // Students
        $activeSchoolYearId = end($schoolYears)->id;
        if (! Student::query()->exists()) {
            Student::factory()
                ->count(5)
                ->create([
                    'school_year_id' => $activeSchoolYearId // Pass the required ID here
                ]);
        }

        // Subjects
        if (! Subject::query()->exists()) {
            Subject::factory()->count(8)->create();
        }

        // Grades: each student gets one grade per subject
        $subjects = Subject::query()->get();
        Student::query()->get()->each(function (Student $student) use ($subjects): void {
            foreach ($subjects as $subject) {
                Grade::query()->firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'subject_id' => $subject->id,
                    ],
                    [
                        'score' => fake()->numberBetween(60, 100),
                    ],
                );
            }
        });
    }
}
