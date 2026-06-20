<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class MassiveDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Prepare a common password hash to speed up seeding
        $password = Hash::make('123456');

        for ($b = 1; $b <= 50; $b++) {
            // Create a branch
            $branch = Branch::create([
                'name' => 'Cabang ' . $faker->city . ' ' . $b
            ]);

            // Create 5 units for each branch
            for ($u = 1; $u <= 5; $u++) {
                $unit = Unit::create([
                    'name' => 'Unit ' . $faker->companySuffix . ' ' . $u . ' - ' . $branch->name,
                    'is_sekretariat' => false,
                    'branch_id' => $branch->id,
                ]);

                // Create 1 staf_unit user for each unit
                User::create([
                    'name' => 'Staf ' . $unit->name,
                    'email' => 'staf_' . strtolower(str_replace(' ', '', $faker->unique()->lexify('??????'))) . $b . '_' . $u . '@example.com',
                    'password' => $password,
                    'role' => 'staf_unit',
                    'unit_id' => $unit->id,
                ]);
            }
        }
    }
}
