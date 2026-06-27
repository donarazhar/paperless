<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PresensiAuthController extends Controller
{
    /**
     * Redirect pengguna ke halaman SSO PresensiGPS.
     */
    public function redirect()
    {
        return Socialite::driver('presensi')->redirect();
    }

    /**
     * Menangani callback dari SSO PresensiGPS.
     */
    public function callback()
    {
        try {
            $presensiUser = Socialite::driver('presensi')->user();
            $rawUser = $presensiUser->user;
            
            // Sync Branch, Unit, Organ based on SSO Data
            if (isset($rawUser['cabang'])) {
                $cabangData = $rawUser['cabang'];
                \App\Models\Branch::updateOrCreate(
                    ['id' => $cabangData['id']],
                    ['name' => $cabangData['name']]
                );
            }

            $organId = null;
            if (isset($rawUser['organ'])) {
                $organData = $rawUser['organ'];
                $unitData = $organData['unit'] ?? null;
                
                if ($unitData) {
                    // Pastikan branch_id valid dengan mengecek apakah branch tersebut ada (dijaga oleh logika cabang di atas)
                    \App\Models\Unit::updateOrCreate(
                        ['id' => $unitData['id']],
                        [
                            'name' => $unitData['name'],
                            'code' => $unitData['code'] ?? 'UNIT',
                            'is_sekretariat' => $unitData['is_sekretariat'] ?? false,
                            'branch_id' => $unitData['branch_id']
                        ]
                    );
                }

                \App\Models\Organ::updateOrCreate(
                    ['id' => $organData['id']],
                    [
                        'name' => $organData['name'],
                        'unit_id' => $organData['unit_id']
                    ]
                );
                $organId = $organData['id'];
            }
            
            // Cari user berdasarkan email atau NIK
            // Karena ini SSO Master, kita asumsikan email selalu sinkron.
            $userEmail = $presensiUser->email ?? ($presensiUser->nickname . '@alazhar.com');
            
            $user = User::updateOrCreate(
                ['email' => $userEmail],
                [
                    'name' => $presensiUser->name,
                    'password' => bcrypt(\Illuminate\Support\Str::random(16)), // Autentikasi via SSO
                    'organ_id' => $organId,
                    // Note: Untuk role, saat ini kita tidak menimpanya jika user sudah ada, agar admin lokal tetap admin.
                    // Namun jika baru, beri role default staf_unit.
                ]
            );

            // Jika user baru dan role belum diset, beri default 'staf_unit' (meski di database default-nya staf_unit, ini memastikan).
            if ($user->wasRecentlyCreated && !$user->role) {
                $user->update(['role' => 'staf_unit']);
            }

            Auth::login($user);

            // Redirect sesuai logic role di aplikasi persuratan
            $role = $user->role;
            if (in_array($role, ['bagian_tu', 'kepala_sekretariat', 'sub_unit'])) {
                return redirect()->intended(route('tugas.myDisposisi'));
            }
            if (in_array($role, ['subag_persuratan', 'kepala_unit'])) {
                return redirect()->intended(route('tugas.disposisi'));
            }
            
            return redirect()->intended(route('letters.inbound'));

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal login melalui SSO: ' . $e->getMessage());
        }
    }
}
