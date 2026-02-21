<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassLevel;
use Illuminate\Http\Request;

class ClassLevelController extends Controller
{
    public function index()
    {
        $classLevels = ClassLevel::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.class_levels.index', compact('classLevels'));
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
