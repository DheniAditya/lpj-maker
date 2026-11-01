<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseReport;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

/**
 * Panel admin: memantau dan mengelola pengguna.
 *
 * Password tersimpan sebagai hash (satu arah), jadi admin tidak bisa melihat password
 * pengguna. Untuk pengguna yang lupa password, admin bisa membuatkan password baru
 * atau mengirim link reset ke email pengguna.
 */
class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $users = User::query()
            ->withCount('expenseReports')
            ->when($search, fn ($query) => $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")))
            ->orderByDesc('is_admin')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'users' => User::count(),
            'reports' => ExpenseReport::count(),
            'guestReports' => ExpenseReport::whereNull('user_id')->count(),
            'sharedReports' => ExpenseReport::where('is_shared', true)->count(),
        ];

        return view('admin.users.index', compact('users', 'stats', 'search'));
    }

    public function show(User $user): View
    {
        $reports = $user->expenseReports()->withTotals()->withCount('entries')->latest('updated_at')->get();

        return view('admin.users.show', compact('user', 'reports'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'is_admin' => ['boolean'],
        ]);

        $data['is_admin'] = $request->boolean('is_admin');

        if ($user->is($request->user()) && ! $data['is_admin']) {
            return back()->withErrors(['is_admin' => 'Anda tidak bisa mencabut status admin akun sendiri.']);
        }

        if ($data['email'] !== $user->email) {
            $data['email_verified_at'] = null;
        }

        $user->forceFill($data)->save(); // is_admin sengaja tidak fillable

        return back()->with('status', 'Data pengguna diperbarui.');
    }

    /** Admin membuatkan password baru, lalu menyampaikannya ke pengguna. */
    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $user->forceFill([
            'password' => Hash::make($request->input('password')),
            'remember_token' => null,
        ])->save();

        return back()->with('status', "Password {$user->name} sudah diganti. Sampaikan password baru ke pengguna dan minta ia menggantinya setelah login.");
    }

    /** Kirim link reset password ke email pengguna (butuh pengaturan email/SMTP di .env). */
    public function sendResetLink(User $user): RedirectResponse
    {
        $status = Password::sendResetLink(['email' => $user->email]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', "Link reset password dikirim ke {$user->email}.")
            : back()->withErrors(['email' => __($status)]);
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'Anda tidak bisa menghapus akun sendiri dari panel admin.']);
        }

        $name = $user->name;
        $user->delete(); // LPJ, nota, dan tanda tangan ikut terhapus (User::booted)

        return redirect()->route('admin.users.index')->with('status', "Akun {$name} beserta semua LPJ-nya sudah dihapus.");
    }
}
