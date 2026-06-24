<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;

// Import model & policy
use App\Models\Letter;
use App\Policies\LetterPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Daftarkan mapping model → policy
        Gate::policy(Letter::class, LetterPolicy::class);

        // Gunakan Bootstrap 5 untuk Pagination
        Paginator::useBootstrapFive();

        // Global variable for sidebar task counts
        \Illuminate\Support\Facades\View::composer(['layouts.app', 'layouts.mailbox'], function ($view) {
            $user = \Illuminate\Support\Facades\Auth::user();
            $pendingAccCount = 0;
            $pendingDispCount = 0;
            $pendingSendingCount = 0;

            if ($user && in_array($user->role, ['subag_persuratan', 'kepala_unit'])) {
                // Count pending ACC
                $qAcc = \App\Models\Letter::whereIn('type', ['internal', 'outbound_external'])
                                          ->where('status', 'pending_approval');
                if ($user->role === 'subag_persuratan') {
                    $qAcc->whereHas('sender', function($sq) {
                        $sq->where('role', 'admin_sekretariat');
                    });
                } else {
                    $qAcc->whereHas('sender.organ', function($sq) use ($user) {
                        $sq->where('unit_id', $user->unit_id);
                    });
                }
                $pendingAccCount = $qAcc->count();

                // Count pending Disposisi
                $qDisp = \App\Models\Letter::query();
                if ($user->role === 'subag_persuratan') {
                    $qDisp->whereNotNull('agenda_number')->where('status', 'in_review_subag');
                } else {
                    $qDisp->whereHas('dispositions', function ($sq) use ($user) {
                        $sq->where(function($q2) use ($user) {
                            $q2->where('to_user_id', $user->id)
                               ->orWhere('to_unit_id', $user->unit_id);
                        })->where('status', 'pending');
                    });
                }
                $pendingDispCount = $qDisp->count();
            }

            if ($user && in_array($user->role, ['admin_sekretariat', 'admin_unit'])) {
                // Count pending sending
                $qSend = \App\Models\Letter::whereIn('type', ['internal', 'outbound_external'])
                                           ->where('status', 'pending_sending');
                if ($user->role === 'admin_sekretariat') {
                    $qSend->whereHas('sender', function($sq) {
                        $sq->where('role', 'admin_sekretariat');
                    });
                } else {
                    $qSend->whereHas('sender.organ', function($sq) use ($user) {
                        $sq->where('unit_id', $user->unit_id);
                    });
                }
                $pendingSendingCount = $qSend->count();
            }

            $view->with(compact('pendingAccCount', 'pendingDispCount', 'pendingSendingCount'));
        });
    }
}
