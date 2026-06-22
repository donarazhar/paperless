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
        if (Auth::user()->role !== 'admin_sekretariat') abort(403);
        
        $data = $request->validate([
            'agenda_number' => 'required|string',
            'note' => 'nullable|string',
        ]);

        $letter->update([
            'agenda_number' => $data['agenda_number'],
            'status' => 'in_review_subag'
        ]);

        LetterHistory::create([
            'letter_id' => $letter->id,
            'user_id' => Auth::id(),
            'action' => 'agenda_assigned',
            'note' => 'Agenda: ' . $data['agenda_number'],
        ]);

        $route = $letter->type === 'external' ? 'letters.inboundExternal' : 'letters.inbound';

        return redirect()
            ->route($route)
            ->with('success', 'Surat berhasil diagendakan dan diteruskan ke Subag Persuratan.');
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

        LetterHistory::create([
            'letter_id' => $letter->id,
            'user_id' => Auth::id(),
            'action' => 'disposed',
            'note' => $data['note'],
        ]);

        return redirect()
            ->route('letters.show', ['letter' => Hashids::encode($letter->id)])
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
        ]);

        $disposition->update([
            'status' => $data['action'],
            'response_note' => $data['response_note'],
        ]);

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
        if (!in_array(Auth::user()->role, ['bagian_tu', 'subag_persuratan', 'kepala_sekretariat'])) {
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
