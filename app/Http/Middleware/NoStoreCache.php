<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Halaman form login/daftar/lupa password tidak boleh disimpan di cache browser.
 * Halaman yang dipulihkan dari cache membawa token CSRF lama → "419 Page Expired".
 */
class NoStoreCache
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');

        return $response;
    }
}
