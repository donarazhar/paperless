<?php
namespace App\Http\Controllers;

use App\Models\Disposition;
use App\Models\Letter;
use App\Models\LetterHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;

class DispositionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function agenda(Request $request, Letter $letter)
    {
        $role = Auth::user()->role;
        if (!in_array($role, ['admin_sekretariat', 'admin_unit'])) abort(403);
        
        $rules = [
            'note' => 'nullable|string',
        ];

        if ($role === 'admin_unit') {
            $rules['to_user_id'] = 'required|exists:users,id';
        }

        $data = $request->validate($rules);

        // Auto-generate agenda_number: nomor_urut / kodeunit / tahun
        $userUnit = Auth::user()->unit;
        $unitCode = $userUnit->code ?? 'UNIT';
        $year = date('Y');
        
        $allAgendas = Letter::whereNotNull('agenda_number')
            ->where('agenda_number', 'like', '%/' . $unitCode . '/' . $year)
            ->pluck('agenda_number');
            
        $nextNumber = 1;
        if ($allAgendas->isNotEmpty()) {
            $max = $allAgendas->map(function($agenda) {
                return (int) explode('/', $agenda)[0];
            })->max();
            $nextNumber = $max + 1;
        }
        $nomorUrut = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $agendaNumber = "{$nomorUrut}/{$unitCode}/{$year}";

        $newStatus = ($role === 'admin_unit') ? 'in_consideration' : 'in_review_subag';
        $successMsg = ($role === 'admin_unit') ? 'Surat berhasil diagendakan dan didisposisikan.' : 'Surat berhasil diagendakan dan diteruskan ke Subag Persuratan.';

        $letter->update([
            'agenda_number' => $agendaNumber,
            'status' => $newStatus
        ]);

        LetterHistory::create([
            'letter_id' => $letter->id,
            'user_id' => Auth::id(),
            'action' => 'agenda_assigned',
            'note' => 'Agenda: ' . $agendaNumber,
        ]);

        if ($role === 'admin_unit') {
            \App\Models\Disposition::create([
                'letter_id' => $letter->id,
                'from_user_id' => Auth::id(),
                'to_unit_id' => null,
                'to_user_id' => $data['to_user_id'],
                'note' => $data['note'] ?: 'Surat diagendakan & diteruskan.',
                'status' => 'pending',
            ]);

            LetterHistory::create([
                'letter_id' => $letter->id,
                'user_id' => Auth::id(),
                'action' => 'disposed',
                'note' => $data['note'] ?: 'Surat diagendakan & diteruskan.',
            ]);
        }

        $route = $letter->type === 'external' ? 'letters.inboundExternal' : 'letters.inbound';

        return redirect()
            ->route($route)
            ->with('success', $successMsg);
    }

    public function forwardToBagianTu(Request $request, Letter $letter)
    {
        if (Auth::user()->role !== 'subag_persuratan') abort(403);
        
        $letter->update(['status' => 'in_review_bagian_tu']);
        
        LetterHistory::create([
            'letter_id' => $letter->id,
            'user_id' => Auth::id(),
            'action' => 'forwarded',
            'note' => 'Diteruskan ke Bagian TU untuk Disposisi',
        ]);
        
        return back()->with('success', 'Surat diteruskan ke Bagian TU.');
    }

    public function store(Request $request, Letter $letter)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['bagian_tu', 'kepala_sekretariat', 'kepala_unit', 'sub_unit', 'admin_unit', 'subag_persuratan'])) {
            abort(403, 'Anda tidak memiliki akses untuk membuat disposisi.');
        }

        if ($user->role === 'admin_unit' && empty($letter->agenda_number)) {
            return back()->withErrors(['note' => 'Surat ini belum memiliki No. Agenda. Silakan beri nomor agenda terlebih dahulu.']);
        }

        $data = $request->validate([
            'to_unit_id' => 'nullable|exists:units,id|required_without:to_user_id',
            'to_user_id' => 'nullable|exists:users,id|required_without:to_unit_id',
            'note' => 'required|string',
        ]);

        $targetUnit = \App\Models\Unit::find($data['to_unit_id'] ?? null);
        $targetUser = \App\Models\User::with('unit')->find($data['to_user_id'] ?? null);
        
        $isSekretariat = ($targetUnit && $targetUnit->is_sekretariat) || 
                         ($targetUser && $targetUser->unit && $targetUser->unit->is_sekretariat);

        if ($isSekretariat) {
            // Jika ke sekretariat, arahkan ke subag_persuratan atau bagian_tu
            $bagTu = \App\Models\User::where('role', 'bagian_tu')->first();
            if ($bagTu) {
                $data['to_unit_id'] = null;
                $data['to_user_id'] = $bagTu->id;
            }
        }

        // Tutup disposisi sebelumnya yang tertuju pada user/unit ini (tandai forwarded)
        \App\Models\Disposition::where('letter_id', $letter->id)
            ->where(function($q) use ($user) {
                $q->where('to_user_id', $user->id)
                  ->orWhere('to_unit_id', $user->unit_id);
            })
            ->where('status', 'pending')
            ->update([
                'status' => 'forwarded',
                'response_note' => 'Didisposisikan kembali: ' . $data['note']
            ]);

        Disposition::create([
            'letter_id' => $letter->id,
            'from_user_id' => Auth::id(),
            'to_unit_id' => $data['to_unit_id'] ?? null,
            'to_user_id' => $data['to_user_id'] ?? null,
            'note' => $data['note'],
            'status' => 'pending',
        ]);

        $newStatus = 'in_consideration';
        if ($isSekretariat && !$letter->agenda_number) {
            $newStatus = 'pending_agenda';
        }

        $letter->update(['status' => $newStatus]);
        $letter->touch();

        LetterHistory::create([
            'letter_id' => $letter->id,
            'user_id' => Auth::id(),
            'action' => 'disposed',
            'note' => $data['note'],
        ]);

        $route = in_array(Auth::user()->role, ['admin_unit', 'admin_sekretariat']) ? 'letters.inbound' : 'tugas.index';

        return redirect()
            ->route($route)
            ->with('success', 'Disposisi berhasil dikirim.');
    }

    public function respond(Request $request, Disposition $disposition)
    {
        $user = Auth::user();
        if (!($disposition->to_user_id === $user->id || $disposition->to_unit_id === $user->unit_id)) {
            abort(403);
        }

        $data = $request->validate([
            'action' => 'required|in:pertimbangan,accepted,rejected,followup',
            'response_note' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
        ]);

        $disposition->update([
            'status' => $data['action'],
            'response_note' => $data['response_note'],
        ]);

        $disposition->letter->touch();

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $originalName = $file->getClientOriginalName();
                $basename = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $filename = $basename . '_' . time() . '.' . $extension;
                
                $path = $file->storeAs('attachments', $filename, 'public');
                \App\Models\Attachment::create([
                    'letter_id' => $disposition->letter_id,
                    'file_path' => $path,
                    'file_name' => $originalName
                ]);
            }
        }

        LetterHistory::create([
            'letter_id' => $disposition->letter_id,
            'user_id' => $user->id,
            'action' => 'disposition_responded',
            'note' => "[$data[action]] " . $data['response_note'],
        ]);

        return back()->with('success', 'Tanggapan berhasil disimpan.');
    }

    public function selesai(Request $request, Letter $letter)
    {
        if (!in_array(Auth::user()->role, ['bagian_tu', 'subag_persuratan', 'kepala_sekretariat', 'admin_unit', 'kepala_unit', 'sub_unit', 'admin_sekretariat'])) {
            abort(403);
        }

        $letter->update(['status' => 'completed']);

        LetterHistory::create([
            'letter_id' => $letter->id,
            'user_id' => Auth::id(),
            'action' => 'completed',
            'note' => 'Proses surat telah selesai.',
        ]);

        return redirect()
            ->route('letters.show', ['letter' => Hashids::encode($letter->id)])
            ->with('success', 'Surat telah ditandai Selesai.');
    }

    public function forwardDispositionToKepala(Letter $letter)
    {
        $user = Auth::user();
        if ($user->role !== 'admin_unit') abort(403);
        
        $dispo = Disposition::where('letter_id', $letter->id)
            ->where('to_unit_id', $user->unit_id)
            ->whereNull('to_user_id')
            ->where('status', 'pending')
            ->first();
            
        if ($dispo) {
            $dispo->update(['status' => 'forwarded_to_kepala', 'response_note' => 'Diteruskan ke Kepala Unit']);
            
            $kepala = \App\Models\User::whereHas('organ', function($q) use ($user) {
                $q->where('unit_id', $user->unit_id);
            })->where('role', 'kepala_unit')->first();

            if ($kepala) {
                Disposition::create([
                    'letter_id' => $letter->id,
                    'from_user_id' => $user->id,
                    'to_user_id' => $kepala->id,
                    'note' => 'Terusan dari Admin Unit: ' . $dispo->note,
                    'status' => 'pending',
                ]);
            }
        }
        
        LetterHistory::create([
            'letter_id' => $letter->id,
            'user_id' => $user->id,
            'action' => 'forwarded_disposition',
            'note' => 'Meneruskan disposisi ke Kepala Unit',
        ]);
        
        return back()->with('success', 'Disposisi diteruskan ke Kepala Unit.');
    }
}
