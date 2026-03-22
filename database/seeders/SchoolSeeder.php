<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    /**
     * Seed the school's singleton profile.
     */
    public function run(): void
    {
        School::query()->updateOrCreate(
            ['id' => 1],
            [
                'name' => 'SMKN 1 Krangkeng',
                'address' => 'Jl. Raya Singakerta Kel. Singakerta Kec. Krangkeng - Indramayu',
                'postal_code' => '45284',
                'website' => 'https://www.smkn1krangkeng.sch.id',
                'email' => 'admin@smkn1krangkeng.sch.id',
                'phone' => '(0234) 7136113',
                'province' => 'Jawa Barat',
                'kcd_wilayah' => 'IX',
                'province_logo' => null,
                'school_stamp' => null,
            ]
        );
    }
}
