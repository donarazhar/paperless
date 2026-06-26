<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user exists by email
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if ($user) {
                // Update google_id if empty
                if (empty($user->google_id)) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
                
                Auth::login($user, true);
                
                $role = $user->role;
                if (in_array($role, ['bagian_tu', 'kepala_sekretariat', 'sub_unit'])) {
                    return redirect()->route('tugas.myDisposisi');
                }
                if (in_array($role, ['subag_persuratan', 'kepala_unit'])) {
                    return redirect()->route('tugas.disposisi');
                }
                
                return redirect()->route('letters.inbound');
            } else {
                // User does not exist, redirect back with error
                return redirect()->route('login')->with('error', 'Email Anda (' . $googleUser->getEmail() . ') belum terdaftar di sistem. Silakan hubungi Administrator.');
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Terjadi kesalahan saat login dengan Google: ' . $e->getMessage());
        }
    }
}
