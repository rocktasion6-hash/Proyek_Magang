<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Memproses login pengguna.
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Proses autentikasi
        if (Auth::attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
        ])) {
            // Regenerasi session untuk keamanan
            $request->session()->regenerate();

            // Ambil user yang berhasil login
            $user = Auth::user();

            // Arahkan berdasarkan role
            if ($user->role === 'hrd') {
                return redirect()->route('hrd.dashboard');
            }

            if ($user->role === 'karyawan') {
                return redirect()->route('karyawan.dashboard');
            }

            // Jika role tidak dikenali
            Auth::logout();

            return redirect()->route('login')
                ->withErrors([
                    'username' => 'Role pengguna tidak dikenali.',
                ]);
        }

        // Login gagal
        return back()
            ->withErrors([
                'username' => 'Username atau password salah.',
            ])
            ->onlyInput('username');
    }

    /**
     * Logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Hapus session
        $request->session()->invalidate();

        // Buat token session baru
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}