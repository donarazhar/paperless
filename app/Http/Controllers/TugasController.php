<?php
namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\Disposition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TugasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Halaman Task Log (Riwayat Pekerjaan)
     * Menampilkan daftar pekerjaan yang telah diselesaikan oleh user (ACC, Disposisi, dsb)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;

        if (!in_array($role, ['subag_persuratan', 'kepala_unit', 'bagian_tu', 'kepala_sekretariat', 'sub_unit'])) {
            abort(403);
        }

        // Ambil surat-surat di mana user ini pernah melakukan aksi:
        // approved (ACC), disposed (Disposisi), forwarded (Teruskan), completed (Arsipkan Selesai)
        $q = Letter::with(['sender', 'recipientUser', 'recipientUnit'])
            ->whereHas('histories', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->whereIn('action', ['approved', 'disposed', 'forwarded', 'completed', 'agendakan', 'disposition_responded', 'forwarded_disposition']);
            });

        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(function ($query) use ($s) {
                $query->where('letter_number', 'like', "%{$s}%")
                      ->orWhere('agenda_number', 'like', "%{$s}%")
                      ->orWhere('subject', 'like', "%{$s}%");
            });
        }

        // Urutkan berdasarkan update terakhir agar yang baru dikerjakan ada di atas
        $letters = $q->orderBy('updated_at', 'desc')->paginate(15)->withQueryString();

        return view('tugas.index', compact('letters'));
    }

    /**
     * Halaman Tugas > Disposisi untuk subag_persuratan dan kepala_unit
     */
    public function disposisi(Request $request)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['subag_persuratan', 'kepala_unit'])) {
            abort(403);
        }

        $q = Letter::with(['sender', 'recipientUser', 'recipientUnit', 'dispositions']);

        if ($user->role === 'subag_persuratan') {
            // Surat yang sudah diagendakan, siap untuk direview/didisposisi subag_persuratan
            $q->whereNotNull('agenda_number')
              ->where('status', 'in_review_subag');
        } else {
            // kepala_unit: Surat yang didisposisikan kepadanya atau unitnya yang belum direspon
            $q->whereHas('dispositions', function ($sq) use ($user) {
                $sq->where(function($q2) use ($user) {
                    $q2->where('to_user_id', $user->id)
                       ->orWhere('to_unit_id', $user->unit_id);
                })->where('status', 'pending');
            });
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(function ($query) use ($s) {
                $query->where('letter_number', 'like', "%{$s}%")
                      ->orWhere('agenda_number', 'like', "%{$s}%")
                      ->orWhere('subject', 'like', "%{$s}%");
            });
        }

        $letters = $q->latest()->paginate(15)->withQueryString();

        return view('tugas.disposisi', compact('letters'));
    }

    /**
     * Halaman Tugas > ACC Surat untuk subag_persuratan dan kepala_unit
     */
    public function accSurat(Request $request)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['subag_persuratan', 'kepala_unit'])) {
            abort(403);
        }

        $q = Letter::with(['sender', 'recipientUser', 'recipientUnit'])
                   ->whereIn('type', ['internal', 'outbound_external'])
                   ->where('status', 'pending_approval');

        if ($user->role === 'subag_persuratan') {
            // Surat keluar dari unit Sekretariat yang dibuat oleh admin_sekretariat
            $q->whereHas('sender', function($sq) {
                $sq->where('role', 'admin_sekretariat');
            });
        } else {
            // Surat keluar dari unitnya sendiri
            $q->whereHas('sender.organ', function($sq) use ($user) {
                $sq->where('unit_id', $user->unit_id);
            });
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(function ($query) use ($s) {
                $query->where('letter_number', 'like', "%{$s}%")
                      ->orWhere('agenda_number', 'like', "%{$s}%")
                      ->orWhere('subject', 'like', "%{$s}%");
            });
        }

        $letters = $q->latest()->paginate(15)->withQueryString();

        return view('tugas.acc_surat', compact('letters'));
    }

    /**
     * Halaman Disposisi untuk role kepala_sekretariat, sub_unit, bagian_tu
     */
    public function myDisposisi(Request $request)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['kepala_sekretariat', 'sub_unit', 'bagian_tu'])) {
            abort(403);
        }

        $q = Letter::with(['sender', 'recipientUser', 'recipientUnit', 'dispositions']);

        if ($user->role === 'bagian_tu') {
            // bagian_tu bisa menerima forward dari subag_persuratan (in_review_bagian_tu) 
            // ATAU disposisi yang secara spesifik ditujukan kepadanya
            $q->where(function($query) use ($user) {
                $query->where('status', 'in_review_bagian_tu')
                      ->orWhereHas('dispositions', function($sq) use ($user) {
                          $sq->where(function($q2) use ($user) {
                              $q2->where('to_user_id', $user->id)
                                 ->orWhere('to_unit_id', $user->unit_id);
                          })->where('status', 'pending');
                      });
            });
        } else {
            // kepala_sekretariat, sub_unit
            $q->whereHas('dispositions', function ($sq) use ($user) {
                $sq->where(function($q2) use ($user) {
                    $q2->where('to_user_id', $user->id)
                       ->orWhere('to_unit_id', $user->unit_id);
                })->where('status', 'pending');
            });
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(function ($query) use ($s) {
                $query->where('letter_number', 'like', "%{$s}%")
                      ->orWhere('agenda_number', 'like', "%{$s}%")
                      ->orWhere('subject', 'like', "%{$s}%");
            });
        }

        $letters = $q->latest()->paginate(15)->withQueryString();

        return view('tugas.my_disposisi', compact('letters'));
    }
}
