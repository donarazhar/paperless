<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\DispositionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ProfileController;

// Auth
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::middleware(['auth', 'role:admin'])->group(function () {
        // Daftar semua surat
        Route::get('letters', [LetterController::class, 'index'])->name('letters.index');
    });

    // Dashboard & Profile (semua user)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('profile/password', [ProfileController::class, 'showPasswordForm'])->name('profile.password');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // **Staff-only: buat & kirim surat**
    Route::middleware('role:staff')->group(function () {
        Route::get('letters/create', [LetterController::class, 'create'])->name('letters.create');
        Route::post('letters', [LetterController::class, 'store'])->name('letters.store');
    });

    // **Inbox & Outbox (semua role)**
    Route::get('letters/inbox', [LetterController::class, 'inbound'])->name('letters.inbound');
    Route::get('letters/outbox', [LetterController::class, 'outbound'])->name('letters.outbound');
    Route::get('letters/{letter}', [LetterController::class, 'show'])->name('letters.show');
    Route::post('letters/{letter}/mark-read', [LetterController::class, 'markRead'])->name('letters.markRead');

    // **Manager-only: disposisi surat**
    Route::middleware('role:manager')->post(
        'letters/{letter}/dispositions',
        [DispositionController::class, 'store']
    )->name('letters.dispositions.store');

    Route::post(
        'dispositions/{disposition}/respond',
        [DispositionController::class, 'respond']
    )->name('dispositions.respond');

    // **Admin-only: manajemen user & unit**
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('units', UnitController::class)->except(['show']);
    });
});
