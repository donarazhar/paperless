<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:staf_tu']);
    }

    public function index()
    {
        $branches = Branch::withCount('units')->get();
        return view('branches.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:branches,name']);
        Branch::create(['name' => $request->name]);
        return back()->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate(['name' => 'required|string|unique:branches,name,' . $branch->id]);
        $branch->update(['name' => $request->name]);
        return back()->with('success', 'Cabang berhasil diperbarui.');
    }

    public function destroy(Branch $branch)
    {
        if ($branch->units()->count() > 0) {
            return back()->with('error', 'Cabang tidak dapat dihapus karena masih memiliki unit.');
        }
        $branch->delete();
        return back()->with('success', 'Cabang berhasil dihapus.');
    }
}
