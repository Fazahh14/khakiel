<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    // Tampilkan form registrasi
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:100',
        'email' => [
            'required',
            'email',
            'unique:users,email',
            'regex:/^[a-zA-Z0-9._%+-]+@gdwgy\.com$/', 
        ],
        'password' => [
            'required',
            'confirmed',
            'min:8',
            'regex:/[a-z]/',      
            'regex:/[A-Z]/',      
            'regex:/[0-9]/',      
        ],
    ], [
        'email.regex' => 'Email harus menggunakan domain @gdwgy.com',
    ]);

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'buyer',
    ]);

    return redirect()->route('login')->with('status', 'Akun berhasil dibuat! Silakan login.');
    }
}