<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Cek apakah user sesuai role, abort 403 jika tidak.
     */
    protected function authorizeRole(string $role): void
    {
        if (!Auth::check() || Auth::user()->role !== $role) {
            abort(403, 'Unauthorized action.');
        }
    }
}
