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

    public function index(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $q = Letter::with(['sender', 'dispositions', 'recipientUser', 'recipientUnit'])
                   ->where('type', 'internal');

        if ($s = $request->search) {
            $q->where(
                fn($q) =>
                $q->where('letter_number', 'like', "%{$s}%")
                    ->orWhere('agenda_number', 'like', "%{$s}%")
                    ->orWhere('subject', 'like', "%{$s}%")
            );
        }
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

    public function inbound(Request $request)
    {
        $user = Auth::user();

        $q = Letter::with([
            'sender',
            'recipientUser',
            'recipientUnit',
            'dispositions',
        ])->where('type', 'internal');

        if ($user->role === 'staf_unit') {
            $q->whereHas(
                'dispositions',
                fn($d) =>
                $d->where('to_user_id', $user->id)
                  ->orWhere('to_unit_id', $user->unit_id)
            );
        } elseif ($user->role === 'staf_tu') {
            // Staf TU can see letters coming to the sekretariat that are pending_agenda
            $q->where('to_unit_id', $user->unit_id)
              ->whereIn('status', ['pending_agenda', 'in_consideration', 'completed']);
        } elseif (in_array($user->role, ['kasubag_tu', 'kepala_sekretariat'])) {
            $q->where(function ($q) use ($user) {
                $q->where('to_unit_id', $user->unit_id)
                    ->orWhereHas(
                        'dispositions',
                        fn($d) =>
                        $d->where('to_user_id', $user->id)
                    );
            });
        }

        //— pencarian & filter seperti sebelumnya —
        if ($s = $request->search) {
            $q->where(
                fn($q) =>
                $q->where('letter_number', 'like', "%{$s}%")
                    ->orWhere('agenda_number', 'like', "%{$s}%")
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

        $letters = $q->latest()->paginate(15);

        return view('letters.inbox', compact('letters'));
    }

    public function inboundExternal(Request $request)
    {
        $user = Auth::user();
        $q = Letter::with(['sender', 'dispositions', 'recipientUser', 'recipientUnit'])
                   ->where('type', 'external');

        if ($user->role === 'staf_unit') {
            $q->where(function ($query) use ($user) {
                $query->where('created_by_user_id', $user->id)
                      ->orWhereHas('dispositions', function ($sq) use ($user) {
                          $sq->where('to_user_id', $user->id)
                             ->orWhere('to_unit_id', $user->unit_id);
                      })
                      ->orWhere('to_unit_id', $user->unit_id);
            });
        } elseif ($user->role === 'staf_tu') {
            $q->where(function ($query) use ($user) {
                  $query->whereIn('status', ['pending_agenda', 'in_consideration', 'completed'])
                        ->orWhere('to_unit_id', $user->unit_id)
                        ->orWhere('created_by_user_id', $user->id);
              });
        } elseif ($user->role === 'kasubag_tu' || $user->role === 'kepala_sekretariat') {
            $q->where(function ($query) use ($user) {
                  $query->whereIn('status', ['in_review_kasubag', 'in_consideration', 'completed'])
                        ->orWhere('to_unit_id', $user->unit_id)
                        ->orWhere('created_by_user_id', $user->id)
                        ->orWhereHas('dispositions', function ($sq) use ($user) {
                            $sq->where('to_user_id', $user->id);
                        });
              });
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(function ($query) use ($s) {
                $query->where('letter_number', 'like', "%$s%")
                      ->orWhere('subject', 'like', "%$s%")
                      ->orWhere('external_sender_name', 'like', "%$s%");
            });
        }

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $q->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $q->whereDate('created_at', '<=', $request->date_to);
        }

        $letters = $q->latest()->paginate(15);
        return view('letters.inbox_external', compact('letters'));
    }

    public function createExternal()
    {
        return view('letters.create_external');
    }

    public function storeExternal(Request $request)
    {
        $data = $request->validate([
            'external_sender_name' => 'required|string|max:255',
            'letter_number'        => 'required|string|max:255',
            'subject'              => 'required|string|max:255',
            'body'                 => 'required|string',
            'attachments.*'        => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'action_type'          => 'required|in:archive,forward',
        ]);

        $letter = Letter::create([
            'type'                 => 'external',
            'external_sender_name' => $data['external_sender_name'],
            'created_by_user_id'   => Auth::id(),
            'from_user_id'         => null,
            'to_unit_id'           => ($data['action_type'] === 'archive') ? Auth::user()->unit_id : null,
            'letter_number'        => $data['letter_number'],
            'subject'              => $data['subject'],
            'body'                 => $data['body'],
            'status'               => ($data['action_type'] === 'archive') ? 'completed' : 'pending_agenda',
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                $letter->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        $msg = ($data['action_type'] === 'archive') 
            ? 'Surat Eksternal berhasil diarsipkan.' 
            : 'Surat Eksternal berhasil diteruskan ke Sekretariat YPIA untuk Disposisi.';

        return redirect()->route('letters.inboundExternal')->with('success', $msg);
    }

    public function outbound(Request $request)
    {
        $user = Auth::user();
        $q = Letter::with(['recipientUser', 'recipientUnit'])
            ->where('from_user_id', $user->id)
            ->where('type', 'internal');

        // -- FILTERS --
        if ($s = $request->search) {
            $q->where(
                fn($q) =>
                $q->where('letter_number', 'like', "%{$s}%")
                    ->orWhere('agenda_number', 'like', "%{$s}%")
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

    public function create()
    {
        $this->authorizeRole('staf_unit');
        $units = \App\Models\Unit::all();
        return view('letters.create', compact('units'));
    }

    public function store(Request $request)
    {
        $this->authorizeRole('staf_unit');

        $data = $request->validate([
            'letter_number' => 'nullable|string',
            'subject' => 'required|string',
            'body' => 'required|string',
            'action' => 'required|in:draft,send',
            'to_unit_id' => 'required|exists:units,id',
            'attachments' => 'required|array|min:1',
            'attachments.*' => 'file|max:5120',
        ]);

        $letter = Letter::create([
            'type' => 'internal',
            'letter_number' => $data['letter_number'] ?? '-',
            'subject' => $data['subject'],
            'body' => $data['body'],
            'from_user_id' => Auth::id(),
            'to_unit_id' => $data['to_unit_id'],
            'status' => $request->action === 'draft' ? 'draft' : 'sent',
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $originalName = $file->getClientOriginalName();
                $basename = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                $filename = $basename . '_' . time() . '.' . $extension;

                $path = $file->storeAs('attachments', $filename, 'public');

                \App\Models\Attachment::create([
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

    public function markRead(Letter $letter)
    {
        $user = Auth::user();

        LetterHistory::create([
            'letter_id' => $letter->id,
            'user_id' => $user->id,
            'action' => 'read',
            'note' => null,
        ]);

        return back();
    }
}
