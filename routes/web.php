<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LpjController;
use App\Http\Controllers\LpjEntryController;
use App\Http\Controllers\LpjShareController;
use App\Http\Controllers\LpjSignatureController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
| Hak akses tiap LPJ (pemilik / tamu pembuat / link share) dicek di
| App\Policies\ExpenseReportPolicy, jadi rute LPJ sengaja tidak memakai middleware auth.
*/

Route::get('/', [LpjController::class, 'create'])->name('lpj.create');

Route::prefix('lpj')->name('lpj.')->group(function () {
    Route::post('/store', [LpjController::class, 'store'])->name('store');

    // Bagikan link
    Route::get('/share/{token}', [LpjShareController::class, 'open'])->name('share');
    Route::post('/{report}/toggle-share', [LpjShareController::class, 'update'])
        ->middleware('auth')->name('share.update');
    Route::post('/{report}/share/revoke', [LpjShareController::class, 'revoke'])
        ->middleware('auth')->name('share.revoke');

    // Transaksi (didefinisikan sebelum /{report} agar "entry" tidak dianggap id laporan)
    Route::patch('/entry/{entry}', [LpjEntryController::class, 'update'])->name('entry.update');
    Route::delete('/entry/{entry}', [LpjEntryController::class, 'destroy'])->name('entry.destroy');

    // Laporan
    Route::get('/{report:slug}', [LpjController::class, 'show'])->name('show');
    Route::put('/{report:slug}/update-title', [LpjController::class, 'updateTitle'])->name('update-title');
    Route::put('/{report:slug}/update-creator', [LpjController::class, 'updateCreator'])->name('update-creator');
    Route::get('/{report:slug}/download', [LpjController::class, 'downloadPdf'])->name('download');
    Route::post('/{report:slug}/add', [LpjEntryController::class, 'store'])->name('entry.store');
    Route::post('/{report:slug}/signature', [LpjSignatureController::class, 'store'])->name('signature.store');
    Route::delete('/{report:slug}/signature', [LpjSignatureController::class, 'destroy'])->name('signature.destroy');
    Route::delete('/{report}', [LpjController::class, 'destroy'])->middleware('auth')->name('destroy');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Panel admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::put('/users/{user}/password', [AdminUserController::class, 'resetPassword'])->name('users.password');
    Route::post('/users/{user}/reset-link', [AdminUserController::class, 'sendResetLink'])->name('users.reset-link');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});

require __DIR__.'/auth.php';
