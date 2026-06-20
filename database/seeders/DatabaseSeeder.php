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
        $this->call(UnitSeeder::class);

        User::firstOrCreate(
            ['email' => 'staftu@example.com'],
            ['name' => 'Staf TU Sekretariat', 'password' => Hash::make('123456'), 'role' => 'staf_tu', 'unit_id' => 1]
        );

        User::firstOrCreate(
            ['email' => 'kasubagtu@example.com'],
            ['name' => 'Kasubag TU', 'password' => Hash::make('123456'), 'role' => 'kasubag_tu', 'unit_id' => 1]
        );

        User::firstOrCreate(
            ['email' => 'kepalasekretariat@example.com'],
            ['name' => 'Kepala Sekretariat', 'password' => Hash::make('123456'), 'role' => 'kepala_sekretariat', 'unit_id' => 1]
        );

        User::firstOrCreate(
            ['email' => 'stafunit@example.com'],
            ['name' => 'Staf Unit Keuangan', 'password' => Hash::make('123456'), 'role' => 'staf_unit', 'unit_id' => 2]
        );

        User::firstOrCreate(
            ['email' => 'stafsd@example.com'],
            ['name' => 'Staf Unit SD 1', 'password' => Hash::make('123456'), 'role' => 'staf_unit', 'unit_id' => 3]
        );

        $this->call(MassiveDataSeeder::class);
        $this->call(LetterSeeder::class);
        $this->call(OutboundLetterSeeder::class);
    }
}
