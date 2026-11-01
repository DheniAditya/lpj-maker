<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LpjController;
use App\Http\Controllers\LpjEntryController;
use App\Http\Controllers\LpjShareController;
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

    // Transaksi (didefinisikan sebelum /{report} agar "entry" tidak dianggap id laporan)
    Route::patch('/entry/{entry}', [LpjEntryController::class, 'update'])->name('entry.update');
    Route::delete('/entry/{entry}', [LpjEntryController::class, 'destroy'])->name('entry.destroy');

    // Laporan
    Route::get('/{report:slug}', [LpjController::class, 'show'])->name('show');
    Route::put('/{report:slug}/update-title', [LpjController::class, 'updateTitle'])->name('update-title');
    Route::put('/{report:slug}/update-creator', [LpjController::class, 'updateCreator'])->name('update-creator');
    Route::get('/{report:slug}/download', [LpjController::class, 'downloadPdf'])->name('download');
    Route::post('/{report:slug}/add', [LpjEntryController::class, 'store'])->name('entry.store');
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

require __DIR__.'/auth.php';
