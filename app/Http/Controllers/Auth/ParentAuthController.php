<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ParentAuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.parent-register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'unique:users,phone'],
            'password' => ['required', Password::min(8), 'confirmed'],
        ], [
            'phone.unique' => 'Nomor WhatsApp sudah terdaftar. Silakan login.',
        ]);

        $phone = $this->normalizePhone($validated['phone']);

        $user = User::create([
            'name' => $validated['name'],
            'phone' => $phone,
            'role' => 'parent',
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('psb.wizard', ['step' => 1])
            ->with('success', 'Registrasi berhasil. Silakan lanjutkan mengisi Step 1 (Program & Dokumen).');
    }

    public function showLogin()
    {
        return view('auth.parent-login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string'],
        ]);

        $phone = $this->normalizePhone($validated['phone']);

        if (Auth::attempt(['phone' => $phone, 'password' => $validated['password'], 'role' => 'parent'], true)) {
            $request->session()->regenerate();
            return redirect()->route('app.dashboard');
        }

        return back()->withErrors([
            'phone' => 'Nomor WhatsApp atau password salah.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Anda telah logout.');
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        // 08xxxx -> 628xxxx
        if (str_starts_with($phone, '08')) {
            $phone = '62' . substr($phone, 1);
        }
        // +62xxxx -> 62xxxx
        if (str_starts_with($phone, '+62')) {
            $phone = '62' . substr($phone, 3);
        }
        return $phone;
    }
}
