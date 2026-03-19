<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Headmaster;
use App\Models\Major;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SchoolYear;
use App\Models\Grade;
use App\Models\Skl;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@test.id',
            'password' => Hash::makee('12345678'),
            'is_admin' => true,
        ]);
    }
}
