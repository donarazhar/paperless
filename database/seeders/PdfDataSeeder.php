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
        $pusat = Branch::firstOrCreate(['name' => 'Kampus Pusat']);
        $pass = Hash::make('123456');

        // Helper to quickly create unit, organ, and user
        $createUser = function($unit, $organName, $userName, $email, $role) use ($pass) {
            $organ = Organ::firstOrCreate(['name' => $organName, 'unit_id' => $unit->id]);
            User::updateOrCreate(
                ['email' => $email],
                ['name' => $userName, 'password' => $pass, 'role' => $role, 'organ_id' => $organ->id]
            );
        };

        // 1. Sekretariat
        $sekretariat = Unit::firstOrCreate(['name' => 'Sekretariat YPI Al Azhar', 'is_sekretariat' => true, 'branch_id' => $pusat->id, 'code' => 'SEK']);
        $createUser($sekretariat, 'Admin Sekretariat', 'Admin Sekretariat', 'admin_sek@alazhar.com', 'admin_sekretariat');
        $createUser($sekretariat, 'Kepala Sekretariat', 'Drs. H. Yayat Suyatna, M.M.', 'yayat@alazhar.com', 'kepala_sekretariat');
        $createUser($sekretariat, 'Bagian Tata Usaha', 'Zainal Arifin, S.Pd.', 'zainal@alazhar.com', 'bagian_tu');
        $createUser($sekretariat, 'Subag Rumah Tangga', 'Bahruddin', 'bahruddin@alazhar.com', 'sub_unit');
        $createUser($sekretariat, 'Subag Persuratan', 'Ryan Ariska, S.H.', 'ryan@alazhar.com', 'subag_persuratan');
        $createUser($sekretariat, 'Subag Keamanan', 'Nasroni', 'nasroni@alazhar.com', 'sub_unit');

        // 2. Bagian Kepegawaian
        $kepegawaian = Unit::firstOrCreate(['name' => 'Bagian Kepegawaian', 'is_sekretariat' => false, 'branch_id' => $pusat->id, 'code' => 'KEP']);
        $createUser($kepegawaian, 'Admin Kepegawaian', 'Admin Kepegawaian', 'admin_kep@alazhar.com', 'admin_unit');
        $createUser($kepegawaian, 'Kepala Bagian Kepegawaian', 'Ngadiman, M.Pd', 'ngadiman@alazhar.com', 'kepala_unit');
        $createUser($kepegawaian, 'Subag Kesejahteraan Pegawai', 'H. Alasri S.Kom', 'alasri@alazhar.com', 'sub_unit');
        $createUser($kepegawaian, 'Subag Administrasi Kepegawaian', 'Winarto, S.Pd.', 'winarto@alazhar.com', 'sub_unit');
        $createUser($kepegawaian, 'Subag Pembinaan, Perencanaan & Pengembangan Karir Pegawai', 'Hasan Umar, S.Pd', 'hasan@alazhar.com', 'sub_unit');

        // 3. Bagian Humas
        $humas = Unit::firstOrCreate(['name' => 'Bagian Humas', 'is_sekretariat' => false, 'branch_id' => $pusat->id, 'code' => 'HUMAS']);
        $createUser($humas, 'Admin Humas', 'Admin Humas', 'admin_humas@alazhar.com', 'admin_unit');
        $createUser($humas, 'Kepala Bagian Humas', 'Subari, S.Pd', 'subari@alazhar.com', 'kepala_unit');
        $createUser($humas, 'Subag Komunikasi & Publikasi', 'Eman Suherman, S.Psi.', 'eman@alazhar.com', 'sub_unit');
        $createUser($humas, 'Subag Pemasaran', 'Teguh Budi Suswanto, S.E.', 'teguh@alazhar.com', 'sub_unit');

        // 4. Bagian Umum
        $umum = Unit::firstOrCreate(['name' => 'Bagian Umum', 'is_sekretariat' => false, 'branch_id' => $pusat->id, 'code' => 'UMUM']);
        $createUser($umum, 'Admin Umum', 'Admin Umum', 'admin_umum@alazhar.com', 'admin_unit');
        $createUser($umum, 'Kepala Bagian Umum', 'Syamsul Arifin', 'syamsul@alazhar.com', 'kepala_unit');
        $createUser($umum, 'Subag Pengadaan', 'Pandu Wijaya, S.E , M.H', 'pandu@alazhar.com', 'sub_unit');
        $createUser($umum, 'Subag Pemeliharaan', 'Nursyamsi Atorida, S. Sos.', 'nursyamsi@alazhar.com', 'sub_unit');
        $createUser($umum, 'Subag Inventaris & Aset', 'Yana Hendarsah, S.E.', 'yana@alazhar.com', 'sub_unit');
        $createUser($umum, 'Pengelola SarPras & Inventaris Kampus Kebayoran Baru', 'M. Arief Affandi', 'arief@alazhar.com', 'sub_unit');
        $createUser($umum, 'Pengelola SarPras & Inventaris Kampus Tangerang Selatan', 'Maulana Malik Amrullah', 'maulana@alazhar.com', 'sub_unit');
        $createUser($umum, 'Pengelola SarPras & Inventaris Kampus Pejaten', 'M. Maulana', 'mmaulana@alazhar.com', 'sub_unit');
        $createUser($umum, 'Pengelola SarPras & Inventaris Kampus Bintaro', 'Muhammad', 'muhammad@alazhar.com', 'sub_unit');
        $createUser($umum, 'Pengelola SarPras & Inventaris Kampus Pasar Minggu', 'Ahmad Taufiq Al Magribi', 'taufiq@alazhar.com', 'sub_unit');
        $createUser($umum, 'Pengelola SarPras & Inventaris Kampus Cibinong', 'Iwan Susanto', 'iwan@alazhar.com', 'sub_unit');
        $createUser($umum, 'Pengelola SarPras & Inventaris Kampus Sentra Primer', 'Drs. Asmu’i', 'asmui@alazhar.com', 'sub_unit');
        $createUser($umum, 'Pengelola SarPras & Inventaris Kampus Cikarang', 'H. Sugiyadi, S.Pd.', 'sugiyadi@alazhar.com', 'sub_unit');
        $createUser($umum, 'Pengelola SarPras & Inventaris Kampus Cisolok', 'Ahmad Saladin, M.Si', 'saladin@alazhar.com', 'sub_unit');
        $createUser($umum, 'Pengelola SarPras & Inventaris Kampus Bandung', 'Mohamad Hasan Nuri, S.Pd', 'hasannuri@alazhar.com', 'sub_unit');
        $createUser($umum, 'Pengelola SarPras & Inventaris Kampus Cianjur', 'Kinkin Zaenal Muttaqien M. S.Kom', 'kinkin@alazhar.com', 'sub_unit');
        $createUser($umum, 'Pengelola SarPras & Inventaris Kampus Cigombong', 'Haryanto', 'haryanto@alazhar.com', 'sub_unit');
        $createUser($umum, 'Pengelola SarPras & Inventaris Kampus Palembang', 'M. Lutfi Fadli', 'lutfi@alazhar.com', 'sub_unit');
        $createUser($umum, 'Pengelola SarPras & Inventaris UAI', 'Amir Faisal', 'amir@alazhar.com', 'sub_unit');
        $createUser($umum, 'Pengelola SarPras & Inventaris Kampus Pusdiklat Al Azhar Anyer', 'Ahmad Taufik Rahman, S.E', 'taufikrahman@alazhar.com', 'sub_unit');

        // 5. Direktorat ITTD
        $ittd = Unit::firstOrCreate(['name' => 'Direktorat ITTD', 'is_sekretariat' => false, 'branch_id' => $pusat->id, 'code' => 'ITTD']);
        $createUser($ittd, 'Admin ITTD', 'Admin ITTD', 'admin_ittd@alazhar.com', 'admin_unit');
        $createUser($ittd, 'Kepala Dirat ITTD', 'Damarahmad Setiobudi, M.M', 'damarahmad@alazhar.com', 'kepala_unit');
        $createUser($ittd, 'Subag Teknologi Informasi', 'Mohammad Noeseir, M.M.', 'mohammad@alazhar.com', 'sub_unit');
        $createUser($ittd, 'Subag Transformasi Digital', 'Doni Sutrisno', 'doni@alazhar.com', 'sub_unit');

        // 6. Pusdiklat Anyer
        $anyer = Unit::firstOrCreate(['name' => 'Pusdiklat Anyer', 'is_sekretariat' => false, 'branch_id' => $pusat->id, 'code' => 'ANYER']);
        $createUser($anyer, 'Admin Pusdiklat Anyer', 'Admin Pusdiklat Anyer', 'admin_anyer@alazhar.com', 'admin_unit');
        $createUser($anyer, 'Kepala Pusdiklat Anyer', 'Subur Kurniawan, S.Si', 'subur@alazhar.com', 'kepala_unit');
    }
}
