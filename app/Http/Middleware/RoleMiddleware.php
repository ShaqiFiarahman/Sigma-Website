<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    // Periksa Peran Pengguna
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Kalau user belum login, langsung redirect ke halaman login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userRole = strtolower($user->role);
        $roles = array_map('strtolower', $roles);

        // Jika role user terdaftar dalam daftar role yang diperbolehkan, lanjutkan request
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
