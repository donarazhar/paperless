<?php
namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function __construct()
    {
        // Hanya admin yang boleh CRUD unit
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $units = Unit::all();
        return view('units.index', compact('units'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:units,name']);
        Unit::create(['name' => $request->name]);
        return back()->with('success', 'Unit berhasil ditambahkan.');
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate(['name' => 'required|string|unique:units,name,' . $unit->id]);
        $unit->update(['name' => $request->name]);
        return back()->with('success', 'Unit berhasil diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return back()->with('success', 'Unit berhasil dihapus.');
    }
}
