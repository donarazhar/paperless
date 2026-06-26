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
                   ->where(function($query) {
                       $query->has('dispositions')
                             ->orWhereNotNull('agenda_number');
                   });

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

        if ($from = $request->date_from) {
            $q->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->date_to) {
            $q->whereDate('created_at', '<=', $to);
        }

        $letters = $q->latest()->paginate(25)->withQueryString();
        
        $pageTitle = 'History Surat';
        $pageSubTitle = 'Menampilkan semua surat yang berproses disposisi atau memiliki nomor agenda';

        return view('letters.index', compact('letters', 'pageTitle', 'pageSubTitle'));
    }

    public function reportOutboundInternal(Request $request)
    {
        $user = Auth::user();
        
        $q = Letter::with(['sender', 'dispositions.toUser', 'dispositions.unit', 'recipientUser', 'recipientUnit'])
                   ->where('type', 'internal')
                   ->whereHas('sender.organ', function($sq) use ($user) {
                       $sq->where('unit_id', $user->unit_id);
                   });

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

        $letters = $q->latest()->paginate(25)->withQueryString();
        
        $pageTitle = 'Laporan Surat Keluar Internal';
        $pageSubTitle = 'Rekap seluruh surat keluar internal dari unit Anda';

        return view('letters.index', compact('letters', 'pageTitle', 'pageSubTitle'));
    }

    public function reportOutboundExternal(Request $request)
    {
        $user = Auth::user();
        
        $q = Letter::with(['sender', 'dispositions.toUser', 'dispositions.unit', 'recipientUser', 'recipientUnit'])
                   ->where('type', 'outbound_external')
                   ->whereHas('sender.organ', function($sq) use ($user) {
                       $sq->where('unit_id', $user->unit_id);
                   });

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

        $letters = $q->latest()->paginate(25)->withQueryString();
        
        $pageTitle = 'Laporan Surat Keluar Eksternal';
        $pageSubTitle = 'Rekap seluruh surat keluar eksternal dari unit Anda';

        return view('letters.index', compact('letters', 'pageTitle', 'pageSubTitle'));
    }

    public function inbound(Request $request)
    {
        $user = Auth::user();

        $q = Letter::with([
            'sender',
            'recipientUser',
            'recipientUnit',
            'dispositions',
        ])->whereIn('type', ['internal', 'external']);

        // Filter out letters that haven't been sent by the sender yet
        $q->whereNotIn('status', ['draft', 'pending_approval', 'pending_sending']);

        // Unified Visibility Logic: User sees letters addressed to their unit OR dispositioned to them/their unit
        $q->where(function ($query) use ($user) {
            // Internal Logic
            $query->where(function($qInt) use ($user) {
                $qInt->where('type', 'internal');
                
                // Pastikan surat yang dikirim oleh unit sendiri tidak muncul di Inbox (masuk ke Outbox)
                $qInt->whereHas('sender.organ', function($sq) use ($user) {
                    $sq->where('unit_id', '!=', $user->unit_id);
                });

                if (in_array($user->role, ['admin_sekretariat', 'subag_persuratan', 'bagian_tu', 'kepala_sekretariat'])) {
                    $qInt->whereNotNull('id');
                } else {
                    $qInt->where(function($qDest) use ($user) {
                        $qDest->where('to_unit_id', $user->unit_id)
                             ->orWhereHas('dispositions', function ($d) use ($user) {
                                 $d->where('to_user_id', $user->id)
                                   ->orWhere('to_unit_id', $user->unit_id);
                             });
                    });
                }
            });

            // External Logic
            $query->orWhere(function($qExt) use ($user) {
                $qExt->where('type', 'external');
                $qExt->where(function ($qE2) use ($user) {
                    $qE2->where('created_by_user_id', $user->id); // Everyone sees what they created
                    if (in_array($user->role, ['kasubag_tu', 'kepala_sekretariat'])) {
                        $qE2->orWhereHas('dispositions', function ($sq) use ($user) {
                                  $sq->where('to_user_id', $user->id)
                                     ->orWhere('to_unit_id', $user->unit_id);
                              })
                              ->orWhereIn('status', ['in_review_kasubag', 'in_consideration', 'completed']);
                    } else {
                        $qE2->orWhere('to_unit_id', $user->unit_id)
                              ->orWhereHas('dispositions', function ($sq) use ($user) {
                                  $sq->where('to_user_id', $user->id)
                                     ->orWhere('to_unit_id', $user->unit_id);
                              });
                        if ($user->role === 'staf_tu') {
                            $qE2->orWhereIn('status', ['pending_agenda', 'in_consideration', 'completed']);
                        }
                    }
                });
            });
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

        // Filter tabs: type param (all / unread / internal / external)
        $filterType = $request->get('type', 'all');
        if ($filterType === 'internal') {
            $q->where('type', 'internal');
        } elseif ($filterType === 'external') {
            $q->where('type', 'external');
        } elseif ($filterType === 'unread') {
            $q->whereDoesntHave('histories', function ($hq) {
                $hq->where('user_id', Auth::id())->where('action', 'read');
            });
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

    public function createExternal()
    {
        $units = \App\Models\Unit::where('id', '!=', Auth::user()->unit_id)->orderBy('name')->get();
        $users = \App\Models\User::with('unit')
            ->whereHas('organ', function($q) {
                $q->where('unit_id', Auth::user()->unit_id);
            })
            ->where('id', '!=', Auth::id())
            ->orderBy('name')->get();
        return view('letters.create_external', compact('units', 'users'));
    }

    public function storeExternal(Request $request)
    {
        $data = $request->validate([
            'external_sender_name' => 'required|string|max:255',
            'letter_number'        => 'required|string|max:255',
            'subject'              => 'required|string|max:255',
            'body'                 => 'required|string',
            'attachments.*'        => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'action_type'          => 'required|in:archive,forward_unit,forward_personal',
            'to_unit_id'           => 'nullable|exists:units,id',
            'to_user_id'           => 'nullable|exists:users,id',
        ]);

        $to_unit_id = null;
        $status = 'pending_agenda';

        if ($data['action_type'] === 'archive') {
            $to_unit_id = Auth::user()->unit_id;
            $status = 'completed';
        } elseif (in_array($data['action_type'], ['forward_unit', 'forward_personal'])) {
            $to_unit_id = Auth::user()->unit_id; // Kepemilikan awal
            $status = 'in_consideration';
        }

        $letter = Letter::create([
            'type'                 => 'external',
            'external_sender_name' => $data['external_sender_name'],
            'created_by_user_id'   => Auth::id(),
            'from_user_id'         => null,
            'to_unit_id'           => $to_unit_id,
            'letter_number'        => $data['letter_number'],
            'subject'              => $data['subject'],
            'body'                 => $data['body'],
            'status'               => $status,
        ]);

        // Buat Disposisi otomatis jika memilih Unit/Personal
        if ($data['action_type'] === 'forward_unit') {
            \App\Models\Disposition::create([
                'letter_id' => $letter->id,
                'from_user_id' => Auth::id(),
                'to_unit_id' => $data['to_unit_id'],
                'note' => 'Disposisi langsung dari Catat Surat Fisik',
                'status' => 'pending'
            ]);
        } elseif ($data['action_type'] === 'forward_personal') {
            \App\Models\Disposition::create([
                'letter_id' => $letter->id,
                'from_user_id' => Auth::id(),
                'to_user_id' => $data['to_user_id'],
                'note' => 'Disposisi langsung dari Catat Surat Fisik',
                'status' => 'pending'
            ]);
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                $letter->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        $msg = 'Surat Eksternal berhasil dicatat.';
        if ($data['action_type'] === 'archive') $msg = 'Surat Eksternal berhasil diarsipkan.';
        elseif ($data['action_type'] === 'forward_unit' || $data['action_type'] === 'forward_personal') $msg = 'Surat Eksternal berhasil didisposisikan.';

        return redirect()->route('letters.inbound')->with('success', $msg);
    }

    public function drafts(Request $request)
    {
        $user = Auth::user();
        $q = Letter::with(['recipientUser', 'recipientUnit'])
            ->whereHas('sender.organ', function($sq) use ($user) {
                $sq->where('unit_id', $user->unit_id);
            })
            ->whereIn('status', ['draft', 'pending_approval']);

        // -- FILTERS --
        if ($s = $request->search) {
            $q->where(function ($query) use ($s) {
                $query->where('letter_number', 'like', "%{$s}%")
                      ->orWhere('agenda_number', 'like', "%{$s}%")
                      ->orWhere('subject', 'like', "%{$s}%");
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

        return view('letters.drafts', compact('letters'));
    }

    public function outbound(Request $request)
    {
        $user = Auth::user();
        $q = Letter::with(['recipientUser', 'recipientUnit'])
            ->whereHas('sender.organ', function($sq) use ($user) {
                $sq->where('unit_id', $user->unit_id);
            })
            ->whereNotIn('status', ['draft', 'pending_approval']);

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
        if ($from = $request->date_from) {
            $q->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->date_to) {
            $q->whereDate('created_at', '<=', $to);
        }

        // Filter tabs dari view
        $filterStatus = $request->get('status', 'all');
        $filterType   = $request->get('type');

        if ($filterStatus === 'waiting') {
            $q->whereIn('status', ['pending_sending', 'pending_agenda', 'in_consideration', 'in_review_kasubag']);
        } elseif ($filterStatus === 'completed') {
            $q->where('status', 'completed');
        } elseif ($filterStatus !== 'all' && $filterStatus) {
            // fallback: filter status spesifik
            $q->where('status', $filterStatus);
        }

        if ($filterType) {
            $q->where('type', $filterType);
        }

        $letters = $q->latest()
            ->paginate(15)
            ->withQueryString();

        return view('letters.outbox', compact('letters'));
    }

    public function create(Request $request)
    {
        $userUnitId = Auth::user()->unit_id;
        
        $units = \App\Models\Unit::when($userUnitId, function($query) use ($userUnitId) {
            return $query->where('id', '!=', $userUnitId);
        })->get();

        $replyTo = null;
        $forward = null;

        if ($request->has('reply_to')) {
            $id = \Vinkla\Hashids\Facades\Hashids::decode($request->reply_to)[0] ?? null;
            if ($id) {
                $replyTo = \App\Models\Letter::find($id);
            }
        } elseif ($request->has('forward')) {
            $id = \Vinkla\Hashids\Facades\Hashids::decode($request->forward)[0] ?? null;
            if ($id) {
                $forward = \App\Models\Letter::find($id);
            }
        }

        return view('letters.create', compact('units', 'replyTo', 'forward'));
    }

    public function store(Request $request)
    {
        $isExternal = $request->letter_type === 'outbound_external';

        $rules = [
            'letter_type' => 'required|in:internal,outbound_external',
            'letter_number' => 'nullable|string',
            'subject' => 'required|string',
            'body' => 'required|string',
            'action' => 'required|in:draft,send',
            'attachments' => 'required|array|min:1',
            'attachments.*' => 'file|max:5120',
        ];

        if ($isExternal) {
            $rules['external_recipient_name'] = 'required|string|max:255';
        } else {
            $rules['to_unit_id'] = 'required|exists:units,id';
        }

        $data = $request->validate($rules);
        
        $status = 'draft';
        if ($request->action === 'send') {
            $status = 'pending_approval';
        }

        $letterData = [
            'type' => $data['letter_type'],
            'letter_number' => $data['letter_number'] ?? '-',
            'subject' => $data['subject'],
            'body' => $data['body'],
            'from_user_id' => Auth::id(),
            'status' => $status,
        ];

        if ($isExternal) {
            $letterData['external_recipient_name'] = $data['external_recipient_name'];
            $letterData['to_unit_id'] = null; // Ensuring it's null for external
        } else {
            $letterData['to_unit_id'] = $data['to_unit_id'];
        }

        $letter = Letter::create($letterData);

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
                    'file_name' => $originalName
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
            ->route('letters.drafts')
            ->with('success', $request->action === 'send'
                ? 'Surat berhasil diajukan untuk persetujuan.'
                : 'Draf surat berhasil disimpan.');
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
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashedId)[0] ?? abort(404);
        $letter = Letter::findOrFail($id);
        $this->authorize('view', $letter);

        // Mark as read in LetterRead
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && $letter->from_user_id !== $user->id) {
            \App\Models\LetterRead::firstOrCreate([
                'letter_id' => $letter->id,
                'user_id' => $user->id,
            ]);
        }

        return view('letters.show', compact('letter'));
    }

    public function edit($hashedId)
    {
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashedId)[0] ?? abort(404);
        $letter = Letter::with('attachments')->findOrFail($id);
        
        // Hanya bisa edit draft atau pending_approval (yang belum dikirim ke tujuan)
        if (!in_array($letter->status, ['draft', 'pending_approval'])) {
            abort(403, 'Surat tidak dapat diedit karena sudah diproses.');
        }

        // Pastikan user berhak (pembuat surat atau admin)
        $user = Auth::user();
        if ($letter->from_user_id !== $user->id && !in_array($user->role, ['admin_sekretariat', 'admin_unit'])) {
            abort(403, 'Anda tidak berhak mengedit surat ini.');
        }

        $userUnitId = Auth::user()->unit_id;
        $units = \App\Models\Unit::when($userUnitId, function($query) use ($userUnitId) {
            return $query->where('id', '!=', $userUnitId);
        })->get();

        return view('letters.edit', compact('letter', 'units'));
    }

    public function update(Request $request, $hashedId)
    {
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashedId)[0] ?? abort(404);
        $letter = Letter::findOrFail($id);

        if (!in_array($letter->status, ['draft', 'pending_approval'])) {
            abort(403, 'Surat tidak dapat diedit karena sudah diproses.');
        }

        $user = Auth::user();
        if ($letter->from_user_id !== $user->id && !in_array($user->role, ['admin_sekretariat', 'admin_unit'])) {
            abort(403, 'Anda tidak berhak mengedit surat ini.');
        }

        $isExternal = $request->letter_type === 'outbound_external';

        $rules = [
            'letter_type' => 'required|in:internal,outbound_external',
            'letter_number' => 'nullable|string',
            'subject' => 'required|string',
            'body' => 'required|string',
            'action' => 'required|in:draft,send',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:5120',
        ];

        if ($isExternal) {
            $rules['external_recipient_name'] = 'required|string|max:255';
        } else {
            $rules['to_unit_id'] = 'required|exists:units,id';
        }

        $data = $request->validate($rules);
        
        $status = 'draft';
        if ($request->action === 'send') {
            $status = 'pending_approval';
        }

        $letterData = [
            'type' => $data['letter_type'],
            'letter_number' => $data['letter_number'] ?? '-',
            'subject' => $data['subject'],
            'body' => $data['body'],
            'status' => $status,
        ];

        if ($isExternal) {
            $letterData['external_recipient_name'] = $data['external_recipient_name'];
            $letterData['to_unit_id'] = null;
        } else {
            $letterData['to_unit_id'] = $data['to_unit_id'];
            $letterData['external_recipient_name'] = null;
        }

        $letter->update($letterData);

        if ($request->hasFile('attachments')) {
            // Hapus lampiran lama
            foreach ($letter->attachments as $att) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($att->file_path);
                $att->delete();
            }

            // Simpan lampiran baru
            foreach ($request->file('attachments') as $file) {
                $originalName = $file->getClientOriginalName();
                $basename = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                $filename = $basename . '_' . time() . '.' . $extension;
                $path = $file->storeAs('attachments', $filename, 'public');

                \App\Models\Attachment::create([
                    'letter_id' => $letter->id,
                    'file_path' => $path,
                    'file_name' => $originalName
                ]);
            }
        }

        LetterHistory::create([
            'letter_id' => $letter->id,
            'user_id' => Auth::id(),
            'action' => 'updated',
            'note' => 'Surat diedit dan disimpan sebagai ' . ($status == 'draft' ? 'draft' : 'diajukan'),
        ]);

        return redirect()
            ->route($request->action === 'send' ? 'letters.outbound' : 'letters.drafts')
            ->with('success', $request->action === 'send'
                ? 'Surat berhasil diperbarui dan diajukan untuk persetujuan.'
                : 'Draf surat berhasil diperbarui.');
    }

    public function printDisposition($hashedId)
    {
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashedId)[0] ?? null;
        if (!$id) abort(404);

        $letter = Letter::with(['dispositions.fromUser', 'dispositions.toUser', 'dispositions.unit', 'histories'])->findOrFail($id);
        
        return view('letters.print_disposition', compact('letter'));
    }

    public function reply(Request $request, Letter $letter)
    {
        $data = $request->validate([
            'response_note' => 'required|string',
            'attachment' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
        ]);

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
            \App\Models\Attachment::create([
                'letter_id' => $letter->id,
                'file_path' => $path,
            ]);
        }

        \App\Models\LetterHistory::create([
            'letter_id' => $letter->id,
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'replied',
            'note' => $data['response_note'],
        ]);

        return back()->with('success', 'Balasan/Catatan berhasil ditambahkan.');
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
        $userRole = Auth::user()->role;
        
        if ($letter->status !== 'pending_approval' || !in_array($userRole, ['kepala_unit', 'subag_persuratan'])) {
            abort(403);
        }
        
        $letter->update(['status' => 'pending_sending']);
        
        $roleName = ($userRole === 'subag_persuratan') ? 'Subag Persuratan' : 'Kepala Unit';
        LetterHistory::create(['letter_id' => $letter->id, 'user_id' => Auth::id(), 'action' => 'approved', 'note' => 'Disetujui ' . $roleName]);
        
        return redirect()->route('tugas.accSurat')->with('success', 'Surat berhasil di-ACC. Menunggu Admin untuk mengirim fisik surat.');
    }

    public function submitDraft(Letter $letter)
    {
        $this->authorize('view', $letter);
        $user = Auth::user();
        
        if ($letter->status !== 'draft') {
            abort(403);
        }
        
        $letter->update(['status' => 'pending_approval']);
        
        LetterHistory::create([
            'letter_id' => $letter->id,
            'user_id' => Auth::id(),
            'action' => 'pending_approval',
            'note' => 'Draft diajukan untuk proses ACC'
        ]);
        
        return redirect()->route('letters.drafts')->with('success', 'Draft surat berhasil diajukan untuk proses ACC.');
    }

    public function sendFinal(Letter $letter)
    {
        $this->authorize('view', $letter);
        $user = Auth::user();
        
        if ($letter->status !== 'pending_sending' || !in_array($user->role, ['admin_unit', 'admin_sekretariat'])) {
            abort(403);
        }
        
        if ($letter->type === 'outbound_external') {
            $newStatus = 'completed';
            $note = 'Surat eksternal telah dikirim.';
        } else {
            $newStatus = 'pending_agenda';
            $note = 'Surat telah dikirim ke tujuan untuk diagendakan.';
        }
        
        $letter->update(['status' => $newStatus]);
        
        LetterHistory::create(['letter_id' => $letter->id, 'user_id' => $user->id, 'action' => 'sent', 'note' => $note]);
        
        return back()->with('success', $note);
    }
}
