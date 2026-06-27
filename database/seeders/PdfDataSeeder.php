<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Models\Branch;
use App\Models\Organ;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PdfDataSeeder extends Seeder
{
    public function run()
    {
        $pusat = Branch::firstOrCreate(['name' => 'YPI Al Azhar Pusat']);
        $pass = Hash::make('123456');

        // Helper to quickly create organ, and optionally user
        $createUser = function($unit, $organName, $userName, $email, $role) use ($pass) {
            $organ = Organ::firstOrCreate(['name' => $organName, 'unit_id' => $unit->id]);
            if ($email) {
                User::updateOrCreate(
                    ['email' => $email],
                    ['name' => $userName, 'password' => $pass, 'role' => $role, 'organ_id' => $organ->id]
                );
            }
        };

        // 1. Sekretariat
        $sekretariat = Unit::firstOrCreate(['name' => 'Sekretariat YPI Al Azhar', 'branch_id' => $pusat->id], ['is_sekretariat' => true, 'code' => 'SEK']);
        
        $createUser($sekretariat, 'Bagian Tata Usaha', 'Admin Sekretariat', 'admin_sek@alazhar.com', 'admin_sekretariat');
        $createUser($sekretariat, 'Bagian Tata Usaha', 'Zainal Arifin, S.Pd.', 'zainal@alazhar.com', 'bagian_tu');
        
        $createUser($sekretariat, 'Kepala Sekretariat', 'Drs. H. Yayat Suyatna, M.M.', 'yayat@alazhar.com', 'kepala_sekretariat');
        $createUser($sekretariat, 'Subag Rumah Tangga', 'Bahruddin', 'bahruddin@alazhar.com', 'sub_unit');
        $createUser($sekretariat, 'Subag Persuratan', 'Ryan Ariska, S.H.', 'ryan@alazhar.com', 'subag_persuratan');
        $createUser($sekretariat, 'Subag Keamanan', 'Nasroni', 'nasroni@alazhar.com', 'sub_unit');

        // 2. Bagian Kepegawaian
        $kepegawaian = Unit::firstOrCreate(['name' => 'Bagian Kepegawaian', 'branch_id' => $pusat->id], ['is_sekretariat' => false, 'code' => 'KEP']);
        
        $createUser($kepegawaian, 'Subag Administrasi Kepegawaian', 'Admin Kepegawaian', 'admin_kep@alazhar.com', 'admin_unit');
        $createUser($kepegawaian, 'Subag Administrasi Kepegawaian', 'Winarto, S.Pd.', 'winarto@alazhar.com', 'sub_unit');
        
        $createUser($kepegawaian, 'Kepala Bagian Kepegawaian', 'Ngadiman, M.Pd', 'ngadiman@alazhar.com', 'kepala_unit');
        $createUser($kepegawaian, 'Subag Kesejahteraan Pegawai', 'H. Alasri S.Kom', 'alasri@alazhar.com', 'sub_unit');
        $createUser($kepegawaian, 'Subag Pembinaan, Perencanaan & Pengembangan Karir Pegawai', 'Hasan Umar, S.Pd', 'hasan@alazhar.com', 'sub_unit');

        // 3. Bagian Humas
        $humas = Unit::firstOrCreate(['name' => 'Bagian Humas', 'branch_id' => $pusat->id], ['is_sekretariat' => false, 'code' => 'HUMAS']);
        
        $createUser($humas, 'Subag Komunikasi & Publikasi', 'Admin Humas', 'admin_humas@alazhar.com', 'admin_unit');
        $createUser($humas, 'Subag Komunikasi & Publikasi', 'Eman Suherman, S.Psi.', 'eman@alazhar.com', 'sub_unit');
        
        $createUser($humas, 'Kepala Bagian Humas', 'Subari, S.Pd', 'subari@alazhar.com', 'kepala_unit');
        $createUser($humas, 'Subag Pemasaran', 'Teguh Budi Suswanto, S.E.', 'teguh@alazhar.com', 'sub_unit');

        // 4. Bagian Umum
        $umum = Unit::firstOrCreate(['name' => 'Bagian Umum', 'branch_id' => $pusat->id], ['is_sekretariat' => false, 'code' => 'UMUM']);
        
        $createUser($umum, 'Subag Pengadaan', 'Admin Umum', 'admin_umum@alazhar.com', 'admin_unit');
        $createUser($umum, 'Subag Pengadaan', 'Pandu Wijaya, S.E , M.H', 'pandu@alazhar.com', 'sub_unit');
        
        $createUser($umum, 'Kepala Bagian Umum', 'Syamsul Arifin', 'syamsul@alazhar.com', 'kepala_unit');
        $createUser($umum, 'Subag Pemeliharaan', 'Nursyamsi Atorida, S. Sos.', 'nursyamsi@alazhar.com', 'sub_unit');
        $createUser($umum, 'Subag Inventaris & Aset', 'Yana Hendarsah, S.E.', 'yana@alazhar.com', 'sub_unit');

        // 5. Direktorat ITTD
        $ittd = Unit::firstOrCreate(['name' => 'Direktorat ITTD', 'branch_id' => $pusat->id], ['is_sekretariat' => false, 'code' => 'ITTD']);
        
        $createUser($ittd, 'Subag Teknologi Informasi', 'Admin ITTD', 'admin_ittd@alazhar.com', 'admin_unit');
        $createUser($ittd, 'Subag Teknologi Informasi', 'Mohammad Noeseir, M.M.', 'noeseir@alazhar.com', 'sub_unit');
        
        $createUser($ittd, 'Kepala Dirat ITTD', 'Damarahmad Setiobudi, M.M', 'damarahmad@alazhar.com', 'kepala_unit');
        $createUser($ittd, 'Subag Transformasi Digital', 'Doni Sutrisno', 'doni@alazhar.com', 'sub_unit');

        // 6. Pusdiklat Anyer
        $anyer = Unit::firstOrCreate(['name' => 'Pusdiklat Anyer', 'branch_id' => $pusat->id], ['is_sekretariat' => false, 'code' => 'ANYER']);
        
        $createUser($anyer, 'Staff Pusdiklat', 'Admin Pusdiklat Anyer', 'admin_anyer@alazhar.com', 'admin_unit');
        $createUser($anyer, 'Kepala Pusdiklat Anyer', 'Subur Kurniawan, S.Si', 'subur@alazhar.com', 'kepala_unit');

        // 7. Bagian Keagamaan
        $keagamaan = Unit::firstOrCreate(['name' => 'Bagian Keagamaan', 'branch_id' => $pusat->id], ['is_sekretariat' => false, 'code' => 'MAA']);
        $createUser($keagamaan, 'Kepala Bagian Keagamaan', null, null, null);
        $createUser($keagamaan, 'Imam Masjid', null, null, null);
        $createUser($keagamaan, 'Muazin', null, null, null);
        $createUser($keagamaan, 'Staff Masjid', null, null, null);
    }
}
