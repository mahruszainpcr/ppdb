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
        return view('admin.users.index');
    }

    public function data(Request $request)
    {
        $baseQuery = User::query()->where('role', 'parent');
        $recordsTotal = (clone $baseQuery)->count();

        $search = $request->input('search');
        if (is_array($search)) {
            $search = $search['value'] ?? null;
        }
        if ($search) {
            $s = trim($search);
            $baseQuery->where(function ($qq) use ($s) {
                $qq->where('name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $columns = [
            0 => 'name',
            1 => 'phone',
            2 => 'created_at',
        ];
        $orderColumn = $columns[$request->input('order.0.column')] ?? 'created_at';
        $orderDir = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 15);
        if ($length <= 0) {
            $length = 15;
        }

        $users = $baseQuery
            ->orderBy($orderColumn, $orderDir)
            ->skip($start)
            ->take($length)
            ->get();

        $data = $users->map(function (User $u) {
            $name = e($u->name ?? '-');
            $phone = e($u->phone ?? '-');
            $actions = '<button class="btn btn-sm btn-outline-primary btn-edit-user" data-id="' . $u->id . '" data-name="' . $name . '" data-phone="' . $phone . '" data-bs-toggle="modal" data-bs-target="#editUserModal">Edit</button> ';
            $actions .= '<button class="btn btn-sm btn-outline-warning btn-reset-user" data-id="' . $u->id . '" data-phone="' . $phone . '" data-bs-toggle="modal" data-bs-target="#resetUserModal">Reset Password</button>';

            return [
                'name' => $name,
                'phone' => $phone,
                'created_at' => $u->created_at ? $u->created_at->format('d M Y') : '-',
                'actions' => $actions,
            ];
        })->values();

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
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

        if (str_starts_with($p, '+62')) {
            $p = '0' . substr($p, 3);
        }
        if (str_starts_with($p, '62')) {
            $p = '0' . substr($p, 2);
        }
        return $p;
    }
}
