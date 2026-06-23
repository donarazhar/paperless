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
            ['email' => 'admin@example.com'],
            ['name' => 'Admin Sekretariat', 'password' => Hash::make('123456'), 'role' => 'admin_sekretariat', 'organ_id' => $organAgenda->id]
        );

        User::firstOrCreate(
            ['email' => 'subagsurat@example.com'],
            ['name' => 'Subag Persuratan', 'password' => Hash::make('123456'), 'role' => 'subag_persuratan', 'organ_id' => $organSubagTu->id]
        );

        User::firstOrCreate(
            ['email' => 'kabagiantu@example.com'],
            ['name' => 'Bagian TU Sekretariat', 'password' => Hash::make('123456'), 'role' => 'bagian_tu', 'organ_id' => $organBagianTu->id]
        );

        User::firstOrCreate(
            ['email' => 'kasekretariat@example.com'],
            ['name' => 'Kepala Sekretariat', 'password' => Hash::make('123456'), 'role' => 'kepala_sekretariat', 'organ_id' => $organKepalaSek->id]
        );

        User::firstOrCreate(
            ['email' => 'adminsd@example.com'],
            ['name' => 'Admin TU SD 1', 'password' => Hash::make('123456'), 'role' => 'admin_unit', 'organ_id' => $organAdminSD->id]
        );

        User::firstOrCreate(
            ['email' => 'kasd@example.com'],
            ['name' => 'Kepala Sekolah SD 1', 'password' => Hash::make('123456'), 'role' => 'kepala_unit', 'organ_id' => $organKepalaSD->id]
        );
        
        User::firstOrCreate(
            ['email' => 'wakasd@example.com'],
            ['name' => 'Wakil Kepala Sekolah SD 1', 'password' => Hash::make('123456'), 'role' => 'sub_unit', 'organ_id' => $organSubSD->id]
        );

        // --- MASJID AGUNG AL AZHAR (Unit ID: 4) ---
        $organAdminMasjid = \App\Models\Organ::firstOrCreate(['name' => 'Admin Masjid', 'unit_id' => 4]);
        $organKepalaMasjid = \App\Models\Organ::firstOrCreate(['name' => 'Kepala Takmir Masjid', 'unit_id' => 4]);
        $organSubMasjid = \App\Models\Organ::firstOrCreate(['name' => 'Wakil Kepala Masjid', 'unit_id' => 4]);

        User::firstOrCreate(
            ['email' => 'adminmasjid@example.com'],
            ['name' => 'Admin Masjid Agung', 'password' => Hash::make('123456'), 'role' => 'admin_unit', 'organ_id' => $organAdminMasjid->id]
        );
        User::firstOrCreate(
            ['email' => 'kepalamasjid@example.com'],
            ['name' => 'Kepala Masjid Agung', 'password' => Hash::make('123456'), 'role' => 'kepala_unit', 'organ_id' => $organKepalaMasjid->id]
        );
        User::firstOrCreate(
            ['email' => 'submasjid@example.com'],
            ['name' => 'Sub Unit Masjid Agung', 'password' => Hash::make('123456'), 'role' => 'sub_unit', 'organ_id' => $organSubMasjid->id]
        );

        // --- BAGIAN ITTD (Unit ID: 5) ---
        $organAdminITTD = \App\Models\Organ::firstOrCreate(['name' => 'Admin ITTD', 'unit_id' => 5]);
        $organKepalaITTD = \App\Models\Organ::firstOrCreate(['name' => 'Kepala ITTD', 'unit_id' => 5]);
        $organSubITTD = \App\Models\Organ::firstOrCreate(['name' => 'Wakil Kepala ITTD', 'unit_id' => 5]);

        User::firstOrCreate(
            ['email' => 'adminittd@example.com'],
            ['name' => 'Admin Bagian ITTD', 'password' => Hash::make('123456'), 'role' => 'admin_unit', 'organ_id' => $organAdminITTD->id]
        );
        User::firstOrCreate(
            ['email' => 'kepalaittd@example.com'],
            ['name' => 'Kepala Bagian ITTD', 'password' => Hash::make('123456'), 'role' => 'kepala_unit', 'organ_id' => $organKepalaITTD->id]
        );
        User::firstOrCreate(
            ['email' => 'subittd@example.com'],
            ['name' => 'Sub Unit ITTD', 'password' => Hash::make('123456'), 'role' => 'sub_unit', 'organ_id' => $organSubITTD->id]
        );

        // --- DIREKTORAT DAKWAH SOSIAL (Unit ID: 6) ---
        $organAdminDakwah = \App\Models\Organ::firstOrCreate(['name' => 'Admin Dakwah', 'unit_id' => 6]);
        $organKepalaDakwah = \App\Models\Organ::firstOrCreate(['name' => 'Kepala Direktorat Dakwah', 'unit_id' => 6]);
        $organSubDakwah = \App\Models\Organ::firstOrCreate(['name' => 'Wakil Kepala Dakwah', 'unit_id' => 6]);

        User::firstOrCreate(
            ['email' => 'admindakwah@example.com'],
            ['name' => 'Admin Dakwah Sosial', 'password' => Hash::make('123456'), 'role' => 'admin_unit', 'organ_id' => $organAdminDakwah->id]
        );
        User::firstOrCreate(
            ['email' => 'kepaladakwah@example.com'],
            ['name' => 'Kepala Dakwah Sosial', 'password' => Hash::make('123456'), 'role' => 'kepala_unit', 'organ_id' => $organKepalaDakwah->id]
        );
        User::firstOrCreate(
            ['email' => 'subdakwah@example.com'],
            ['name' => 'Sub Unit Dakwah Sosial', 'password' => Hash::make('123456'), 'role' => 'sub_unit', 'organ_id' => $organSubDakwah->id]
        );
    }
}
