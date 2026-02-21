<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicYearController extends Controller
{
    public function index()
    {
        return view('admin.academic_years.index');
    }

    public function data(Request $request)
    {
        $baseQuery = AcademicYear::query();
        $recordsTotal = (clone $baseQuery)->count();

        $search = $request->input('search');
        if (is_array($search)) {
            $search = $search['value'] ?? null;
        }
        if ($search) {
            $s = trim($search);
            $baseQuery->where('name', 'like', "%{$s}%");
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $columns = [
            0 => 'name',
            1 => 'start_date',
            2 => 'is_active',
            3 => 'created_at',
        ];
        $orderColumn = $columns[$request->input('order.0.column')] ?? 'created_at';
        $orderDir = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 15);
        if ($length <= 0) {
            $length = 15;
        }

        $academicYears = $baseQuery
            ->orderBy($orderColumn, $orderDir)
            ->skip($start)
            ->take($length)
            ->get();

        $data = $academicYears->map(function (AcademicYear $year) {
            $toggleUrl = route('admin.academic-years.toggle', $year);
            $editUrl = route('admin.academic-years.edit', $year);
            $deleteUrl = route('admin.academic-years.destroy', $year);

            $start = optional($year->start_date)->format('d M Y');
            $end = optional($year->end_date)->format('d M Y');
            $range = ($start || $end) ? ($start ?: '-') . ' - ' . ($end ?: '-') : '-';

            $status = $year->is_active ? 'Aktif' : 'Nonaktif';

            return [
                'name' => e($year->name),
                'range' => e($range),
                'status' => '<div class="d-flex align-items-center gap-2">'
                    . '<span class="badge ' . ($year->is_active ? 'bg-success' : 'bg-secondary') . '">' . $status . '</span>'
                    . '<form method="POST" action="' . $toggleUrl . '">'
                    . csrf_field() . method_field('PATCH')
                    . '<div class="form-check form-switch m-0">'
                    . '<input class="form-check-input js-toggle-active" type="checkbox" role="switch" ' . ($year->is_active ? 'checked' : '') . '>'
                    . '</div></form></div>',
                'created_at' => optional($year->created_at)->format('Y-m-d'),
                'actions' => '<a class="btn btn-sm btn-outline-light me-1" href="' . $editUrl . '">Edit</a>'
                    . '<form method="POST" action="' . $deleteUrl . '" class="d-inline" onsubmit="return confirm(\'Hapus tahun ajaran ini?\')">'
                    . csrf_field() . method_field('DELETE')
                    . '<button class="btn btn-sm btn-outline-danger">Hapus</button></form>',
            ];
        })->values();

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function create()
    {
        return view('admin.academic_years.form', [
            'academicYear' => new AcademicYear(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        DB::transaction(function () use ($data) {
            if ($data['is_active']) {
                AcademicYear::query()->update(['is_active' => false]);
            }
            AcademicYear::create($data);
        });

        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic_years.form', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        DB::transaction(function () use ($data, $academicYear) {
            if ($data['is_active']) {
                AcademicYear::query()
                    ->where('id', '!=', $academicYear->id)
                    ->update(['is_active' => false]);
            }
            $academicYear->update($data);
        });

        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();

        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', 'Tahun ajaran berhasil dihapus.');
    }

    public function toggle(AcademicYear $academicYear)
    {
        DB::transaction(function () use ($academicYear) {
            $nextState = !$academicYear->is_active;

            if ($nextState) {
                AcademicYear::query()
                    ->where('id', '!=', $academicYear->id)
                    ->update(['is_active' => false]);
            }

            $academicYear->update(['is_active' => $nextState]);
        });

        return back()->with('success', 'Status tahun ajaran diperbarui.');
    }
}
