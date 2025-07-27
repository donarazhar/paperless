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
        // Admin boleh lihat apapun
        if ($user->role === 'admin') {
            return true;
        }

        // STAFF
        // - Surat langsung ke dia (to_user_id)
//      - Surat yang ia kirim (from_user_id)
//      - Surat yang dideposisikan ke dia (dispositions.to_user_id)
        if ($user->role === 'staff') {
            return $letter->to_user_id === $user->id
                || $letter->from_user_id === $user->id
                || $letter->dispositions()
                    ->where('to_user_id', $user->id)
                    ->exists();
        }

        // MANAGER
        // - Surat ke unit mereka (to_unit_id)
//      - Surat personal ke mereka (to_user_id)
//      - Surat yang dideposisikan ke unit mereka atau ke personal mereka
        if ($user->role === 'manager') {
            return $letter->to_unit_id === $user->unit_id
                || $letter->to_user_id === $user->id
                || $letter->dispositions()
                    ->where(function ($q) use ($user) {
                        $q->where('to_unit_id', $user->unit_id)
                            ->orWhere('to_user_id', $user->id);
                    })
                    ->exists();
        }

        return false;
    }
}
