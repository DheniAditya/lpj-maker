<?php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LpjController;
use App\Models\ExpenseEntry;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;


Route::get('/', [LpjController::class, 'create'])->name('lpj.create');

Route::post('/lpj/store', [LpjController::class, 'storeReport'])->name('lpj.store');

Route::get('/lpj/{report:slug}', [LpjController::class, 'show'])->name('lpj.show');
Route::put('/lpj/{report:slug}/update-title', [LpjController::class, 'updateTitle'])->name('lpj.update-title');
Route::put('/lpj/{report:slug}/update-creator', [LpjController::class, 'updateCreator'])->name('lpj.update-creator');
Route::post('/lpj/{report:slug}/add', [LpjController::class, 'storeEntry'])->name('lpj.entry.store'); 
Route::get('/lpj/{report:slug}/download', [LpjController::class, 'downloadPdf'])->name('lpj.download');
Route::patch('/lpj/entry/{entry}', [LpjController::class, 'updateEntry'])->name('lpj.entry.update');
Route::delete('/lpj/entry/{entry}', [LpjController::class, 'destroyEntry'])->name('lpj.entry.destroy');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/lpj/{id}', [App\Http\Controllers\LpjController::class, 'destroy'])->name('lpj.destroy');
    });

require __DIR__.'/auth.php';