<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Models\Branch;

class UnitSeeder extends Seeder
{
    public function run()
    {
        $pusat = Branch::firstOrCreate(['name' => 'Cabang Pusat (YPI Al Azhar)']);
        $daerah = Branch::firstOrCreate(['name' => 'Cabang Daerah A']);

        Unit::firstOrCreate(['name' => 'Sekretariat YPI Al Azhar', 'is_sekretariat' => true, 'branch_id' => $pusat->id]);
        Unit::firstOrCreate(['name' => 'Direktorat Keuangan', 'branch_id' => $pusat->id]);
        Unit::firstOrCreate(['name' => 'Unit SD Islam Al Azhar 1', 'branch_id' => $daerah->id]);
    }
}
