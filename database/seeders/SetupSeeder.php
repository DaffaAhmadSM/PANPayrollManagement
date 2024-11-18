<?php

namespace Database\Seeders;

use App\Models\WorkingHour;
use App\Models\NumberSequence;
use App\Models\WorkingHoursDetail;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $wk=WorkingHour::create([
            "code" => "6DAYS",
            "description" => "six working days in one week"
         ]);
        $number_sequence = [
            [
                "code" => "N/A",
                "description" => "Imported Data"
            ],
            [
                "code" => "PNS",
                "description" => "PNS employee"
            ],
        ];

        $working_hour_detail = [
            [
                "working_hours_id" => $wk->id,
                "day" => "Monday",
                "hours" => 7,
            ],
            [
                "working_hours_id" => $wk->id,
                "day" => "Tuesday",
                "hours" => 7,
            ],
            [
                "working_hours_id" => $wk->id,
                "day" => "Wednesday",
                "hours" => 7,
            ],
            [
                "working_hours_id" => $wk->id,
                "day" => "Thursday",
                "hours" => 7,
            ],
            [
                "working_hours_id" => $wk->id,
                "day" => "Friday",
                "hours" => 5,
            ],
            [
                "working_hours_id" => $wk->id,
                "day" => "Saturday",
                "hours" => 7,
            ]
        ];
        NumberSequence::insert($number_sequence);
        WorkingHoursDetail::insert($working_hour_detail);
    }
}
