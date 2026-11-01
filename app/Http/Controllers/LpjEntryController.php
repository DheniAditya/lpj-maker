<?php

namespace App\Http\Controllers;

use App\Http\Requests\Lpj\StoreEntryRequest;
use App\Http\Requests\Lpj\UpdateEntryRequest;
use App\Models\ExpenseEntry;
use App\Models\ExpenseReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/** Transaksi (debit/kredit) di dalam satu LPJ beserta foto notanya. */
class LpjEntryController extends Controller
{
    public function store(StoreEntryRequest $request, ExpenseReport $report): JsonResponse
    {
        Gate::authorize('update', $report);

        DB::transaction(function () use ($request, $report) {
            $entry = $report->entries()->create($request->entryData());
            $entry->attachImages($request->uploadedImages());
        });

        return $this->success('Transaksi berhasil disimpan');
    }

    public function update(UpdateEntryRequest $request, ExpenseEntry $entry): JsonResponse
    {
        Gate::authorize('update', $entry->expenseReport);

        DB::transaction(function () use ($request, $entry) {
            $entry->update($request->entryData());
            $entry->detachImages($request->deletedImageIds());
            $entry->attachImages($request->uploadedImages());
        });

        return $this->success('Entri berhasil diperbarui!');
    }

    public function destroy(ExpenseEntry $entry): JsonResponse
    {
        Gate::authorize('update', $entry->expenseReport);

        $entry->delete(); // file nota ikut terhapus lewat model event

        return $this->success('Entri dan semua nota berhasil dihapus.');
    }
}
