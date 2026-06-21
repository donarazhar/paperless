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
        $user = Auth::user();
        
        $q = Letter::with(['sender', 'dispositions.toUser', 'dispositions.unit', 'recipientUser', 'recipientUnit'])
                   ->where('type', 'internal');

        // Jika admin_unit / kepala_unit, tampilkan surat keluar dari unit mereka
        if (in_array($user->role, ['admin_unit', 'kepala_unit', 'sub_unit'])) {
            $q->whereHas('sender.organ', function($sq) use ($user) {
                $sq->where('unit_id', $user->unit_id);
            });
        }

        if ($s = $request->search) {
            $q->where(function ($query) use ($s) {
                $query->where('letter_number', 'like', "%{$s}%")
                      ->orWhere('agenda_number', 'like', "%{$s}%")
                      ->orWhere('subject', 'like', "%{$s}%")
                      ->orWhereHas('sender.unit', function ($sq) use ($s) {
                          $sq->where('name', 'like', "%{$s}%");
                      });
            });
        }

        if ($st = $request->status) {
            $q->where('status', $st);
        }

        if ($branchId = $request->branch_id) {
            $q->whereHas('sender.unit', function($sq) use ($branchId) {
                $sq->where('branch_id', $branchId);
            });
        }

        if ($unitId = $request->unit_id) {
            $q->whereHas('sender.organ', function($sq) use ($unitId) {
                $sq->where('unit_id', $unitId);
            });
        }

        $letters = $q->latest()->get();
        $branches = \App\Models\Branch::orderBy('name')->get();
        $units = \App\Models\Unit::orderBy('name')->get();

        return view('letters.index', compact('letters', 'branches', 'units'));
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

        // Filter out drafts from inbox
        $q->where('status', '!=', 'draft');

        // Unified Visibility Logic: User sees letters addressed to their unit OR dispositioned to them/their unit
        $q->where(function ($query) use ($user) {
            if (in_array($user->role, ['admin_sekretariat', 'subag_persuratan', 'bagian_tu', 'kepala_sekretariat'])) {
                // Sekretariat sees letters that have reached them (pending_agenda or further)
                $query->whereNotIn('status', ['draft', 'pending_approval', 'pending_sending']);
            } else {
                // Admin Unit / Kepala Unit sees direct letters to their unit or dispositions
                $query->where('to_unit_id', $user->unit_id)
                      ->orWhereHas('dispositions', function ($d) use ($user) {
                          $d->where('to_user_id', $user->id)
                            ->orWhere('to_unit_id', $user->unit_id);
                      });
            }
        });

        //— pencarian & filter seperti sebelumnya —
        if ($s = $request->search) {
            $q->where(function ($query) use ($s) {
                $query->where('letter_number', 'like', "%{$s}%")
                      ->orWhere('agenda_number', 'like', "%{$s}%")
                      ->orWhere('subject', 'like', "%{$s}%")
                      ->orWhereHas('sender.unit', function ($sq) use ($s) {
                          $sq->where('name', 'like', "%{$s}%");
                      });
            });
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

        $letters = $q->latest()->paginate(15)->withQueryString();

        return view('letters.inbox', compact('letters'));
    }

    public function arsip(Request $request)
    {
        $user = Auth::user();
        $q = Letter::with(['sender', 'dispositions.toUser', 'dispositions.unit', 'recipientUser', 'recipientUnit'])
                   ->whereIn('type', ['internal', 'external']);

        $q->where('status', 'completed');

        // Apply visibility rules for non-Staf TU users
        if ($user->role === 'staf_unit') {
            // Untuk unit: Hanya arsip dari surat keluar internal mereka sendiri
            $q->where('from_user_id', $user->id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(function ($query) use ($s) {
                $query->where('letter_number', 'like', "%{$s}%")
                      ->orWhere('agenda_number', 'like', "%{$s}%")
                      ->orWhere('subject', 'like', "%{$s}%")
                      ->orWhereHas('sender.unit', function ($sq) use ($s) {
                          $sq->where('name', 'like', "%{$s}%");
                      });
            });
        }

        if ($from = $request->date_from) {
            $q->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->date_to) {
            $q->whereDate('created_at', '<=', $to);
        }

        $letters = $q->latest()->paginate(15)->withQueryString();

        return view('letters.arsip', compact('letters'));
    }

    public function inboundExternal(Request $request)
    {
        $user = Auth::user();
        $q = Letter::with(['sender', 'dispositions', 'recipientUser', 'recipientUnit'])
                   ->where('type', 'external');

        $q->where(function ($query) use ($user) {
            $query->where('created_by_user_id', $user->id); // Everyone sees what they created

            if (in_array($user->role, ['kasubag_tu', 'kepala_sekretariat'])) {
                // Kasubag/Kepala ONLY see it via dispositions or specific post-agenda statuses
                $query->orWhereHas('dispositions', function ($sq) use ($user) {
                          $sq->where('to_user_id', $user->id)
                             ->orWhere('to_unit_id', $user->unit_id);
                      })
                      ->orWhereIn('status', ['in_review_kasubag', 'in_consideration', 'completed']);
            } else {
                // Staf Unit / Staf TU see direct letters to their unit
                $query->orWhere('to_unit_id', $user->unit_id)
                      ->orWhereHas('dispositions', function ($sq) use ($user) {
                          $sq->where('to_user_id', $user->id)
                             ->orWhere('to_unit_id', $user->unit_id);
                      });

                // Catch-all for external letters forwarded to Secretariat without explicit to_unit_id
                if ($user->role === 'staf_tu') {
                    $query->orWhereIn('status', ['pending_agenda', 'in_consideration', 'completed']);
                }
            }
        });

        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(function ($query) use ($s) {
                $query->where('letter_number', 'like', "%{$s}%")
                      ->orWhere('agenda_number', 'like', "%{$s}%")
                      ->orWhere('subject', 'like', "%{$s}%")
                      ->orWhereHas('sender.unit', function ($sq) use ($s) {
                          $sq->where('name', 'like', "%{$s}%");
                      });
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

        $letters = $q->latest()->paginate(15)->withQueryString();
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
            ->whereHas('sender.organ', function($sq) use ($user) {
                $sq->where('unit_id', $user->unit_id);
            })
            ->where('type', 'internal');

        // -- FILTERS --
        if ($s = $request->search) {
            $q->where(function ($query) use ($s) {
                $query->where('letter_number', 'like', "%{$s}%")
                      ->orWhere('agenda_number', 'like', "%{$s}%")
                      ->orWhere('subject', 'like', "%{$s}%")
                      ->orWhereHas('sender.unit', function ($sq) use ($s) {
                          $sq->where('name', 'like', "%{$s}%");
                      });
            });
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
        $userUnitId = Auth::user()->unit_id;
        
        $units = \App\Models\Unit::when($userUnitId, function($query) use ($userUnitId) {
            return $query->where('id', '!=', $userUnitId);
        })->get();

        return view('letters.create', compact('units'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'letter_number' => 'nullable|string',
            'subject' => 'required|string',
            'body' => 'required|string',
            'action' => 'required|in:draft,send',
            'to_unit_id' => 'required|exists:units,id',
            'attachments' => 'required|array|min:1',
            'attachments.*' => 'file|max:5120',
        ]);

        $targetUnit = \App\Models\Unit::find($data['to_unit_id']);
        $isSekretariat = $targetUnit && $targetUnit->is_sekretariat;
        
        $status = 'draft';
        if ($request->action === 'send') {
            $status = 'pending_approval';
        }

        $letter = Letter::create([
            'type' => 'internal',
            'letter_number' => $data['letter_number'] ?? '-',
            'subject' => $data['subject'],
            'body' => $data['body'],
            'from_user_id' => Auth::id(),
            'to_unit_id' => $data['to_unit_id'],
            'status' => $status,
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

    public function createOutboundExternal()
    {
        return view('letters.create_outbound_external');
    }

    public function storeOutboundExternal(Request $request)
    {
        $data = $request->validate([
            'external_recipient_name' => 'required|string|max:255',
            'letter_number' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'nullable|string',
            'external_notes' => 'nullable|string',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $letter = Letter::create([
            'type' => 'outbound_external',
            'letter_number' => $data['letter_number'] ?? '-',
            'subject' => $data['subject'],
            'body' => $data['body'] ?? '-',
            'external_recipient_name' => $data['external_recipient_name'],
            'external_notes' => $data['external_notes'],
            'from_user_id' => Auth::id(),
            'status' => 'pending_approval',
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

        return redirect()->route('letters.outboundExternal')->with('success', 'Surat keluar eksternal berhasil dicatat.');
    }

    public function outboundExternal(Request $request)
    {
        $user = Auth::user();
        $q = Letter::whereHas('sender.organ', function($sq) use ($user) {
                $sq->where('unit_id', $user->unit_id);
            })
            ->where('type', 'outbound_external');

        if ($request->has('search') && $request->search != '') {
            $s = $request->search;
            $q->where(function ($query) use ($s) {
                $query->where('letter_number', 'like', "%{$s}%")
                      ->orWhere('agenda_number', 'like', "%{$s}%")
                      ->orWhere('subject', 'like', "%{$s}%")
                      ->orWhereHas('sender.unit', function ($sq) use ($s) {
                          $sq->where('name', 'like', "%{$s}%");
                      });
            });
        }

        $letters = $q->latest()->paginate(15)->withQueryString();

        return view('letters.outbox_external', compact('letters'));
    }

    public function updateExternalNotes(Request $request, Letter $letter)
    {
        $request->validate([
            'external_notes' => 'required|string'
        ]);

        $letter->update([
            'external_notes' => $request->external_notes
        ]);

        return back()->with('success', 'Keterangan hasil surat keluar eksternal berhasil diperbarui.');
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

    public function printDisposition($hashedId)
    {
        $id = Hashids::decode($hashedId)[0] ?? abort(404);
        $letter = Letter::with(['dispositions.toUser', 'dispositions.unit', 'dispositions.fromUser'])->findOrFail($id);
        $this->authorize('view', $letter);
        return view('letters.print_disposition', compact('letter'));
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

    public function approve(Letter $letter)
    {
        $this->authorize('view', $letter);
        if ($letter->status !== 'pending_approval' || Auth::user()->role !== 'kepala_unit') {
            abort(403);
        }
        $letter->update(['status' => 'pending_sending']);
        LetterHistory::create(['letter_id' => $letter->id, 'user_id' => Auth::id(), 'action' => 'approved', 'note' => 'Disetujui Kepala Unit']);
        return back()->with('success', 'Surat berhasil di-ACC. Menunggu Admin Unit mengirim fisik surat.');
    }

    public function sendFinal(Letter $letter)
    {
        $this->authorize('view', $letter);
        if ($letter->status !== 'pending_sending' || Auth::user()->role !== 'admin_unit') {
            abort(403);
        }
        
        $newStatus = ($letter->type === 'outbound_external') ? 'completed' : 'pending_agenda';
        $letter->update(['status' => $newStatus]);
        
        $note = ($letter->type === 'outbound_external') ? 'Surat eksternal telah dikirim.' : 'Surat dikirim ke Sekretariat untuk diagendakan.';
        LetterHistory::create(['letter_id' => $letter->id, 'user_id' => Auth::id(), 'action' => 'sent', 'note' => $note]);
        
        return back()->with('success', $note);
    }
}
