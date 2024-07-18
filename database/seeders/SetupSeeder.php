<?php

namespace Database\Seeders;

use App\Models\WorkingHour;
use App\Models\NumberSequence;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $working_hour = [
            [
                "code" => "6DAYS",
                "description" => "six working days in one week"
            ],
            [
                "code" => "5DAYS",
                "description" => "five working days in one week"
            ],
        ];

        $number_sequence = [
            [
                "code" => "CUST",
                "description" => "Customer"
            ],
            [
                "code" => "EMPL",
                "description" => "Employee"
            ],
        ];

        WorkingHour::insert($working_hour);
        NumberSequence::insert($number_sequence);
    }
}
