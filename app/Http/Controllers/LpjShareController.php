<?php

namespace App\Http\Controllers;

use App\Http\Requests\Lpj\UpdateShareRequest;
use App\Models\ExpenseReport;
use App\Policies\ExpenseReportPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

/** Fitur "Bagikan Link" LPJ. */
class LpjShareController extends Controller
{
    /** Penerima membuka link: token dicatat di sesi agar policy mengizinkan akses. */
    public function open(Request $request, string $token): RedirectResponse
    {
        $report = ExpenseReport::where('share_token', $token)
            ->where('is_shared', true)
            ->firstOrFail();

        $key = ExpenseReportPolicy::SESSION_SHARE_TOKENS;
        if (! in_array($token, $request->session()->get($key, []), true)) {
            $request->session()->push($key, $token);
        }

        return redirect()->route('lpj.show', $report->slug);
    }

    /** Pemilik menyalakan/mematikan link dan memilih izin (viewer/editor). */
    public function update(UpdateShareRequest $request, ExpenseReport $report): JsonResponse
    {
        Gate::authorize('share', $report);

        $report->update([
            'is_shared' => $request->boolean('is_shared'),
            'share_role' => $request->input('share_role', $report->share_role ?? ExpenseReport::ROLE_VIEWER),
            'share_token' => $report->share_token ?: Str::random(32),
        ]);

        return $this->success('Pengaturan berbagi diperbarui.', ['share_token' => $report->share_token]);
    }

    /**
     * Batalkan link share: link dimatikan dan diganti token baru, sehingga link lama
     * (dan semua orang yang pernah membukanya) langsung kehilangan akses.
     */
    public function revoke(ExpenseReport $report): JsonResponse
    {
        Gate::authorize('share', $report);

        $report->update([
            'is_shared' => false,
            'share_token' => Str::random(32),
        ]);

        return $this->success('Link share dibatalkan. Link lama sudah tidak bisa dibuka.', [
            'share_token' => $report->share_token,
        ]);
    }
}
