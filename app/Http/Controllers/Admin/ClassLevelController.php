<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassLevel;
use Illuminate\Http\Request;

class ClassLevelController extends Controller
{
    public function index()
    {
        return view('admin.class_levels.index');
    }

    public function data(Request $request)
    {
        $baseQuery = ClassLevel::query();
        $recordsTotal = (clone $baseQuery)->count();

        $search = $request->input('search');
        if (is_array($search)) {
            $search = $search['value'] ?? null;
        }
        if ($search) {
            $s = trim($search);
            $baseQuery->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('code', 'like', "%{$s}%");
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $columns = [
            0 => 'name',
            1 => 'code',
            2 => 'sort_order',
            3 => 'is_active',
            4 => 'created_at',
        ];
        $orderColumn = $columns[$request->input('order.0.column')] ?? 'sort_order';
        $orderDir = $request->input('order.0.dir') === 'desc' ? 'desc' : 'asc';

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 15);
        if ($length <= 0) {
            $length = 15;
        }

        $classLevels = $baseQuery
            ->orderBy($orderColumn, $orderDir)
            ->skip($start)
            ->take($length)
            ->get();

        $data = $classLevels->map(function (ClassLevel $level) {
            $toggleUrl = route('admin.class-levels.toggle', $level);
            $editUrl = route('admin.class-levels.edit', $level);
            $deleteUrl = route('admin.class-levels.destroy', $level);
            $status = $level->is_active ? 'Aktif' : 'Nonaktif';

            return [
                'name' => e($level->name),
                'code' => e($level->code ?: '-'),
                'sort_order' => $level->sort_order,
                'status' => '<div class="d-flex align-items-center gap-2">'
                    . '<span class="badge ' . ($level->is_active ? 'bg-success' : 'bg-secondary') . '">' . $status . '</span>'
                    . '<form method="POST" action="' . $toggleUrl . '">'
                    . csrf_field() . method_field('PATCH')
                    . '<div class="form-check form-switch m-0">'
                    . '<input class="form-check-input js-toggle-active" type="checkbox" role="switch" ' . ($level->is_active ? 'checked' : '') . '>'
                    . '</div></form></div>',
                'created_at' => optional($level->created_at)->format('Y-m-d'),
                'actions' => '<a class="btn btn-sm btn-outline-light me-1" href="' . $editUrl . '">Edit</a>'
                    . '<form method="POST" action="' . $deleteUrl . '" class="d-inline" onsubmit="return confirm(\'Hapus kelas/tingkatan ini?\')">'
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
        return view('admin.class_levels.form', [
            'classLevel' => new ClassLevel(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:30'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        ClassLevel::create($data);

        return redirect()
            ->route('admin.class-levels.index')
            ->with('success', 'Kelas/tingkatan berhasil ditambahkan.');
    }

    public function edit(ClassLevel $classLevel)
    {
        return view('admin.class_levels.form', compact('classLevel'));
    }

    public function update(Request $request, ClassLevel $classLevel)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:30'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        $classLevel->update($data);

        return redirect()
            ->route('admin.class-levels.index')
            ->with('success', 'Kelas/tingkatan berhasil diperbarui.');
    }

    public function destroy(ClassLevel $classLevel)
    {
        $classLevel->delete();

        return redirect()
            ->route('admin.class-levels.index')
            ->with('success', 'Kelas/tingkatan berhasil dihapus.');
    }

    public function toggle(ClassLevel $classLevel)
    {
        $classLevel->update(['is_active' => !$classLevel->is_active]);

        return back()->with('success', 'Status kelas/tingkatan diperbarui.');
    }
}
