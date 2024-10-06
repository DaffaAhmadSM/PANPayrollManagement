<?php

namespace Database\Seeders;

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
        User::factory(500)->create();
        $this->call(UserMenuSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(SetupSeeder::class);
        $this->call(multiplicationSetup::class);
    }
}
