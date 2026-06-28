<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Letter;

class AdminDashboardController extends Controller
{
    public function monitoring(Request $request)
    {
        $query = Letter::with('sender', 'unit');
        
        if ($request->filled('search')) {
            $query->where('subject', 'like', '%' . $request->search . '%')
                  ->orWhere('letter_number', 'like', '%' . $request->search . '%');
        }
        
        $letters = $query->latest()->paginate(20);
        return view('admin.monitoring', compact('letters'));
    }

    public function logs()
    {
        // For now, if no activity log table exists, we pass empty collection.
        // If there's an Activity model, we can use it. But let's assume we just have a placeholder or we can query authentication logs if available.
        $logs = collect([]);
        return view('admin.logs', compact('logs'));
    }
}
