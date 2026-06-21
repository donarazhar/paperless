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

        $organAgenda = \App\Models\Organ::firstOrCreate(['name' => 'Staf TU (Agenda)', 'unit_id' => 1]);
        $organSubagTu = \App\Models\Organ::firstOrCreate(['name' => 'Subag Persuratan', 'unit_id' => 1]);
        $organBagianTu = \App\Models\Organ::firstOrCreate(['name' => 'Bagian TU', 'unit_id' => 1]);
        $organKepalaSek = \App\Models\Organ::firstOrCreate(['name' => 'Kepala Sekretariat', 'unit_id' => 1]);
        
        $organAdminKeu = \App\Models\Organ::firstOrCreate(['name' => 'Admin Unit Keuangan', 'unit_id' => 2]);
        $organKepalaKeu = \App\Models\Organ::firstOrCreate(['name' => 'Kepala Direktorat Keuangan', 'unit_id' => 2]);

        $organAdminSD = \App\Models\Organ::firstOrCreate(['name' => 'Admin Unit SD', 'unit_id' => 3]);
        $organKepalaSD = \App\Models\Organ::firstOrCreate(['name' => 'Kepala Sekolah SD', 'unit_id' => 3]);
        $organSubSD = \App\Models\Organ::firstOrCreate(['name' => 'Wakil Kepala Sekolah SD', 'unit_id' => 3]);

        User::firstOrCreate(
            ['email' => 'stafagenda@example.com'],
            ['name' => 'Admin Sekretariat', 'password' => Hash::make('123456'), 'role' => 'admin_sekretariat', 'organ_id' => $organAgenda->id]
        );

        User::firstOrCreate(
            ['email' => 'subagpersuratan@example.com'],
            ['name' => 'Subag Persuratan', 'password' => Hash::make('123456'), 'role' => 'subag_persuratan', 'organ_id' => $organSubagTu->id]
        );

        User::firstOrCreate(
            ['email' => 'bagiantu@example.com'],
            ['name' => 'Bagian TU Sekretariat', 'password' => Hash::make('123456'), 'role' => 'bagian_tu', 'organ_id' => $organBagianTu->id]
        );

        User::firstOrCreate(
            ['email' => 'kepalasekretariat@example.com'],
            ['name' => 'Kepala Sekretariat', 'password' => Hash::make('123456'), 'role' => 'kepala_sekretariat', 'organ_id' => $organKepalaSek->id]
        );

        User::firstOrCreate(
            ['email' => 'adminsd@example.com'],
            ['name' => 'Admin TU SD 1', 'password' => Hash::make('123456'), 'role' => 'admin_unit', 'organ_id' => $organAdminSD->id]
        );

        User::firstOrCreate(
            ['email' => 'kepalasd@example.com'],
            ['name' => 'Kepala Sekolah SD 1', 'password' => Hash::make('123456'), 'role' => 'kepala_unit', 'organ_id' => $organKepalaSD->id]
        );
        
        User::firstOrCreate(
            ['email' => 'wakepalasd@example.com'],
            ['name' => 'Wakil Kepala Sekolah SD 1', 'password' => Hash::make('123456'), 'role' => 'sub_unit', 'organ_id' => $organSubSD->id]
        );

    }
}
