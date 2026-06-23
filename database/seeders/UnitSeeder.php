<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Models\Branch;

class UnitSeeder extends Seeder
{
    public function run()
    {
        $pusat = Branch::firstOrCreate(['name' => 'Kampus Pusat']);
        $bandung = Branch::firstOrCreate(['name' => 'Kampus Bandung']);
        $cikarang = Branch::firstOrCreate(['name' => 'Kampus Cikarang']);

        Unit::firstOrCreate(['name' => 'Sekretariat YPI Al Azhar', 'is_sekretariat' => true, 'branch_id' => $pusat->id]);
        Unit::firstOrCreate(['name' => 'Direktorat Keuangan', 'branch_id' => $pusat->id]);
        Unit::firstOrCreate(['name' => 'Unit SD Islam Al Azhar 1', 'branch_id' => $bandung->id]);
        
        Unit::firstOrCreate(['name' => 'Masjid Agung Al Azhar', 'branch_id' => $pusat->id]);
        Unit::firstOrCreate(['name' => 'Bagian ITTD', 'branch_id' => $pusat->id]);
        Unit::firstOrCreate(['name' => 'Direktorat Dakwah Sosial', 'branch_id' => $pusat->id]);
    }
}
