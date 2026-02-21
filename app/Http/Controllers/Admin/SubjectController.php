<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index()
    {
        return view('admin.subjects.index');
    }

    public function data(Request $request)
    {
        $baseQuery = Subject::query();
        $recordsTotal = (clone $baseQuery)->count();

        $search = $request->input('search');
        if (is_array($search)) {
            $search = $search['value'] ?? null;
        }
        if ($search) {
            $s = trim($search);
            $baseQuery->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('code', 'like', "%{$s}%")
                    ->orWhere('type', 'like', "%{$s}%");
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $columns = [
            0 => 'name',
            1 => 'type',
            2 => 'code',
            3 => 'sort_order',
            4 => 'is_active',
            5 => 'created_at',
        ];
        $orderColumn = $columns[$request->input('order.0.column')] ?? 'sort_order';
        $orderDir = $request->input('order.0.dir') === 'desc' ? 'desc' : 'asc';

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 15);
        if ($length <= 0) {
            $length = 15;
        }

        $subjects = $baseQuery
            ->orderBy($orderColumn, $orderDir)
            ->skip($start)
            ->take($length)
            ->get();

        $data = $subjects->map(function (Subject $subject) {
            $toggleUrl = route('admin.subjects.toggle', $subject);
            $editUrl = route('admin.subjects.edit', $subject);
            $deleteUrl = route('admin.subjects.destroy', $subject);
            $status = $subject->is_active ? 'Aktif' : 'Nonaktif';

            return [
                'name' => e($subject->name),
                'type' => $subject->type === 'kitab' ? 'Kitab' : 'Mapel',
                'code' => e($subject->code ?: '-'),
                'sort_order' => $subject->sort_order,
                'status' => '<div class="d-flex align-items-center gap-2">'
                    . '<span class="badge ' . ($subject->is_active ? 'bg-success' : 'bg-secondary') . '">' . $status . '</span>'
                    . '<form method="POST" action="' . $toggleUrl . '">'
                    . csrf_field() . method_field('PATCH')
                    . '<div class="form-check form-switch m-0">'
                    . '<input class="form-check-input js-toggle-active" type="checkbox" role="switch" ' . ($subject->is_active ? 'checked' : '') . '>'
                    . '</div></form></div>',
                'created_at' => optional($subject->created_at)->format('Y-m-d'),
                'actions' => '<a class="btn btn-sm btn-outline-light me-1" href="' . $editUrl . '">Edit</a>'
                    . '<form method="POST" action="' . $deleteUrl . '" class="d-inline" onsubmit="return confirm(\'Hapus mapel/kitab ini?\')">'
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
        return view('admin.subjects.form', [
            'subject' => new Subject(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'type' => ['required', Rule::in(['mapel', 'kitab'])],
            'code' => ['nullable', 'string', 'max:30'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        Subject::create($data);

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Mapel/kitab berhasil ditambahkan.');
    }

    public function edit(Subject $subject)
    {
        return view('admin.subjects.form', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'type' => ['required', Rule::in(['mapel', 'kitab'])],
            'code' => ['nullable', 'string', 'max:30'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        $subject->update($data);

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Mapel/kitab berhasil diperbarui.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Mapel/kitab berhasil dihapus.');
    }

    public function toggle(Subject $subject)
    {
        $subject->update(['is_active' => !$subject->is_active]);

        return back()->with('success', 'Status mapel/kitab diperbarui.');
    }
}
