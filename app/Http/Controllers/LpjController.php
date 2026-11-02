<?php

namespace App\Http\Controllers;

use App\Http\Requests\Lpj\StoreReportRequest;
use App\Models\ExpenseReport;
use App\Models\User;
use App\Policies\ExpenseReportPolicy;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Laporan LPJ: buat, buka, ubah judul/penanggung jawab, unduh PDF, hapus.
 * Transaksi ada di LpjEntryController, fitur bagikan link di LpjShareController.
 */
class LpjController extends Controller
{
    public function create(): View
    {
        return view('lpj.create');
    }

    public function store(StoreReportRequest $request): RedirectResponse
    {
        if ($request->user()?->hasReachedReportLimit()) {
            return back()->withInput()->withErrors([
                'title' => 'Arsip LPJ sudah mencapai batas '.User::MAX_REPORTS.'. Hapus LPJ lama di dashboard untuk membuat yang baru.',
            ]);
        }

        $report = ExpenseReport::create($request->validated() + [
            'user_id' => $request->user()?->id,
        ]);

        // Tamu tidak punya akun, jadi kepemilikan disimpan di sesi browser.
        if (! $request->user()) {
            $request->session()->push(ExpenseReportPolicy::SESSION_GUEST_SLUGS, $report->slug);
        }

        return redirect()->route('lpj.show', $report->slug);
    }

    public function show(ExpenseReport $report): View
    {
        Gate::authorize('view', $report);

        $report->load('entries.images');

        return view('lpj.workspace', compact('report'));
    }

    public function updateTitle(Request $request, ExpenseReport $report): JsonResponse
    {
        Gate::authorize('update', $report);

        $data = $request->validate(['title' => ['required', 'string', 'max:255']]);
        $report->update($data);

        return $this->success('Judul LPJ berhasil diperbarui!', ['new_title' => $report->title]);
    }

    public function updateCreator(Request $request, ExpenseReport $report): JsonResponse
    {
        Gate::authorize('update', $report);

        $data = $request->validate(['creator_name' => ['required', 'string', 'max:255']]);
        $report->update($data);

        return $this->success('Nama diperbarui!', ['new_name' => $report->creator_name]);
    }

    public function downloadPdf(ExpenseReport $report): Response
    {
        Gate::authorize('view', $report);

        if (! $report->hasSignature()) {
            return redirect()->route('lpj.show', $report->slug)
                ->with('error', 'Tambahkan tanda tangan terlebih dahulu sebelum mengunduh PDF.');
        }

        if ($report->creditNumbersMissingReceipt()) {
            return redirect()->route('lpj.show', $report->slug)
                ->with('error', 'Nota belum lengkap. Download PDF terkunci.');
        }

        $report->load('entries.images');

        return Pdf::loadView('lpj.pdf_template', compact('report'))
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'chroot' => public_path(),
            ])
            ->download("LPJ - {$report->title}.pdf");
    }

    public function destroy(ExpenseReport $report): JsonResponse
    {
        Gate::authorize('delete', $report);

        $report->delete(); // entri & file nota ikut terhapus lewat model event

        return $this->success('Laporan dan semua lampiran berhasil dihapus.');
    }
}
