<?php
namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\Disposition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $role = Auth::user()->role;
        
        if (in_array($role, ['admin_sekretariat', 'admin_unit', 'admin'])) {
            return redirect()->route('letters.inbound');
        } elseif (in_array($role, ['subag_persuratan', 'kepala_unit'])) {
            return redirect()->route('tugas.accSurat');
        } elseif (in_array($role, ['kepala_sekretariat', 'sub_unit', 'bagian_tu'])) {
            return redirect()->route('tugas.myDisposisi');
        } else {
            return redirect()->route('letters.inbound');
        }
    }
}
