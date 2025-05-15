<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Tampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Coba autentikasi
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return match (Auth::user()->role) {
                'buyer' => redirect()->route('pembeli.produk.index')->with('success', 'Login berhasil!'),
                'seller' => redirect()->route('admin.akun.index')->with('success', 'Login admin berhasil!'), // ← diperbaiki disini
                default => abort(403, 'Role tidak dikenali'),
            };
        }

        // Jika gagal login
        return back()->withErrors([
            'email' => 'Ups! Email atau password salah.',
        ])->onlyInput('email');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('pembeli.produk.index')->with('status', 'Berhasil logout!');
    }
}
