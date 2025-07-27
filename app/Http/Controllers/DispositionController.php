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

    /**
     * Manager membuat disposisi
     */
    public function store(Request $request, Letter $letter)
    {
        if (Auth::user()->role !== 'manager') {
            abort(403);
        }

        $data = $request->validate([
            'to_unit_id' => 'nullable|exists:units,id|required_without:to_user_id',
            'to_user_id' => 'nullable|exists:users,id|required_without:to_unit_id',
            'note' => 'required|string',
        ]);

        $disp = Disposition::create([
            'letter_id' => $letter->id,
            'from_user_id' => Auth::id(),
            'to_unit_id' => $data['to_unit_id'] ?? null,
            'to_user_id' => $data['to_user_id'] ?? null,
            'note' => $data['note'],
            'status' => 'pending',
        ]);

        $letter->update(['status' => 'disposed']);

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

    /**
     * Penerima merespon disposisi: accept / reject / followup
     */
    public function respond(Request $request, Disposition $disposition)
    {
        $user = Auth::user();
        // pastikan penerima disposisi
        if (
            !(
                $disposition->to_user_id === $user->id ||
                $disposition->to_unit_id === $user->unit_id
            )
        ) {
            abort(403);
        }

        $data = $request->validate([
            'action' => 'required|in:accepted,rejected,followup',
            'response_note' => 'required_if:action,rejected,followup|string',
        ]);

        $disposition->update([
            'status' => $data['action'],
            'response_note' => $data['response_note'] ?? null,
        ]);

        LetterHistory::create([
            'letter_id' => $disposition->letter_id,
            'user_id' => $user->id,
            'action' => 'disposition_' . $data['action'],
            'note' => $data['response_note'] ?? null,
        ]);

        return redirect()
            ->route('letters.show', ['letter' => Hashids::encode($disposition->letter_id)])
            ->with('success', 'Anda telah ' . ($data['action'] === 'accepted' ? 'menerima' : 'menolak/tindak lanjut') . ' disposisi.');
    }
}
