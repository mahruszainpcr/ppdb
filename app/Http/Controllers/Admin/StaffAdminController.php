<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffAdminController extends Controller
{
    public function index()
    {
        return view('admin.staff.index');
    }

    public function data(Request $request)
    {
        $baseQuery = User::query()->whereIn('role', ['admin', 'ustadz']);
        $recordsTotal = (clone $baseQuery)->count();

        $search = $request->input('search');
        if (is_array($search)) {
            $search = $search['value'] ?? null;
        }
        if ($search) {
            $s = trim($search);
            $baseQuery->where(function ($qq) use ($s) {
                $qq->where('name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $roleFilter = $request->input('role');
        if ($roleFilter && in_array($roleFilter, ['admin', 'ustadz'], true)) {
            $baseQuery->where('role', $roleFilter);
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $columns = [
            0 => 'name',
            1 => 'role',
            2 => 'phone',
            3 => 'email',
            4 => 'created_at',
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
            $email = e($u->email ?? '-');
            $role = e($u->role ?? '-');

            $actions = '<button class="btn btn-sm btn-outline-primary btn-edit-user" data-id="' . $u->id .
                '" data-name="' . $name . '" data-phone="' . $phone . '" data-email="' . $email .
                '" data-role="' . $role . '" data-bs-toggle="modal" data-bs-target="#editUserModal">Edit</button> ';
            $actions .= '<button class="btn btn-sm btn-outline-warning btn-reset-user" data-id="' . $u->id .
                '" data-phone="' . $phone . '" data-bs-toggle="modal" data-bs-target="#resetUserModal">Reset Password</button> ';
            $actions .= '<button class="btn btn-sm btn-outline-danger btn-delete-user" data-id="' . $u->id .
                '" data-name="' . $name . '" data-bs-toggle="modal" data-bs-target="#deleteUserModal">Hapus</button>';

            return [
                'name' => $name,
                'role' => $role === 'admin' ? 'Admin' : 'Ustadz',
                'phone' => $phone,
                'email' => $email,
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'role' => ['required', 'in:admin,ustadz'],
        ]);

        $data['phone'] = $this->normalizePhone($data['phone']);

        if (User::where('phone', $data['phone'])->exists()) {
            return back()->withErrors(['phone' => 'Nomor HP sudah digunakan user lain.']);
        }
        if (!empty($data['email']) && User::where('email', $data['email'])->exists()) {
            return back()->withErrors(['email' => 'Email sudah digunakan user lain.']);
        }

        $password = $data['phone'];

        User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'role' => $data['role'],
            'password' => Hash::make($password),
        ]);

        return back()->with('success', 'Akun berhasil ditambahkan. Password awal = nomor HP.');
    }

    public function update(Request $request, User $user)
    {
        if (!in_array($user->role, ['admin', 'ustadz'], true)) {
            return back()->withErrors(['user' => 'User ini bukan akun admin/ustadz.']);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'role' => ['required', 'in:admin,ustadz'],
        ]);

        $data['phone'] = $this->normalizePhone($data['phone']);

        if (User::where('phone', $data['phone'])->where('id', '!=', $user->id)->exists()) {
            return back()->withErrors(['phone' => 'Nomor HP sudah digunakan user lain.']);
        }
        if (!empty($data['email']) && User::where('email', $data['email'])->where('id', '!=', $user->id)->exists()) {
            return back()->withErrors(['email' => 'Email sudah digunakan user lain.']);
        }

        if ($request->user()->id === $user->id && $data['role'] !== 'admin') {
            return back()->withErrors(['role' => 'Tidak bisa mengubah role akun sendiri.']);
        }

        $user->update($data);

        return back()->with('success', 'Data akun berhasil diperbarui.');
    }

    public function resetPassword(User $user)
    {
        if (!in_array($user->role, ['admin', 'ustadz'], true) || !$user->phone) {
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

    public function destroy(Request $request, User $user)
    {
        if (!in_array($user->role, ['admin', 'ustadz'], true)) {
            return back()->withErrors(['delete' => 'User ini bukan akun admin/ustadz.']);
        }
        if ($request->user()->id === $user->id) {
            return back()->withErrors(['delete' => 'Tidak bisa menghapus akun sendiri.']);
        }

        $user->delete();

        return back()->with('success', 'Akun berhasil dihapus.');
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
