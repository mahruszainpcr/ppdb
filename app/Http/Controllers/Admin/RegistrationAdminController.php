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
        return view('admin.registrations.index');
    }

    public function data(Request $request)
    {
        $baseQuery = Registration::query()->with(['user', 'studentProfile']);
        $recordsTotal = (clone $baseQuery)->count();

        $search = $request->input('search');
        if (is_array($search)) {
            $search = $search['value'] ?? null;
        }
        if ($search) {
            $s = trim($search);
            $baseQuery->where(function ($q) use ($s) {
                $q->where('registration_no', 'like', "%{$s}%")
                    ->orWhereHas('studentProfile', fn($qq) => $qq->where('full_name', 'like', "%{$s}%"))
                    ->orWhereHas('user', fn($qq) => $qq->where('phone', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('period_id')) {
            $baseQuery->where('period_id', $request->period_id);
        }
        if ($request->filled('status')) {
            $baseQuery->where('status', $request->status);
        }
        if ($request->filled('graduation_status')) {
            $baseQuery->where('graduation_status', $request->graduation_status);
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $columns = [
            0 => 'registration_no',
            3 => 'education_level',
            4 => 'status',
            5 => 'graduation_status',
        ];
        $orderColumn = $columns[$request->input('order.0.column')] ?? 'created_at';
        $orderDir = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 15);
        if ($length <= 0) {
            $length = 15;
        }

        $registrations = $baseQuery
            ->orderBy($orderColumn, $orderDir)
            ->skip($start)
            ->take($length)
            ->get();

        $data = $registrations->map(function (Registration $r) {
            $studentName = e(optional($r->studentProfile)->full_name ?? '-');
            $phone = e($r->user->phone ?? '-');
            $status = e($r->status ?? '-');
            $graduation = e($r->graduation_status ?? '-');
            $detailUrl = route('admin.registrations.show', $r);

            return [
                'registration_no' => e($r->registration_no ?? '-'),
                'student_name' => $studentName,
                'phone' => $phone,
                'education_level' => e($r->education_level ?? '-'),
                'status' => '<span class="badge bg-secondary">' . $status . '</span>',
                'graduation_status' => '<span class="badge bg-info">' . $graduation . '</span>',
                'actions' => '<a class="btn btn-sm btn-outline-light" href="' . $detailUrl . '">Detail</a>',
            ];
        })->values();

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
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
