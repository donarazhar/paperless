<?php
namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\LetterHistory;
use App\Models\Unit;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

class LetterController extends Controller
{
    public function __construct()
    {
        // Autentikasi (route group meng-apply role secara spesifik)
        $this->middleware('auth');
    }

    /**
     * Daftar Semua Surat (Admin only)
     */
    public function index(Request $request)
    {
        // Pastikan hanya Admin
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $q = Letter::with(['sender', 'recipientUser', 'recipientUnit']);

        // optional: pencarian
        if ($s = $request->search) {
            $q->where(
                fn($q) =>
                $q->where('letter_number', 'like', "%{$s}%")
                    ->orWhere('subject', 'like', "%{$s}%")
            );
        }
        // optional: filter type inbound/outbound
        if ($t = $request->type) {
            $q->where('type', $t);
        }
        if ($st = $request->status) {
            $q->where('status', $st);
        }
        if ($from = $request->date_from) {
            $q->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->date_to) {
            $q->whereDate('created_at', '<=', $to);
        }

        $letters = $q->latest()
            ->paginate(15)
            ->withQueryString();

        return view('letters.index', compact('letters'));
    }

    /**
     * Surat Masuk (Inbox)
     */
    public function inbound(Request $request)
    {
        $user = Auth::user();

        // Base query: load relasi yang dibutuhkan
        $q = Letter::with([
            'sender',
            'recipientUser',
            'recipientUnit',
            'dispositions',
        ]);

        if ($user->role === 'staff') {
            // Staff: hanya tipe inbound, ke user atau via disposisi
            $q->where('type', 'inbound')
                ->where(function ($q) use ($user) {
                    $q->where('to_user_id', $user->id)
                        ->orWhereHas(
                            'dispositions',
                            fn($d) =>
                            $d->where('to_user_id', $user->id)
                        );
                });
        } elseif ($user->role === 'manager') {
            // Manager: **hapus** filter type, 
            // lihat semua surat (inbound eksternal & outbound internal)
            // yang ke unitnya, personal, atau via disposisi
            $q->where(function ($q) use ($user) {
                $q->where('to_unit_id', $user->unit_id)
                    ->orWhere('to_user_id', $user->id)
                    ->orWhereHas(
                        'dispositions',
                        fn($d) =>
                        $d->where('to_unit_id', $user->unit_id)
                            ->orWhere('to_user_id', $user->id)
                    );
            });
        }
        // else Admin: tidak dibatasi sama sekali

        //— pencarian & filter seperti sebelumnya —
        if ($s = $request->search) {
            $q->where(
                fn($q) =>
                $q->where('letter_number', 'like', "%{$s}%")
                    ->orWhere('subject', 'like', "%{$s}%")
            );
        }
        if ($st = $request->status) {
            $q->where('status', $st);
        }
        if ($from = $request->date_from) {
            $q->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->date_to) {
            $q->whereDate('created_at', '<=', $to);
        }

        $letters = $q->latest()
            ->paginate(15)
            ->withQueryString();

        return view('letters.inbox', compact('letters'));
    }

    /**
     * Surat Keluar (Outbox) — hanya milik staff
     */
    public function outbound(Request $request)
    {
        $q = Letter::with(['recipientUser', 'recipientUnit'])
            ->where('type', 'outbound')
            ->where('from_user_id', Auth::id());

        // -- FILTERS --
        if ($s = $request->search) {
            $q->where(
                fn($q) =>
                $q->where('letter_number', 'like', "%{$s}%")
                    ->orWhere('subject', 'like', "%{$s}%")
            );
        }
        if ($st = $request->status) {
            $q->where('status', $st);
        }
        if ($from = $request->date_from) {
            $q->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->date_to) {
            $q->whereDate('created_at', '<=', $to);
        }

        $letters = $q->latest()
            ->paginate(15)
            ->withQueryString();

        return view('letters.outbox', compact('letters'));
    }

    /**
     * Form Buat Surat (Staff only)
     */
    public function create()
    {
        $this->authorizeRole('staff');
        $units = Unit::all();
        return view('letters.create', compact('units'));
    }

    /**
     * Simpan Surat (draft atau kirim)
     */
    public function store(Request $request)
    {
        $this->authorizeRole('staff'); // pastikan hanya staff

        $data = $request->validate([
            'letter_number' => 'nullable|string|unique:letters,letter_number',
            'subject' => 'required|string',
            'body' => 'required|string',
            // harus pilih salah satu penerima
            'to_user_id' => 'nullable|exists:users,id|required_without:to_unit_id',
            'to_unit_id' => 'nullable|exists:units,id|required_without:to_user_id',
            'recipient_type' => 'required|in:unit,user',
            'action' => 'required|in:draft,send',
            // attachments wajib satu atau lebih
            'attachments' => 'required|array|min:1',

            // pakai mimetypes, bukan mimes
            'attachments.*' => 'file|max:5120',
        ]);

        // nomor otomatis bila kirim
        if ($request->action === 'send' && empty($data['letter_number'])) {
            $data['letter_number'] = 'L-' . now()->format('Ymd') . '-' . Str::upper(Str::random(4));
        }

        $letter = Letter::create([
            'type' => 'outbound',
            'letter_number' => $data['letter_number'],
            'subject' => $data['subject'],
            'body' => $data['body'],
            'from_user_id' => Auth::id(),
            'to_user_id' => $data['to_user_id'] ?? null,
            'to_unit_id' => $data['to_unit_id'] ?? null,
            'status' => $request->action === 'draft' ? 'draft' : 'sent',
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $originalName = $file->getClientOriginalName();
                $basename = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                $filename = $basename . '_' . time() . '.' . $extension;

                $path = $file->storeAs('attachments', $filename, 'public');

                Attachment::create([
                    'letter_id' => $letter->id,
                    'file_path' => $path,
                ]);
            }
        }

        // record history: draft or sent
        LetterHistory::create([
            'letter_id' => $letter->id,
            'user_id' => Auth::id(),
            'action' => $letter->status,
            'note' => null,
        ]);

        return redirect()
            ->route($request->action === 'send' ? 'letters.outbound' : 'letters.inbound')
            ->with('success', $request->action === 'send'
                ? 'Surat berhasil dikirim.'
                : 'Draft berhasil disimpan.');
    }

    /**
     * Detail Surat (bisa diakses semua role sesuai izin)
     */
    public function show($hashedId)
    {
        $id = Hashids::decode($hashedId)[0] ?? abort(404);
        $letter = Letter::findOrFail($id);
        $this->authorize('view', $letter);
        return view('letters.show', compact('letter'));
    }

    /**
     * Tandai Surat sebagai Dibaca atau Selesai
     */
    public function markRead(Letter $letter)
    {
        $user = Auth::user();

        // Hanya staff atau manager yang boleh menandai sebagai dibaca
        if (in_array($user->role, ['staff', 'manager']) && $letter->status === 'sent') {
            // Set status selalu ke 'read', terlepas dari role
            $letter->update(['status' => 'read']);

            // Catat history
            LetterHistory::create([
                'letter_id' => $letter->id,
                'user_id' => $user->id,
                'action' => 'read',
                'note' => null,
            ]);
        }

        return back();
    }
}
