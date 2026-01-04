<?php

// app/Http/Controllers/Admin/AdminAuthController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // login by phone OR email
        $credentialPhone = ['phone' => $request->username, 'password' => $request->password];
        $credentialEmail = ['email' => $request->username, 'password' => $request->password];

        $ok = Auth::attempt($credentialPhone) || Auth::attempt($credentialEmail);

        if (!$ok) {
            return back()->withErrors(['username' => 'Login gagal. Periksa username dan password.']);
        }

        // pastikan role admin
        if (Auth::user()->role !== 'admin') {
            Auth::logout();
            return back()->withErrors(['username' => 'Akun ini bukan admin.']);
        }

        $request->session()->regenerate();
        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}

