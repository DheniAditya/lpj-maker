<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseReport;
use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

/**
 * Panel admin: memantau dan mengelola pengguna.
 *
 * Password tersimpan sebagai hash (satu arah), jadi admin tidak bisa melihat password
 * pengguna. Pengguna yang lupa password mengirim permintaan dari halaman login; admin
 * melihatnya di panel ini lalu membuatkan password baru.
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
                ->orWhere('username', 'like', "%{$search}%")))
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

        $resetRequests = PasswordResetRequest::pending()->with('user')->latest('updated_at')->get();

        return view('admin.users.index', compact('users', 'stats', 'search', 'resetRequests'));
    }

    public function show(User $user): View
    {
        $reports = $user->expenseReports()->withTotals()->withCount('entries')->latest('updated_at')->get();

        $resetRequest = $user->passwordResetRequests()->pending()->latest('updated_at')->first();

        return view('admin.users.show', compact('user', 'reports', 'resetRequest'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->merge(['username' => User::normalizeUsername($request->input('username'))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => User::usernameRules($user->id),
            'is_admin' => ['boolean'],
        ], [
            'username.regex' => 'Username hanya boleh berisi huruf kecil, angka, titik, garis bawah, atau strip (tanpa spasi).',
            'username.unique' => 'Username ini sudah dipakai pengguna lain.',
        ]);

        $data['is_admin'] = $request->boolean('is_admin');

        if ($user->is($request->user()) && ! $data['is_admin']) {
            return back()->withErrors(['is_admin' => 'Anda tidak bisa mencabut status admin akun sendiri.']);
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

        // Permintaan lupa password dari pengguna ini dianggap selesai
        $user->passwordResetRequests()->pending()->get()->each->resolve($request->user());

        return back()->with('status', "Password {$user->name} sudah diganti. Sampaikan password baru ke pengguna (username: {$user->username}) dan minta ia menggantinya setelah login.");
    }

    /** Tandai permintaan lupa password selesai/diabaikan (mis. username tidak dikenal). */
    public function resolveResetRequest(Request $request, PasswordResetRequest $resetRequest): RedirectResponse
    {
        $resetRequest->resolve($request->user());

        return back()->with('status', 'Permintaan reset password ditandai selesai.');
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
