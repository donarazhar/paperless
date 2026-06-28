<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        // Middleware sudah diatur di routes/web.php
    }

    public function index(Request $request)
    {
        $query = User::with(['organ.unit.branch']);
        $currentUser = auth()->user();

        // Admin Sekretariat (dan lainnya) tidak boleh melihat Superadmin (role: admin)
        if ($currentUser->role !== 'admin') {
            $query->where('role', '!=', 'admin');
        }

        if ($request->filled('branch_id')) {
            $query->whereHas('organ.unit', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }
        if ($request->filled('unit_id')) {
            $query->whereHas('organ', function($q) use ($request) {
                $q->where('unit_id', $request->unit_id);
            });
        }

        $users = $query->paginate(15)->withQueryString();
        $branches = \App\Models\Branch::all();
        $units = Unit::all();

        return view('users.index', compact('users', 'branches', 'units'));
    }

    public function create()
    {
        // Get all emails that are already registered in the persuratan database
        $registeredEmails = \App\Models\User::pluck('email')->toArray();

        $karyawan = [];
        try {
            $url = env('PRESENSI_URL', 'https://presensigps.masjidagungalazhar.com') . '/api/karyawan-list';
            $response = \Illuminate\Support\Facades\Http::get($url);
            
            if ($response->successful()) {
                $allKaryawan = $response->json();
                foreach ($allKaryawan as $k) {
                    if (!in_array($k['email'], $registeredEmails)) {
                        $karyawan[] = (object) $k;
                    }
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to fetch karyawan from API in UserController@create: ' . $e->getMessage());
        }

        return view('users.create', compact('karyawan'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,admin_sekretariat,subag_persuratan,bagian_tu,kepala_sekretariat,admin_unit,kepala_unit,sub_unit,staf_unit',
            'name' => 'nullable|string'
        ]);

        User::create([
            'email' => $data['email'],
            'role' => $data['role'],
            'name' => $data['name'] ?? explode('@', $data['email'])[0],
            'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
            'organ_id' => null, // Will be synced via SSO
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil dipra-daftar. Hak akses (role) sudah ditetapkan.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => 'required|in:admin,admin_sekretariat,subag_persuratan,bagian_tu,kepala_sekretariat,admin_unit,kepala_unit,sub_unit',
        ]);

        $user->update($data);
        return redirect()->route('users.index')
            ->with('success', 'Role user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $currentUser = auth()->user();

        if ($currentUser->role === 'admin') {
            // Superadmin (admin) tidak boleh menghapus dirinya/admin lain
            if ($user->role === 'admin') {
                return back()->with('error', 'Role Superadmin tidak dapat dihapus.');
            }
        } else {
            // Role selain admin tidak boleh menghapus admin & petinggi sekretariat
            $protectedRoles = ['admin', 'admin_sekretariat', 'subag_persuratan', 'bagian_tu', 'kepala_sekretariat'];
            if (in_array($user->role, $protectedRoles)) {
                return back()->with('error', 'User dengan role ini tidak dapat dihapus, hanya dapat diedit.');
            }
        }

        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }
}
