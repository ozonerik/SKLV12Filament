<?php

namespace Database\Seeders;

use App\Models\User;
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

        User::query()->updateOrCreate([
            'email' => 'admin@test.id',
        ], [
            'name' => 'Admin',
            'password' => Hash::make('12345678'),
            'is_admin' => true,
        ]);

        $this->call([
            AcademicSeeder::class,
            QuestionnaireSeeder::class,
            SklSeeder::class,
        ]);
    }
}
