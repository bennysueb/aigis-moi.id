<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Cek apakah user login dan punya data tenant yang aktif
        if (!$user || !$user->tenant || $user->tenant->status !== 'active') {
            // Jika belum punya toko, lempar ke halaman registrasi toko (nanti dibuat)
            // Atau tampilkan 403
            abort(403, 'Anda belum mendaftar sebagai Tenant atau akun belum aktif.');
        }

        return $next($request);
    }
}
