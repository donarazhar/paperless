<?php

namespace App\Http\Controllers;

use App\Models\Organ;
use App\Models\Unit;
use Illuminate\Http\Request;

class OrganController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        $organs = Organ::with('unit')->paginate(15);
        $units = Unit::all();
        return view('organs.index', compact('organs', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'unit_id' => 'required|exists:units,id'
        ]);

        // Opsional: Validasi unique organ dalam satu unit
        $exists = Organ::where('name', $request->name)->where('unit_id', $request->unit_id)->exists();
        if ($exists) {
            return back()->with('error', 'Organ dengan nama ini sudah ada di unit tersebut.');
        }

        Organ::create([
            'name' => $request->name,
            'unit_id' => $request->unit_id,
        ]);
        return back()->with('success', 'Organ berhasil ditambahkan.');
    }

    public function update(Request $request, Organ $organ)
    {
        $request->validate([
            'name' => 'required|string',
            'unit_id' => 'required|exists:units,id'
        ]);

        $exists = Organ::where('name', $request->name)
                       ->where('unit_id', $request->unit_id)
                       ->where('id', '!=', $organ->id)
                       ->exists();
        if ($exists) {
            return back()->with('error', 'Organ dengan nama ini sudah ada di unit tersebut.');
        }

        $organ->update([
            'name' => $request->name,
            'unit_id' => $request->unit_id,
        ]);
        return back()->with('success', 'Organ berhasil diperbarui.');
    }

    public function destroy(Organ $organ)
    {
        if ($organ->users()->count() > 0) {
            return back()->with('error', 'Organ tidak dapat dihapus karena masih memiliki pengguna terkait.');
        }
        $organ->delete();
        return back()->with('success', 'Organ berhasil dihapus.');
    }
}
