<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login DAN role-nya admin
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request); // Silakan lewat
        }

        // Jika bukan admin, tendang keluar (403 Forbidden)
        abort(403, 'AKSES DITOLAK: Halaman ini khusus Administrator.');
    }
}