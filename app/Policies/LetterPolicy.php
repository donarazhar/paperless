<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Letter;
use Illuminate\Auth\Access\HandlesAuthorization;

class LetterPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the letter.
     */
    public function view(User $user, Letter $letter): bool
    {
        $userUnitId = $user->unit_id;

        // Peran-peran di Sekretariat
        if (in_array($user->role, ['admin_sekretariat', 'subag_persuratan', 'bagian_tu', 'kepala_sekretariat'])) {
            // Mereka bisa melihat semua surat yang sudah masuk ke Sekretariat (pending_agenda dan seterusnya)
            if (in_array($letter->status, ['draft', 'pending_approval'])) {
                // Kecuali surat tersebut berasal dari unit mereka sendiri (Sekretariat juga bisa buat surat)
                if ($letter->sender && $letter->sender->unit_id === $userUnitId) {
                    return true;
                }
                return false;
            }
            return true;
        }

        // Peran-peran di tingkat Unit
        if (in_array($user->role, ['admin_unit', 'kepala_unit', 'sub_unit'])) {
            // Bisa melihat jika surat dibuat oleh unitnya
            if ($letter->sender && $letter->sender->unit_id === $userUnitId) {
                return true;
            }
            // Bisa melihat jika ditujukan ke unitnya
            if ($letter->to_unit_id === $userUnitId) {
                return true;
            }
            // Bisa melihat jika ditujukan ke dirinya secara spesifik
            if ($letter->to_user_id === $user->id) {
                return true;
            }
            // Bisa melihat jika ada disposisi yang masuk ke unit atau ke dirinya
            return $letter->dispositions()->where(function ($q) use ($user, $userUnitId) {
                $q->where('to_unit_id', $userUnitId)
                  ->orWhere('to_user_id', $user->id);
            })->exists();
        }

        return false;
    }
}
