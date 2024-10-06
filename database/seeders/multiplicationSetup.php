<?php

namespace Database\Seeders;

use App\Models\MultiplicationCalculation;
use App\Models\OvertimeMultiplicationSetup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class multiplicationSetup extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $multiplication = [
            [
                "id" => 1,
                "code" => "1.5X",
                "description" => "Multiply by 1.5",
                "multiplier" => 1.5
            ],
            [
                "id" => 2,
                "code" => "2X",
                "description" => "Multiply by 2",
                "multiplier" => 2
            ],
            [
                "id" => 3,
                "code" => "3X",
                "description" => "Multiply by 3",
                "multiplier" => 3
            ]
        ];

        $multiplicationSetup = [
            [
                "id" => 1,
                "day_type" => "Normal",
                "day" => "Monday",
                "from_hours" => 0.00,
                "to_hours" => 1.00,
                "multiplication_calc_id" => 1
            ],
            [
                "id" => 2,
                "day_type" => "Normal",
                "day" => "Monday",
                "from_hours" => 1.00,
                "to_hours" => 2.00,
                "multiplication_calc_id" => 2
            ],
            [
                "id" => 3,
                "day_type" => "Normal",
                "day" => "Monday",
                "from_hours" => 2.00,
                "to_hours" => 100.00,
                "multiplication_calc_id" => 3
            ],
            [
                "id" => 4,
                "day_type" => "Normal",
                "day" => "Tuesday",
                "from_hours" => 0.00,
                "to_hours" => 1.00,
                "multiplication_calc_id" => 1
            ],
            [
                "id" => 5,
                "day_type" => "Normal",
                "day" => "Tuesday",
                "from_hours" => 1.00,
                "to_hours" => 2.00,
                "multiplication_calc_id" => 2
            ],
            [
                "id" => 6,
                "day_type" => "Normal",
                "day" => "Tuesday",
                "from_hours" => 2.00,
                "to_hours" => 100.00,
                "multiplication_calc_id" => 3
            ],
            [
                "id" => 7,
                "day_type" => "Normal",
                "day" => "Wednesday",
                "from_hours" => 0.00,
                "to_hours" => 1.00,
                "multiplication_calc_id" => 1
            ],
            [
                "id" => 8,
                "day_type" => "Normal",
                "day" => "Wednesday",
                "from_hours" => 1.00,
                "to_hours" => 2.00,
                "multiplication_calc_id" => 2
            ],
            [
                "id" => 9,
                "day_type" => "Normal",
                "day" => "Wednesday",
                "from_hours" => 2.00,
                "to_hours" => 100.00,
                "multiplication_calc_id" => 3
            ],
            [
                "id" => 10,
                "day_type" => "Normal",
                "day" => "Thursday",
                "from_hours" => 0.00,
                "to_hours" => 1.00,
                "multiplication_calc_id" => 1
            ],
            [
                "id" => 11,
                "day_type" => "Normal",
                "day" => "Thursday",
                "from_hours" => 1.00,
                "to_hours" => 2.00,
                "multiplication_calc_id" => 2
            ],
        ];

        MultiplicationCalculation::insert($multiplication);
        OvertimeMultiplicationSetup::insert($multiplicationSetup);
    }
}
