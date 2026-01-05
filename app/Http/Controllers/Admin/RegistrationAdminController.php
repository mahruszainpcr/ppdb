<?php

// app/Http/Controllers/Admin/RegistrationAdminController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegistrationAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = Registration::query()
            ->with(['user', 'studentProfile', 'period'])
            ->latest();

        // search (nama santri / no pendaftaran / no WA)
        if ($request->filled('search')) {
            $s = trim($request->search);
            $q->where('registration_no', 'like', "%{$s}%")
                ->orWhereHas('studentProfile', fn($qq) => $qq->where('full_name', 'like', "%{$s}%"))
                ->orWhereHas('user', fn($qq) => $qq->where('phone', 'like', "%{$s}%"));
        }

        // filter gelombang/periode
        if ($request->filled('period_id')) {
            $q->where('period_id', $request->period_id);
        }

        // filter status
        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        // filter kelulusan
        if ($request->filled('graduation_status')) {
            $q->where('graduation_status', $request->graduation_status);
        }

        $registrations = $q->paginate(15)->withQueryString();

        return view('admin.registrations.index', compact('registrations'));
    }

    public function show(Registration $registration)
    {
        $registration->load([
            'user',
            'period',
            'studentProfile',
            'parentProfile',
            'statement',
            'documents' => fn($q) => $q->orderBy('type'),
        ]);

        return view('admin.registrations.show', compact('registration'));
    }
    public function setGraduation(Request $request, Registration $registration)
    {
        $data = $request->validate([
            'graduation_status' => ['required', Rule::in(['pending', 'lulus', 'tidak_lulus', 'cadangan'])],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $registration->update([
            'graduation_status' => $data['graduation_status'],
            'admin_note' => $data['admin_note'] ?? null,
        ]);

        // (opsional) jika mau otomatis update status dokumen/verifikasi
        // if ($data['graduation_status'] !== 'pending') {
        //     $registration->update(['status' => 'verified']);
        // }

        return back()->with('success', 'Kelulusan berhasil diperbarui.');
    }
}

