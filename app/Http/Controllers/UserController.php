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
        // Hanya admin yang boleh CRUD user
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $users = User::with('unit')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $units = Unit::all();
        return view('users.create', compact('units'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,manager,staff',
            'unit_id' => 'required|exists:units,id',
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $units = Unit::all();
        return view('users.edit', compact('user', 'units'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,manager,staff',
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
