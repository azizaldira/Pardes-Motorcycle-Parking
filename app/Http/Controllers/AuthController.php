<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BusinessSetting;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            if (!Auth::user()->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                return back()->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan oleh Administrator. Silakan hubungi admin untuk informasi lebih lanjut.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            return redirect()->intended('dashboard')->with('success', 'Berhasil login! Selamat datang kembali, ' . Auth::user()->name . '.');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
