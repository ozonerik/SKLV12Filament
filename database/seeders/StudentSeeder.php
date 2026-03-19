<?php

namespace Database\Seeders;

use App\Models\Major;
use App\Models\SchoolYear;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $schoolYearId = SchoolYear::query()->inRandomOrder()->value('id');
        $majorId = Major::query()->inRandomOrder()->value('id');

        if (! Major::query()->exists()) {
            Major::factory()->count(3)->create();
        }

        if (! SchoolYear::query()->exists()) {
            SchoolYear::factory()->count(3)->create();
        }

        Student::factory()->count(5)->create();
    }
}

