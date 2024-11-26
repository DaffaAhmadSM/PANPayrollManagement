<?php

namespace Database\Seeders;

use App\Models\ClassificationOfTaxPayer;
use App\Models\GeneralSetup;
use App\Models\User;
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
        User::factory()->create([
            'name' => 'dev',
            'email' => 'dev@admin.com',
            'password' => Hash::make("devsecret")
        ]);
        $this->call(UserMenuSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(SetupSeeder::class);
        $this->call(multiplicationSetup::class);
        $this->call(EducationLevelSeeder::class);
        $this->call(PositionRateSeeder::class);

        GeneralSetup::create([
            'customer' => 'N/A',
            'customer_contract' => 'N/A',
            'customer_timesheet' => 'N/A',
            'customer_invoice' => 'N/A',
            'employee' => 'PNS',
            'leave_request' => 'N/A',
            'leave_adjustment' => 'N/A',
            'timesheet' => 'N/A',
            'invent_journal_id' => 'N/A',
        ]);

        ClassificationOfTaxPayer::create([
            'description' => 'N/A',
            'code' => 'N/A',
        ]);
    }
}
