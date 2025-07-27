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

        // 1) Surat Masuk Hari Ini
        if ($user->role === 'staff') {
            $inboundToday = Letter::whereDate('created_at', $today)
                ->where('type', 'inbound')
                ->where(function ($q) use ($user) {
                    $q->where('to_user_id', $user->id)
                        ->orWhereHas(
                            'dispositions',
                            fn($d) =>
                            $d->where('to_user_id', $user->id)
                        );
                })
                ->count();
        } elseif ($user->role === 'manager') {
            $inboundToday = Letter::whereDate('created_at', $today)
                ->where('type', 'inbound')
                ->where(function ($q) use ($user) {
                    $q->where('to_unit_id', $user->unit_id)
                        ->orWhere('to_user_id', $user->id)
                        ->orWhereHas(
                            'dispositions',
                            fn($d) =>
                            $d->where('to_unit_id', $user->unit_id)
                                ->orWhere('to_user_id', $user->id)
                        );
                })
                ->count();
        } else {
            $inboundToday = Letter::whereDate('created_at', $today)
                ->where('type', 'inbound')
                ->count();
        }

        // 2) Surat Keluar Hari Ini
        if ($user->role === 'staff') {
            $outboundToday = Letter::whereDate('created_at', $today)
                ->where('type', 'outbound')
                ->where('from_user_id', $user->id)
                ->count();
        } elseif ($user->role === 'manager') {
            $outboundToday = Letter::whereDate('created_at', $today)
                ->where('type', 'outbound')
                ->whereHas(
                    'sender',
                    fn($s) =>
                    $s->where('unit_id', $user->unit_id)
                )
                ->count();
        } else {
            $outboundToday = Letter::whereDate('created_at', $today)
                ->where('type', 'outbound')
                ->count();
        }

        // 3) Surat Belum Dibaca
        $unreadCount = Letter::where('status', 'sent')
            ->when(
                $user->role === 'staff',
                fn($q) =>
                $q->where('to_user_id', $user->id)
            )
            ->when(
                $user->role === 'manager',
                fn($q) =>
                $q->where('to_unit_id', $user->unit_id)
            )
            ->count();

        // 4) Disposisi Belum Ditanggapi = count hanya yang status = 'pending'
        $withDisposition = Disposition::where('status', 'pending')
            ->when($role === 'staff', function ($q) use ($user) {
                return $q->where('to_user_id', $user->id);
            })
            ->when($role === 'manager', function ($q) use ($user) {
                return $q->where(function ($q2) use ($user) {
                    $q2->where('to_unit_id', $user->unit_id)
                        ->orWhere('to_user_id', $user->id);
                });
            })
            // admin melihat semua pending dispositions
            ->count();

        // ===== DATA UNTUK GRAFIK 7 HARI TERAKHIR =====
        $start = Carbon::today()->subDays(6);
        $labels = [];
        $dataInbound = [];
        $dataOutbound = [];
        $dataDispo = [];

        for ($i = 0; $i < 7; $i++) {
            $d = $start->copy()->addDays($i);
            $labels[] = $d->isoFormat('D MMM');
            // Surat masuk hari itu (tanpa filter role, asumsi grafik global)
            $dataInbound[] = Letter::where('type', 'inbound')
                ->whereDate('created_at', $d)->count();
            // Surat keluar hari itu
            $dataOutbound[] = Letter::where('type', 'outbound')
                ->whereDate('created_at', $d)->count();
            // Disposisi dibuat hari itu
            $dataDispo[] = Disposition::whereDate('created_at', $d)->count();
        }
        // ============================================

        //
        // 5) Notifikasi Surat Baru (5 terbaru)
        //

        // 1) Ambil notif Surat (status = sent)
        $letterNotes = Letter::where('status', 'sent')
            ->when($user->role === 'staff', fn($q) => $q->where('to_user_id', $user->id))
            ->when($user->role === 'manager', fn($q) => $q->where('to_unit_id', $user->unit_id))
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($l) => (object) [
                'type' => 'letter',
                'letter_id' => $l->id,
                'letter_number' => $l->letter_number,
                'text' => $l->subject,
                'icon' => 'bi-envelope-fill text-primary',
                'created_at' => $l->created_at,
            ]);

        // 2) Ambil notif Disposisi (status = pending)
        $dispoNotes = Disposition::where('status', 'pending')
            ->when($user->role === 'staff', fn($q) => $q->where('to_user_id', $user->id))
            ->when($user->role === 'manager', fn($q) => $q->where(
                fn($q2) =>
                $q2->where('to_unit_id', $user->unit_id)
                    ->orWhere('to_user_id', $user->id)
            ))
            ->latest()
            ->take(5)
            ->with('fromUser', 'letter')
            ->get()
            ->map(fn($d) => (object) [
                'type' => 'disposition',
                'letter_id' => $d->letter->id,
                'letter_number' => $d->letter->letter_number,
                'text' => 'Disposisi: ' . Str::limit($d->note, 30, '...')
                    . ' (oleh ' . $d->fromUser->name . ')',
                'icon' => 'bi-arrow-repeat text-warning',
                'created_at' => $d->created_at,
            ]);

        // 3) Gabung, urutkan, ambil 5 teratas
        $notifications = $letterNotes
            ->concat($dispoNotes)
            ->sortByDesc('created_at')
            ->values()
            ->take(5);

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
