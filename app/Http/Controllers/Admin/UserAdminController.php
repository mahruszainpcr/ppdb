<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = User::query()
            ->where('role', 'parent')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $s = trim($request->search);
            $q->where(function ($qq) use ($s) {
                $qq->where('name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $users = $q->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->role !== 'parent') {
            return back()->withErrors(['user' => 'User ini bukan akun wali.']);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        // normalisasi no HP
        $data['phone'] = $this->normalizePhone($data['phone']);

        // pastikan no HP unik
        if (User::where('phone', $data['phone'])->where('id', '!=', $user->id)->exists()) {
            return back()->withErrors(['phone' => 'Nomor HP sudah digunakan user lain.']);
        }

        $user->update($data);

        return back()->with('success', 'Data user berhasil diperbarui.');
    }

    public function resetPassword(User $user)
    {
        if ($user->role !== 'parent' || !$user->phone) {
            return back()->withErrors(['reset' => 'Tidak bisa reset password user ini.']);
        }

        $password = $this->normalizePhone($user->phone);

        $user->update([
            'password' => Hash::make($password),
        ]);

        return back()
            ->with('reset_password_success', true)
            ->with('reset_password_value', $password);
    }

    private function normalizePhone(string $phone): string
    {
        $p = preg_replace('/[^0-9+]/', '', $phone) ?? $phone;

        if (str_starts_with($p, '08')) {
            $p = '62' . substr($p, 1);
        }
        if (str_starts_with($p, '+62')) {
            $p = substr($p, 1);
        }
        return $p;
    }
}
