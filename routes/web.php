<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\PresensiAuthController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\DispositionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ProfileController;

// Auth
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');


Route::get('auth/presensi', [PresensiAuthController::class, 'redirect'])->name('presensi.login');
Route::get('auth/presensi/callback', [PresensiAuthController::class, 'callback'])->name('presensi.callback');

// API Internal untuk mengecek akses dari PresensiGPS
Route::get('api/check-user', function (\Illuminate\Http\Request $request) {
    $email = $request->query('email');
    if (!$email) return response()->json(['exists' => false, 'role' => null]);
    $user = \App\Models\User::where('email', $email)->first();
    return response()->json([
        'exists' => $user !== null,
        'role' => $user ? $user->role : null
    ]);
});

Route::middleware('auth')->group(function () {
    Route::get('letters', [LetterController::class, 'index'])->name('letters.index');

    Route::get('/', function () {
        $role = auth()->user()->role ?? '';
        if ($role === 'admin') {
            return redirect()->route('users.index');
        }
        if (in_array($role, ['bagian_tu', 'kepala_sekretariat', 'sub_unit'])) {
            return redirect()->route('tugas.myDisposisi');
        }
        if (in_array($role, ['subag_persuratan', 'kepala_unit'])) {
            return redirect()->route('tugas.disposisi');
        }
        return redirect()->route('letters.inbound');
    })->name('dashboard');
    
    // Superadmin Routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/monitoring', [\App\Http\Controllers\AdminDashboardController::class, 'monitoring'])->name('admin.monitoring');
        Route::get('/admin/logs', [\App\Http\Controllers\AdminDashboardController::class, 'logs'])->name('admin.logs');
    });
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('profile/password', [ProfileController::class, 'showPasswordForm'])->name('profile.password');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Pembuatan Surat
    Route::middleware('role:admin_unit,kepala_unit,sub_unit,admin_sekretariat,subag_persuratan,bagian_tu')->group(function () {
        Route::get('letters/create', [LetterController::class, 'create'])->name('letters.create');
        Route::post('letters', [LetterController::class, 'store'])->name('letters.store');
        
        // Catat Surat Eksternal Masuk (Fisik)
        Route::get('letters/create-external', [LetterController::class, 'createExternal'])->name('letters.createExternal');
        Route::post('letters/external', [LetterController::class, 'storeExternal'])->name('letters.storeExternal');
        
        Route::post('letters/{letter}/update-notes', [LetterController::class, 'updateExternalNotes'])->name('letters.updateExternalNotes');
    });

    Route::get('letters/inbox', [LetterController::class, 'inbound'])->name('letters.inbound');
    Route::get('letters/drafts', [LetterController::class, 'drafts'])->name('letters.drafts');
    Route::get('letters/outbox', [LetterController::class, 'outbound'])->name('letters.outbound');
    Route::get('letters/arsip', [LetterController::class, 'arsip'])->name('letters.arsip');
    
    // New Report Routes
    Route::get('letters/report-outbound-internal', [LetterController::class, 'reportOutboundInternal'])->name('letters.reportOutboundInternal');
    Route::get('letters/report-outbound-external', [LetterController::class, 'reportOutboundExternal'])->name('letters.reportOutboundExternal');
    
    // Tugas Routes
    Route::get('tugas', [\App\Http\Controllers\TugasController::class, 'index'])->name('tugas.index');
    Route::get('tugas/disposisi', [\App\Http\Controllers\TugasController::class, 'disposisi'])->name('tugas.disposisi');
    Route::get('tugas/acc-surat', [\App\Http\Controllers\TugasController::class, 'accSurat'])->name('tugas.accSurat');
    Route::get('tugas/my-disposisi', [\App\Http\Controllers\TugasController::class, 'myDisposisi'])->name('tugas.myDisposisi');

    Route::get('letters/{letter}', [LetterController::class, 'show'])->name('letters.show');
    Route::get('letters/{letter}/edit', [LetterController::class, 'edit'])->name('letters.edit');
    Route::put('letters/{letter}', [LetterController::class, 'update'])->name('letters.update');
    Route::get('letters/{letter}/print-disposition', [LetterController::class, 'printDisposition'])->name('letters.printDisposition');
    Route::post('letters/{letter}/reply', [LetterController::class, 'reply'])->name('letters.reply');
    Route::post('letters/{letter}/mark-read', [LetterController::class, 'markRead'])->name('letters.markRead');

    // Aksi Workflow Surat
    Route::post('letters/{letter}/approve', [LetterController::class, 'approve'])->name('letters.approve');
    Route::post('letters/{letter}/send-final', [LetterController::class, 'sendFinal'])->name('letters.sendFinal');
    Route::post('letters/{letter}/submit-draft', [LetterController::class, 'submitDraft'])->name('letters.submitDraft');
    Route::post('letters/{letter}/agenda', [DispositionController::class, 'agenda'])->name('letters.agenda');
    Route::post('letters/{letter}/forward-tu', [DispositionController::class, 'forwardToBagianTu'])->name('letters.forwardToBagianTu');
    Route::post('letters/{letter}/forward-kepala', [DispositionController::class, 'forwardDispositionToKepala'])->name('letters.dispositions.forwardToKepala');
    Route::post('letters/{letter}/complete', [DispositionController::class, 'selesai'])->name('letters.complete');
    
    Route::post('letters/{letter}/dispositions', [DispositionController::class, 'store'])->name('letters.dispositions.store');
    Route::put('dispositions/{disposition}/respond', [DispositionController::class, 'respond'])->name('letters.dispositions.respond');

    // Master Data (Hanya Hak Akses Role)
    Route::middleware('role:admin,admin_sekretariat,subag_persuratan,bagian_tu')->group(function () {
        Route::resource('users', UserController::class);
        // Note: organs, units, dan branches sekarang dikelola secara terpusat di PresensiGPS
    });
});
