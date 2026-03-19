<?php

namespace Database\Seeders;

use App\Models\Major;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        if (! Major::query()->exists()) {
            Major::factory()->count(3)->create();
        }

        Student::factory()->count(5)->create();
    }
}

