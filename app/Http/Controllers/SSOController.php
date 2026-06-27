<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Unit;
use App\Models\Organ;

class SSOController extends Controller
{
    /**
     * Redirect to the PresensiGPS SSO login page
     */
    public function redirect(Request $request)
    {
        $request->session()->put('state', $state = Str::random(40));

        $query = http_build_query([
            'client_id' => env('SSO_CLIENT_ID'),
            'redirect_uri' => env('SSO_REDIRECT_URI'),
            'response_type' => 'code',
            'scope' => '',
            'state' => $state,
        ]);

        return redirect(env('SSO_URL') . '/oauth/authorize?' . $query);
    }

    /**
     * Handle the callback from PresensiGPS SSO
     */
    public function callback(Request $request)
    {
        $state = $request->session()->pull('state');

        if (strlen($state) > 0 && $state !== $request->state) {
            abort(403, 'Invalid state parameter.');
        }

        // Get access token
        $response = Http::asForm()->post(env('SSO_URL') . '/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => env('SSO_CLIENT_ID'),
            'client_secret' => env('SSO_CLIENT_SECRET'),
            'redirect_uri' => env('SSO_REDIRECT_URI'),
            'code' => $request->code,
        ]);

        if ($response->failed()) {
            Log::error('SSO Token Error: ' . $response->body());
            return redirect('/login')->withErrors(['error' => 'Gagal mendapatkan token dari SSO PresensiGPS.']);
        }

        $tokenData = $response->json();
        $accessToken = $tokenData['access_token'];

        // Get user info
        $userResponse = Http::withToken($accessToken)->get(env('SSO_URL') . '/api/user');

        if ($userResponse->failed()) {
            Log::error('SSO User Info Error: ' . $userResponse->body());
            return redirect('/login')->withErrors(['error' => 'Gagal mengambil data profil dari SSO PresensiGPS.']);
        }

        $ssoUser = $userResponse->json();

        // Ensure Organ and Unit exist locally to maintain relationships (sync logic)
        $organId = null;
        if (isset($ssoUser['organ'])) {
            $organData = $ssoUser['organ'];
            $unitData = $organData['unit'] ?? null;

            if ($unitData) {
                Unit::updateOrCreate(
                    ['id' => $unitData['id']],
                    [
                        'name' => $unitData['name'],
                        'code' => $unitData['code'],
                        'is_sekretariat' => $unitData['is_sekretariat'] ?? false,
                        'branch_id' => $unitData['branch_id']
                    ]
                );
            }

            Organ::updateOrCreate(
                ['id' => $organData['id']],
                [
                    'name' => $organData['name'],
                    'unit_id' => $organData['unit_id']
                ]
            );
            $organId = $organData['id'];
        }

        // Generate a fallback email since Karyawan API might not have an email
        $userEmail = $ssoUser['email'] ?? ($ssoUser['nik_karyawan'] . '@alazhar.com');

        // Find or create local user based on NIK or Email
        $user = User::updateOrCreate(
            ['email' => $userEmail],
            [
                'name' => $ssoUser['name'],
                'password' => bcrypt(Str::random(16)), // Random password, authentication relies on SSO
                'organ_id' => $organId,
                'role' => 'staf_unit', // Default role. You may need logic to determine real roles.
            ]
        );

        // Optional: Store the access token in session if needed for future API calls
        $request->session()->put('sso_access_token', $accessToken);

        Auth::login($user);

        return redirect('/inbox')->with('success', 'Berhasil login via SSO Al Azhar Presensi System.');
    }
}
