<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting = [
            [
                "name" => "sizePage",
                "value" => "70",
                "description" => "set pagination limit value"
            ]
        ];

        AppSetting::insert($setting);
    }
}
