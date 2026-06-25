<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $r)
    {
        $data = $r->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'photo' => 'nullable|image|max:2048'
        ]);
        
        if ($r->hasFile('photo')) {
            if (Auth::user()->photo && Storage::disk('public')->exists(Auth::user()->photo)) {
                Storage::disk('public')->delete(Auth::user()->photo);
            }
            $data['photo'] = $r->file('photo')->store('photos', 'public');
        }

        Auth::user()->update($data);
        return back()->with('success', 'Profil diperbarui.');
    }

    public function showPasswordForm()
    {
        return view('profile.password');
    }

    public function updatePassword(Request $r)
    {
        $r->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:6'
        ]);
        if (!Hash::check($r->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Password salah']);
        }
        Auth::user()->update(['password' => Hash::make($r->password)]);
        return back()->with('success', 'Password berhasil diubah.');
    }
}
