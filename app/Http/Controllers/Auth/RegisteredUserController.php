<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge(['username' => User::normalizeUsername($request->input('username'))]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => User::usernameRules(),
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'username.regex' => 'Username hanya boleh berisi huruf kecil, angka, titik, garis bawah, atau strip (tanpa spasi).',
            'username.unique' => 'Username ini sudah dipakai. Coba yang lain.',
            'username.min' => 'Username minimal 3 karakter.',
            'name.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Ulangi password tidak sama.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
