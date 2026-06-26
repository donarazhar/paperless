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
                    $qDisp->where(function($query) use ($user) {
                        $query->where(function($q1) {
                            $q1->whereNotNull('agenda_number')
                               ->where('status', 'in_review_subag');
                        })->orWhereHas('dispositions', function($sq) use ($user) {
                            $sq->where('to_user_id', $user->id)
                               ->where('status', 'pending');
                        });
                    });
                } else {
                    $qDisp->whereHas('dispositions', function ($sq) use ($user) {
                        $sq->where('to_user_id', $user->id)
                           ->where('status', 'pending');
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

            $unreadInboxCount = 0;
            $unreadDispCount = 0;
            $unreadAccCount = 0;

            if ($user) {
                // Base Inbox Query
                $qIn = \App\Models\Letter::whereIn('type', ['internal', 'external'])
                                         ->whereNotIn('status', ['draft', 'pending_approval', 'pending_sending']);
                if (in_array($user->role, ['kepala_unit', 'sub_unit'])) {
                    $qIn->where(function($q) use ($user) {
                        $q->where('to_unit_id', $user->unit_id)->orWhere('to_user_id', $user->id)
                          ->orWhereHas('dispositions', function($dq) use ($user) {
                              $dq->where('to_user_id', $user->id);
                          });
                    });
                } elseif (in_array($user->role, ['admin_unit', 'staf_unit'])) {
                    $qIn->where(function($q) use ($user) {
                        $q->where('to_unit_id', $user->unit_id)->orWhere('to_user_id', $user->id);
                    });
                }
                $qIn->where('from_user_id', '!=', $user->id); // Usually don't count own sent letters in inbox unread
                // Unread logic: tidak ada di tabel letter_reads untuk user ini
                $qIn->whereDoesntHave('reads', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
                $unreadInboxCount = $qIn->count();

                // Unread Disposisi
                if (in_array($user->role, ['subag_persuratan', 'kepala_unit', 'sub_unit', 'staf_unit'])) {
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
                    $qDisp->where('from_user_id', '!=', $user->id);
                    $qDisp->whereDoesntHave('histories', function($q) use ($user) {
                        $q->where('user_id', $user->id)->where('action', 'read');
                    });
                    $unreadDispCount = $qDisp->count();
                }

                // Pending Kotak Disposisi (myDisposisi)
                $pendingMyDispCount = 0;
                if (in_array($user->role, ['kepala_sekretariat', 'sub_unit', 'bagian_tu'])) {
                    $qMyDisp = \App\Models\Letter::query();
                    
                    if ($user->role === 'bagian_tu') {
                        $qMyDisp->where(function($query) use ($user) {
                            $query->where('status', 'in_review_bagian_tu')
                                  ->orWhereHas('dispositions', function($sq) use ($user) {
                                      $sq->where(function($q2) use ($user) {
                                          $q2->where('to_user_id', $user->id)
                                             ->orWhere('to_unit_id', $user->unit_id);
                                      })->where('status', 'pending');
                                  });
                        });
                    } else {
                        $qMyDisp->whereHas('dispositions', function ($sq) use ($user) {
                            $sq->where(function($q2) use ($user) {
                                $q2->where('to_user_id', $user->id)
                                   ->orWhere('to_unit_id', $user->unit_id);
                            })->where('status', 'pending');
                        });
                    }
                    
                    $pendingMyDispCount = $qMyDisp->count();
                }

                // Unread Acc Surat
                if (in_array($user->role, ['subag_persuratan', 'kepala_unit'])) {
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
                    $qAcc->where('from_user_id', '!=', $user->id);
                    $qAcc->whereDoesntHave('histories', function($q) use ($user) {
                        $q->where('user_id', $user->id)->where('action', 'read');
                    });
                    $unreadAccCount = $qAcc->count();
                }
                // Draft Count
                $draftCount = \App\Models\Letter::whereHas('sender.organ', function($sq) use ($user) {
                    $sq->where('unit_id', $user->unit_id);
                })->whereIn('status', ['draft', 'pending_approval'])->count();
            }

            $view->with(compact('pendingAccCount', 'pendingDispCount', 'pendingSendingCount', 'unreadInboxCount', 'pendingMyDispCount', 'draftCount'));
        });
    }
}
