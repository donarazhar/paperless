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
        // Staf TU Sekretariat, Kasubag TU, dan Kepala Sekretariat berfungsi sebagai pengelola sentral
        // sehingga memiliki akses penuh untuk melihat semua laporan surat demi keperluan arsip dan pemantauan.
        if (in_array($user->role, ['staf_tu', 'kasubag_tu', 'kepala_sekretariat'])) {
            return true;
        }

        // Staf Unit Biasa
        // Bisa melihat surat yang ia kirim sendiri (outbox),
        // atau surat yang didisposisikan ke unitnya / ke dirinya secara personal.
        if ($user->role === 'staf_unit') {
            return $letter->from_user_id === $user->id
                || $letter->to_user_id === $user->id
                || $letter->to_unit_id === $user->unit_id
                || $letter->created_by_user_id === $user->id
                || $letter->dispositions()->where(function ($q) use ($user) {
                        $q->where('to_unit_id', $user->unit_id)
                          ->orWhere('to_user_id', $user->id);
                    })->exists();
        }

        return false;
    }
}
