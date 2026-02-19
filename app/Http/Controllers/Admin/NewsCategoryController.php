<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsCategoryController extends Controller
{
    public function index()
    {
        return view('admin.news_categories.index');
    }

    public function data(Request $request)
    {
        $baseQuery = NewsCategory::query();
        $recordsTotal = (clone $baseQuery)->count();

        $search = $request->input('search');
        if (is_array($search)) {
            $search = $search['value'] ?? null;
        }
        if ($search) {
            $s = trim($search);
            $baseQuery->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('slug', 'like', "%{$s}%");
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $columns = [
            0 => 'name',
            1 => 'slug',
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

        $categories = $baseQuery
            ->orderBy($orderColumn, $orderDir)
            ->skip($start)
            ->take($length)
            ->get();

        $data = $categories->map(function (NewsCategory $category) {
            $editUrl = route('admin.news-categories.edit', $category);
            $deleteUrl = route('admin.news-categories.destroy', $category);
            $status = $category->is_active ? 'Aktif' : 'Nonaktif';

            return [
                'name' => e($category->name),
                'slug' => e($category->slug),
                'status' => '<span class="badge ' . ($category->is_active ? 'bg-success' : 'bg-secondary') . '">' . $status . '</span>',
                'created_at' => optional($category->created_at)->format('Y-m-d'),
                'actions' => '<a class="btn btn-sm btn-outline-light me-1" href="' . $editUrl . '">Edit</a>'
                    . '<form method="POST" action="' . $deleteUrl . '" class="d-inline" onsubmit="return confirm(\'Hapus kategori ini?\')">'
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
        return view('admin.news_categories.form', [
            'category' => new NewsCategory(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $slugBase = Str::slug($data['name']);
        $data['slug'] = $this->uniqueSlug($slugBase, null);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        NewsCategory::create($data);

        return redirect()
            ->route('admin.news-categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(NewsCategory $newsCategory)
    {
        return view('admin.news_categories.form', [
            'category' => $newsCategory,
        ]);
    }

    public function update(Request $request, NewsCategory $newsCategory)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $slugBase = Str::slug($data['name']);
        $data['slug'] = $this->uniqueSlug($slugBase, $newsCategory->id);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $newsCategory->update($data);

        return redirect()
            ->route('admin.news-categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(NewsCategory $newsCategory)
    {
        $newsCategory->delete();

        return redirect()
            ->route('admin.news-categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }

    private function uniqueSlug(string $base, ?int $ignoreId): string
    {
        $slug = $base ?: Str::random(8);
        $counter = 1;

        while (NewsCategory::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
