<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassLevel;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeachingAssignmentController extends Controller
{
    public function index()
    {
        return view('admin.teaching_assignments.index');
    }

    public function data(Request $request)
    {
        $baseQuery = TeachingAssignment::query()->with([
            'ustadz:id,name',
            'subject:id,name,type',
            'classLevel:id,name',
            'semester:id,name',
            'academicYear:id,name',
        ]);

        $recordsTotal = (clone $baseQuery)->count();

        $search = $request->input('search');
        if (is_array($search)) {
            $search = $search['value'] ?? null;
        }
        if ($search) {
            $s = trim($search);
            $baseQuery->where(function ($q) use ($s) {
                $q->whereHas('ustadz', fn($q2) => $q2->where('name', 'like', "%{$s}%"))
                    ->orWhereHas('subject', fn($q2) => $q2->where('name', 'like', "%{$s}%"))
                    ->orWhereHas('classLevel', fn($q2) => $q2->where('name', 'like', "%{$s}%"))
                    ->orWhereHas('semester', fn($q2) => $q2->where('name', 'like', "%{$s}%"))
                    ->orWhereHas('academicYear', fn($q2) => $q2->where('name', 'like', "%{$s}%"));
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $columns = [
            0 => 'created_at',
        ];
        $orderColumn = $columns[$request->input('order.0.column')] ?? 'created_at';
        $orderDir = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 15);
        if ($length <= 0) {
            $length = 15;
        }

        $assignments = $baseQuery
            ->orderBy($orderColumn, $orderDir)
            ->skip($start)
            ->take($length)
            ->get();

        $data = $assignments->map(function (TeachingAssignment $ta) {
            $toggleUrl = route('admin.teaching-assignments.toggle', $ta);
            $editUrl = route('admin.teaching-assignments.edit', $ta);
            $deleteUrl = route('admin.teaching-assignments.destroy', $ta);
            $status = $ta->is_active ? 'Aktif' : 'Nonaktif';

            return [
                'ustadz' => e($ta->ustadz?->name ?? '-'),
                'subject' => e($ta->subject?->name ?? '-'),
                'class_level' => e($ta->classLevel?->name ?? '-'),
                'semester' => e($ta->semester?->name ?? '-'),
                'academic_year' => e($ta->academicYear?->name ?? '-'),
                'status' => '<div class="d-flex align-items-center gap-2">'
                    . '<span class="badge ' . ($ta->is_active ? 'bg-success' : 'bg-secondary') . '">' . $status . '</span>'
                    . '<form method="POST" action="' . $toggleUrl . '">'
                    . csrf_field() . method_field('PATCH')
                    . '<div class="form-check form-switch m-0">'
                    . '<input class="form-check-input js-toggle-active" type="checkbox" role="switch" ' . ($ta->is_active ? 'checked' : '') . '>'
                    . '</div></form></div>',
                'created_at' => optional($ta->created_at)->format('Y-m-d'),
                'actions' => '<a class="btn btn-sm btn-outline-light me-1" href="' . $editUrl . '">Edit</a>'
                    . '<form method="POST" action="' . $deleteUrl . '" class="d-inline" onsubmit="return confirm(\'Hapus ustadz pengampu ini?\')">'
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
        return view('admin.teaching_assignments.form', [
            'assignment' => new TeachingAssignment(),
            'ustadzList' => User::ustadz()->orderBy('name')->get(['id', 'name']),
            'subjects' => Subject::query()->orderBy('name')->get(['id', 'name']),
            'classLevels' => ClassLevel::query()->orderBy('sort_order')->orderBy('name')->get(['id', 'name']),
            'semesters' => Semester::query()->orderBy('sort_order')->orderBy('name')->get(['id', 'name']),
            'academicYears' => AcademicYear::query()->orderByDesc('is_active')->orderByDesc('start_date')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'class_level_id' => ['required', 'exists:class_levels,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $request->validate([
            'user_id' => [
                Rule::unique('teaching_assignments')->where(function ($q) use ($data) {
                    return $q->where('subject_id', $data['subject_id'])
                        ->where('class_level_id', $data['class_level_id'])
                        ->where('semester_id', $data['semester_id'])
                        ->where('academic_year_id', $data['academic_year_id']);
                }),
            ],
        ], [
            'user_id.unique' => 'Ustadz pengampu dengan kombinasi mapel/kelas/semester/tahun ajaran tersebut sudah ada.',
        ]);

        TeachingAssignment::create($data);

        return redirect()
            ->route('admin.teaching-assignments.index')
            ->with('success', 'Ustadz pengampu berhasil ditambahkan.');
    }

    public function edit(TeachingAssignment $teachingAssignment)
    {
        return view('admin.teaching_assignments.form', [
            'assignment' => $teachingAssignment,
            'ustadzList' => User::ustadz()->orderBy('name')->get(['id', 'name']),
            'subjects' => Subject::query()->orderBy('name')->get(['id', 'name']),
            'classLevels' => ClassLevel::query()->orderBy('sort_order')->orderBy('name')->get(['id', 'name']),
            'semesters' => Semester::query()->orderBy('sort_order')->orderBy('name')->get(['id', 'name']),
            'academicYears' => AcademicYear::query()->orderByDesc('is_active')->orderByDesc('start_date')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, TeachingAssignment $teachingAssignment)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'class_level_id' => ['required', 'exists:class_levels,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $request->validate([
            'user_id' => [
                Rule::unique('teaching_assignments')->where(function ($q) use ($data, $teachingAssignment) {
                    return $q->where('subject_id', $data['subject_id'])
                        ->where('class_level_id', $data['class_level_id'])
                        ->where('semester_id', $data['semester_id'])
                        ->where('academic_year_id', $data['academic_year_id'])
                        ->where('id', '!=', $teachingAssignment->id);
                }),
            ],
        ], [
            'user_id.unique' => 'Ustadz pengampu dengan kombinasi mapel/kelas/semester/tahun ajaran tersebut sudah ada.',
        ]);

        $teachingAssignment->update($data);

        return redirect()
            ->route('admin.teaching-assignments.index')
            ->with('success', 'Ustadz pengampu berhasil diperbarui.');
    }

    public function destroy(TeachingAssignment $teachingAssignment)
    {
        $teachingAssignment->delete();

        return redirect()
            ->route('admin.teaching-assignments.index')
            ->with('success', 'Ustadz pengampu berhasil dihapus.');
    }

    public function toggle(TeachingAssignment $teachingAssignment)
    {
        $teachingAssignment->update(['is_active' => !$teachingAssignment->is_active]);

        return back()->with('success', 'Status ustadz pengampu diperbarui.');
    }
}
