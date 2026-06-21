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
        $organs = \App\Models\Organ::with('unit')->get();
        return view('users.create', compact('organs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin_sekretariat,subag_persuratan,bagian_tu,kepala_sekretariat,admin_unit,kepala_unit,sub_unit',
            'organ_id' => 'required|exists:organs,id',
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $organs = \App\Models\Organ::with('unit')->get();
        return view('users.edit', compact('user', 'organs'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin_sekretariat,subag_persuratan,bagian_tu,kepala_sekretariat,admin_unit,kepala_unit,sub_unit',
            'organ_id' => 'required|exists:organs,id',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if (in_array($user->role, ['admin_sekretariat', 'subag_persuratan', 'bagian_tu'])) {
            return back()->with('error', 'User dengan role ini tidak dapat dihapus, hanya dapat diedit.');
        }

        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }
}
