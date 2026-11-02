<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Lupa password tanpa email: pengguna mengirim permintaan, admin mendapat notifikasi
 * di Panel Admin lalu membuatkan password baru.
 */
class ForgotPasswordController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['username' => User::normalizeUsername($request->input('username'))]);

        $data = $request->validate([
            'username' => ['required', 'string', 'max:100'],
            'contact' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $user = User::where('username', $data['username'])->first();

        // Satu permintaan aktif per username: kirim ulang hanya memperbarui isinya.
        PasswordResetRequest::pending()
            ->where('username', $data['username'])
            ->firstOrNew()
            ->fill($data + ['user_id' => $user?->id])
            ->touch();

        // Pesan sama untuk username yang ada / tidak ada (tidak membocorkan daftar akun).
        return back()
            ->with('status', 'Permintaan terkirim. Admin akan mereset password kamu lalu memberi tahu password barunya.')
            ->with('sent_username', $data['username']);
    }
}
