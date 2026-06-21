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
        $today = now()->toDateString();
        $user = Auth::user();
        $role = $user->role;

        $inboundToday = 0;
        $outboundToday = 0;
        $unreadCount = 0;
        $withDisposition = 0;

        $unitId = $user->unit_id;

        if (in_array($role, ['admin_unit', 'kepala_unit', 'sub_unit'])) {
            $inboundToday = Letter::whereDate('created_at', $today)
                ->whereHas('dispositions', fn($d) => $d->where('to_user_id', $user->id)->orWhere('to_unit_id', $unitId))
                ->count();
            $outboundToday = Letter::whereDate('created_at', $today)
                ->whereHas('sender', fn($s) => $s->where('unit_id', $unitId))
                ->count();

            if ($role === 'kepala_unit') {
                $unreadCount = Letter::where('status', 'pending_approval')
                    ->whereHas('sender', fn($s) => $s->where('unit_id', $unitId))
                    ->count();
            } else {
                $unreadCount = Letter::where('status', 'in_consideration')
                    ->whereHas('dispositions', fn($d) => $d->where('to_user_id', $user->id)->orWhere('to_unit_id', $unitId))
                    ->count();
            }

            $withDisposition = Disposition::where('status', 'pending')
                ->where(function($q) use ($user, $unitId) {
                    $q->where('to_user_id', $user->id)->orWhere('to_unit_id', $unitId);
                })->count();
        } elseif ($role === 'admin_sekretariat') {
            $inboundToday = Letter::whereDate('created_at', $today)->count();
            $unreadCount = Letter::where('status', 'pending_agenda')->count();
        } elseif ($role === 'subag_persuratan') {
            $inboundToday = Letter::whereDate('created_at', $today)->count();
            $unreadCount = Letter::where('status', 'in_review_subag')->count();
        } elseif ($role === 'bagian_tu') {
            $inboundToday = Letter::whereDate('created_at', $today)->count();
            $unreadCount = Letter::where('status', 'in_review_bagian_tu')->count();
            $withDisposition = Disposition::where('status', 'pending')->where('to_user_id', $user->id)->count();
        } elseif ($role === 'kepala_sekretariat') {
            $inboundToday = Letter::whereDate('created_at', $today)->count();
            $unreadCount = 0; // Hanya monitoring
            $withDisposition = Disposition::where('status', 'pending')->where('to_user_id', $user->id)->count();
        } else {
            $inboundToday = Letter::whereDate('created_at', $today)->count();
            $outboundToday = Letter::whereDate('created_at', $today)->count();
        }

        $start = Carbon::today()->subDays(6);
        $labels = [];
        $dataInbound = [];
        $dataOutbound = [];
        $dataDispo = [];

        for ($i = 0; $i < 7; $i++) {
            $d = $start->copy()->addDays($i);
            $labels[] = $d->isoFormat('D MMM');
            $dataInbound[] = Letter::whereDate('created_at', $d)->count();
            $dataOutbound[] = Letter::whereDate('created_at', $d)->count();
            $dataDispo[] = Disposition::whereDate('created_at', $d)->count();
        }

        $letterNotes = collect();
        if ($role === 'kepala_unit') {
            $letterNotes = Letter::where('status', 'pending_approval')->whereHas('sender', fn($s) => $s->where('unit_id', $unitId))->latest()->take(5)->get();
        } elseif ($role === 'admin_sekretariat') {
            $letterNotes = Letter::where('status', 'pending_agenda')->latest()->take(5)->get();
        } elseif ($role === 'subag_persuratan') {
            $letterNotes = Letter::where('status', 'in_review_subag')->latest()->take(5)->get();
        } elseif ($role === 'bagian_tu') {
            $letterNotes = Letter::where('status', 'in_review_bagian_tu')->latest()->take(5)->get();
        }

        $letterNotes = $letterNotes->map(fn($l) => (object) [
            'type' => 'letter',
            'letter_id' => $l->id,
            'letter_number' => $l->letter_number,
            'text' => $l->subject,
            'icon' => 'bi-envelope-fill text-primary',
            'created_at' => $l->created_at,
        ]);

        $dispoNotes = Disposition::where('status', 'pending')
            ->when(in_array($role, ['admin_unit', 'kepala_unit', 'sub_unit']), fn($q) => $q->where('to_user_id', $user->id)->orWhere('to_unit_id', $unitId))
            ->when(in_array($role, ['bagian_tu', 'kepala_sekretariat']), fn($q) => $q->where('to_user_id', $user->id))
            ->latest()->take(5)->with('fromUser', 'letter')->get()
            ->map(fn($d) => (object) [
                'type' => 'disposition',
                'letter_id' => $d->letter->id,
                'letter_number' => $d->letter->letter_number,
                'text' => 'Disposisi: ' . Str::limit($d->note, 30, '...') . ' (oleh ' . ($d->fromUser->name ?? '-') . ')',
                'icon' => 'bi-arrow-repeat text-warning',
                'created_at' => $d->created_at,
            ]);

        $notifications = $letterNotes->concat($dispoNotes)->sortByDesc('created_at')->values()->take(5);

        return view('dashboard', compact(
            'inboundToday',
            'outboundToday',
            'unreadCount',
            'withDisposition',
            'notifications',
            'labels',
            'dataInbound',
            'dataOutbound',
            'dataDispo'
        ));
    }
}
