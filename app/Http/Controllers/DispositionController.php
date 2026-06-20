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
        $this->authorizeRole('staf_tu');
        
        $data = $request->validate([
            'agenda_number' => 'required|string',
            'note' => 'nullable|string',
        ]);

        $letter->update([
            'agenda_number' => $data['agenda_number'],
            'status' => 'in_review_kasubag'
        ]);

        $kasubagTuUser = \App\Models\User::where('role', 'kasubag_tu')->first();
        if ($kasubagTuUser) {
            Disposition::create([
                'letter_id' => $letter->id,
                'from_user_id' => Auth::id(),
                'to_user_id' => $kasubagTuUser->id,
                'note' => $data['note'] ?? 'Mohon arahan/disposisi',
                'status' => 'pending',
            ]);
        }

        LetterHistory::create([
            'letter_id' => $letter->id,
            'user_id' => Auth::id(),
            'action' => 'agenda_assigned',
            'note' => 'Agenda: ' . $data['agenda_number'],
        ]);

        return redirect()
            ->route('letters.show', ['letter' => Hashids::encode($letter->id)])
            ->with('success', 'Surat berhasil diagendakan dan diteruskan ke Kasubag TU.');
    }

    public function store(Request $request, Letter $letter)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['kasubag_tu', 'kepala_sekretariat', 'staf_unit', 'staf_tu'])) {
            abort(403, 'Anda tidak memiliki akses untuk membuat disposisi.');
        }

        $data = $request->validate([
            'to_unit_id' => 'nullable|exists:units,id|required_without:to_user_id',
            'to_user_id' => 'nullable|exists:users,id|required_without:to_unit_id',
            'note' => 'required|string',
        ]);

        Disposition::create([
            'letter_id' => $letter->id,
            'from_user_id' => Auth::id(),
            'to_unit_id' => $data['to_unit_id'] ?? null,
            'to_user_id' => $data['to_user_id'] ?? null,
            'note' => $data['note'],
            'status' => 'pending',
        ]);

        $letter->update(['status' => 'in_consideration']);

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
        $this->authorizeRole('staf_tu');

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
}
