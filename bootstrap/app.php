<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'no-store' => \App\Http\Middleware\NoStoreCache::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // "419 Page Expired" (token CSRF kedaluwarsa, mis. halaman login dibuka terlalu lama
        // atau dipulihkan dari cache browser): jangan tampilkan halaman error, arahkan ulang.
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() !== 419 || $request->expectsJson()) {
                return null;
            }

            // Sudah login (mis. lewat "Ingat saya") → langsung ke dashboard.
            if (Auth::check() && $request->is('login', 'register')) {
                return redirect()->intended(route('dashboard'));
            }

            // Logout dengan token kedaluwarsa → cukup keluarkan.
            if ($request->is('logout')) {
                Auth::guard('web')->logoutCurrentDevice();
                $request->session()->invalidate();

                return redirect('/');
            }

            return back()
                ->withInput($request->except('password', 'password_confirmation', '_token'))
                ->with('status', 'Halaman sudah terlalu lama dibuka. Silakan coba lagi.');
        });
    })->create();
