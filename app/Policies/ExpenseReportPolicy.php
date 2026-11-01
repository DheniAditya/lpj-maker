<?php

namespace App\Policies;

use App\Models\ExpenseReport;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Aturan akses LPJ.
 *
 * Tiga cara seseorang boleh membuka LPJ:
 *  1. Pemilik      : user login yang membuat LPJ.
 *  2. Tamu pembuat : LPJ dibuat tanpa login di browser (sesi) yang sama.
 *  3. Link share   : pernah membuka link share yang aktif; boleh mengedit hanya jika role = editor.
 *
 * $user boleh null (tamu), jadi semua method memakai ?User.
 */
class ExpenseReportPolicy
{
    public const SESSION_GUEST_SLUGS = 'guest_lpj_slugs';

    public const SESSION_SHARE_TOKENS = 'accessed_share_tokens';

    public function view(?User $user, ExpenseReport $report): Response
    {
        if ($this->isCreator($user, $report) || $this->hasOpenedShareLink($report)) {
            return Response::allow();
        }

        return $this->denyNoAccess();
    }

    public function update(?User $user, ExpenseReport $report): Response
    {
        if ($this->isCreator($user, $report)) {
            return Response::allow();
        }

        if ($this->hasOpenedShareLink($report)) {
            return $report->share_role === ExpenseReport::ROLE_EDITOR
                ? Response::allow()
                : Response::deny('Anda hanya memiliki izin pelihat (viewer).');
        }

        return $this->denyNoAccess();
    }

    /** Hanya pembuat (bukan penerima link editor) yang boleh menghapus seluruh laporan. */
    public function delete(?User $user, ExpenseReport $report): Response
    {
        return $this->isCreator($user, $report)
            ? Response::allow()
            : Response::deny('Hanya pemilik laporan yang diizinkan untuk menghapus laporan ini.');
    }

    /** Pengaturan share hanya untuk pemilik yang login (tombolnya ada di dashboard). */
    public function share(?User $user, ExpenseReport $report): Response
    {
        return $this->isOwner($user, $report)
            ? Response::allow()
            : Response::deny('Unauthorized');
    }

    private function isOwner(?User $user, ExpenseReport $report): bool
    {
        return $user !== null && (int) $report->user_id === (int) $user->id;
    }

    private function isCreator(?User $user, ExpenseReport $report): bool
    {
        return $this->isOwner($user, $report)
            || in_array($report->slug, session(self::SESSION_GUEST_SLUGS, []), true);
    }

    private function hasOpenedShareLink(ExpenseReport $report): bool
    {
        return $report->is_shared
            && in_array($report->share_token, session(self::SESSION_SHARE_TOKENS, []), true);
    }

    private function denyNoAccess(): Response
    {
        return Response::deny('Anda tidak memiliki izin untuk mengakses LPJ ini.');
    }
}
