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
        if (in_array($user->role, ['admin_sekretariat', 'subag_persuratan', 'bagian_tu'])) {
            return back()->with('error', 'User dengan role ini tidak dapat dihapus, hanya dapat diedit.');
        }

        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }
}
