<?php

namespace Database\Seeders;

use App\Models\EducationLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EducationLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EducationLevel::insert([
            [
                "level" => "SMK",
                "description" => "Smk"
            ],
            [
                "level" => "SMA",
                "description" => "Sma"
            ],
            [
                "level" => "SMP",
                "description" => "smp"
            ],
            [
                "level" => "Sarjana (S1)",
                "description" => "S1"
            ],
        ]);
    }
}
