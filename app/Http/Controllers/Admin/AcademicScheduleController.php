<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSchedule;
use App\Models\AcademicYear;
use App\Models\ClassLevel;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcademicScheduleController extends Controller
{
    public function index()
    {
        return view('admin.academic_schedules.index');
    }

    public function data(Request $request)
    {
        $baseQuery = AcademicSchedule::query()->with([
            'academicYear:id,name',
            'semester:id,name',
            'classLevel:id,name',
            'subject:id,name',
            'teacher:id,name',
        ]);

        $recordsTotal = (clone $baseQuery)->count();

        $search = $request->input('search');
        if (is_array($search)) {
            $search = $search['value'] ?? null;
        }
        if ($search) {
            $s = trim($search);
            $baseQuery->where(function ($q) use ($s) {
                $q->whereHas('teacher', fn($q2) => $q2->where('name', 'like', "%{$s}%"))
                    ->orWhereHas('subject', fn($q2) => $q2->where('name', 'like', "%{$s}%"))
                    ->orWhereHas('classLevel', fn($q2) => $q2->where('name', 'like', "%{$s}%"))
                    ->orWhereHas('semester', fn($q2) => $q2->where('name', 'like', "%{$s}%"))
                    ->orWhereHas('academicYear', fn($q2) => $q2->where('name', 'like', "%{$s}%"))
                    ->orWhere('room', 'like', "%{$s}%");
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $columns = [
            0 => 'day_of_week',
            1 => 'start_time',
            2 => 'created_at',
        ];
        $orderColumn = $columns[$request->input('order.0.column')] ?? 'created_at';
        $orderDir = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 15);
        if ($length <= 0) {
            $length = 15;
        }

        $schedules = $baseQuery
            ->orderBy($orderColumn, $orderDir)
            ->skip($start)
            ->take($length)
            ->get();

        $dayMap = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Ahad',
        ];

        $data = $schedules->map(function (AcademicSchedule $schedule) use ($dayMap) {
            $toggleUrl = route('admin.academic-schedules.toggle', $schedule);
            $editUrl = route('admin.academic-schedules.edit', $schedule);
            $deleteUrl = route('admin.academic-schedules.destroy', $schedule);
            $status = $schedule->is_active ? 'Aktif' : 'Nonaktif';

            $time = substr($schedule->start_time, 0, 5) . ' - ' . substr($schedule->end_time, 0, 5);
            $dayLabel = $dayMap[$schedule->day_of_week] ?? '-';

            return [
                'day' => e($dayLabel),
                'time' => e($time),
                'class_level' => e($schedule->classLevel?->name ?? '-'),
                'subject' => e($schedule->subject?->name ?? '-'),
                'teacher' => e($schedule->teacher?->name ?? '-'),
                'semester' => e($schedule->semester?->name ?? '-'),
                'academic_year' => e($schedule->academicYear?->name ?? '-'),
                'room' => e($schedule->room ?: '-'),
                'status' => '<div class="d-flex align-items-center gap-2">'
                    . '<span class="badge ' . ($schedule->is_active ? 'bg-success' : 'bg-secondary') . '">' . $status . '</span>'
                    . '<form method="POST" action="' . $toggleUrl . '">'
                    . csrf_field() . method_field('PATCH')
                    . '<div class="form-check form-switch m-0">'
                    . '<input class="form-check-input js-toggle-active" type="checkbox" role="switch" ' . ($schedule->is_active ? 'checked' : '') . '>'
                    . '</div></form></div>',
                'created_at' => optional($schedule->created_at)->format('Y-m-d'),
                'actions' => '<a class="btn btn-sm btn-outline-light me-1" href="' . $editUrl . '">Edit</a>'
                    . '<form method="POST" action="' . $deleteUrl . '" class="d-inline" onsubmit="return confirm(\'Hapus jadwal ini?\')">'
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
        return view('admin.academic_schedules.form', $this->formOptions(new AcademicSchedule()));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        AcademicSchedule::create($data);

        return redirect()
            ->route('admin.academic-schedules.index')
            ->with('success', 'Jadwal akademik berhasil ditambahkan.');
    }

    public function edit(AcademicSchedule $academicSchedule)
    {
        return view('admin.academic_schedules.form', $this->formOptions($academicSchedule));
    }

    public function update(Request $request, AcademicSchedule $academicSchedule)
    {
        $data = $this->validateData($request);
        $academicSchedule->update($data);

        return redirect()
            ->route('admin.academic-schedules.index')
            ->with('success', 'Jadwal akademik berhasil diperbarui.');
    }

    public function destroy(AcademicSchedule $academicSchedule)
    {
        $academicSchedule->delete();

        return redirect()
            ->route('admin.academic-schedules.index')
            ->with('success', 'Jadwal akademik berhasil dihapus.');
    }

    public function toggle(AcademicSchedule $academicSchedule)
    {
        $academicSchedule->update(['is_active' => !$academicSchedule->is_active]);

        return back()->with('success', 'Status jadwal akademik diperbarui.');
    }

    private function formOptions(AcademicSchedule $assignment): array
    {
        return [
            'schedule' => $assignment,
            'ustadzList' => User::ustadz()->orderBy('name')->get(['id', 'name']),
            'subjects' => Subject::query()->orderBy('name')->get(['id', 'name']),
            'classLevels' => ClassLevel::query()->orderBy('sort_order')->orderBy('name')->get(['id', 'name']),
            'semesters' => Semester::query()->orderBy('sort_order')->orderBy('name')->get(['id', 'name']),
            'academicYears' => AcademicYear::query()->orderByDesc('is_active')->orderByDesc('start_date')->get(['id', 'name']),
            'dayMap' => [
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu',
                7 => 'Ahad',
            ],
        ];
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
            'class_level_id' => ['required', 'exists:class_levels,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'teacher_id' => ['required', 'exists:users,id'],
            'day_of_week' => ['required', Rule::in([1, 2, 3, 4, 5, 6, 7])],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string', 'max:60'],
            'note' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        return $data;
    }
}
