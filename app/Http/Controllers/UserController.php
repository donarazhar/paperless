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
        // Hanya staf_tu yang boleh CRUD user
        $this->middleware(['auth', 'role:staf_tu']);
    }

    public function index(Request $request)
    {
        $query = User::with(['unit.branch']);

        if ($request->filled('branch_id')) {
            $query->whereHas('unit', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        $users = $query->get();
        $branches = \App\Models\Branch::all();
        $units = Unit::all();

        return view('users.index', compact('users', 'branches', 'units'));
    }

    public function create()
    {
        $units = Unit::whereNotIn('id', User::select('unit_id')->whereNotNull('unit_id')->distinct())->get();
        return view('users.create', compact('units'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:staf_unit,staf_tu,kasubag_tu,kepala_sekretariat',
            'unit_id' => 'required|exists:units,id',
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $units = Unit::whereNotIn('id', User::select('unit_id')->whereNotNull('unit_id')->where('unit_id', '!=', $user->unit_id)->distinct())->get();
        return view('users.edit', compact('user', 'units'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:staf_unit,staf_tu,kasubag_tu,kepala_sekretariat',
            'unit_id' => 'required|exists:units,id',
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
        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }
}
