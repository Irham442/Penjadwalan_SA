<?php

namespace App\Http\Middleware;

// KESALAHAN #1 DIPERBAIKI: Menambahkan 'use Auth'
use Illuminate\Support\Facades\Auth;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekPeran
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles   // Parameter ini akan berisi peran yang diizinkan dari file rute (misal: 'Kurikulum')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pengecekan ini sebenarnya tidak wajib karena rute kita sudah dilindungi 'auth',
        // tapi tidak apa-apa untuk keamanan ganda.
        if (!Auth::check()) {
            return redirect('login');
        }

        // KESALAHAN #2 DIPERBAIKI: Mengambil nilai peran secara langsung
        $userRole = Auth::user()->role; // Ini akan menghasilkan string "Kurikulum" atau "Super Admin"

        // Cek apakah peran user ($userRole) ada di dalam daftar peran yang diizinkan ($roles)
        if (in_array($userRole, $roles)) {
            // Jika cocok, izinkan user melanjutkan ke halaman yang dituju
            return $next($request);
        }

        // Jika tidak diizinkan, kembalikan user ke halaman dashboard utama atau halaman sebelumnya
        // dengan pesan error.
        return redirect('/dashboard')->with('error', 'Anda tidak memiliki hak akses.');
    }
}