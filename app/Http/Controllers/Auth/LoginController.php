<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Tampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            $role = Auth::user()->role;
            if (in_array($role, ['bagian_tu', 'kepala_sekretariat', 'sub_unit'])) {
                return redirect()->intended(route('tugas.myDisposisi'));
            }
            if (in_array($role, ['subag_persuratan', 'kepala_unit'])) {
                return redirect()->intended(route('tugas.disposisi'));
            }
            
            return redirect()->intended(route('letters.inbound'));
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->onlyInput('email');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
