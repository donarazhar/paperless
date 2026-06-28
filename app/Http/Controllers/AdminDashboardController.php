<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Letter;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $usersCount = User::count();
        $adminCount = User::where('role', 'admin')->count();
        
        $totalSurat = Letter::count();
        $suratInternal = Letter::where('type', 'internal')->count(); // Adjust if type values differ
        $suratEksternal = Letter::where('type', 'external')->count(); // Adjust if type values differ
        $suratDraft = Letter::where('status', 'draft')->count();
        
        $recentUsers = User::latest()->take(5)->get();
        $recentLetters = Letter::with('sender')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'usersCount', 'adminCount', 'totalSurat', 
            'suratInternal', 'suratEksternal', 'suratDraft',
            'recentUsers', 'recentLetters'
        ));
    }
}
