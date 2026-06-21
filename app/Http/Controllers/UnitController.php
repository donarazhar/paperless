<?php
namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        $units = Unit::with('branch')->paginate(15);
        $branches = \App\Models\Branch::all();
        return view('units.index', compact('units', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:units,name',
            'branch_id' => 'required|exists:branches,id',
            'is_sekretariat' => 'boolean'
        ]);
        Unit::create([
            'name' => $request->name,
            'branch_id' => $request->branch_id,
            'is_sekretariat' => $request->boolean('is_sekretariat')
        ]);
        return back()->with('success', 'Unit berhasil ditambahkan.');
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|string|unique:units,name,' . $unit->id,
            'branch_id' => 'required|exists:branches,id',
            'is_sekretariat' => 'boolean'
        ]);
        $unit->update([
            'name' => $request->name,
            'branch_id' => $request->branch_id,
            'is_sekretariat' => $request->boolean('is_sekretariat')
        ]);
        return back()->with('success', 'Unit berhasil diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return back()->with('success', 'Unit berhasil dihapus.');
    }
}
