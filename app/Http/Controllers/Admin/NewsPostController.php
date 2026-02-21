<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsCategory;
use App\Models\NewsPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsPostController extends Controller
{
    public function index()
    {
        $categories = NewsCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.news_posts.index', compact('categories'));
    }

    public function data(Request $request)
    {
        $baseQuery = NewsPost::query()->with(['category', 'author']);
        $recordsTotal = (clone $baseQuery)->count();

        $search = $request->input('search');
        if (is_array($search)) {
            $search = $search['value'] ?? null;
        }
        if ($search) {
            $s = trim($search);
            $baseQuery->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('slug', 'like', "%{$s}%")
                    ->orWhereHas('category', fn($qq) => $qq->where('name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('category_id')) {
            $baseQuery->where('news_category_id', $request->category_id);
        }
        if ($request->filled('status')) {
            $baseQuery->where('is_published', $request->status === 'published');
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $columns = [
            0 => 'title',
            1 => 'news_category_id',
            2 => 'is_published',
            3 => 'published_at',
            4 => 'created_at',
        ];
        $orderColumn = $columns[$request->input('order.0.column')] ?? 'created_at';
        $orderDir = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 15);
        if ($length <= 0) {
            $length = 15;
        }

        $posts = $baseQuery
            ->orderBy($orderColumn, $orderDir)
            ->skip($start)
            ->take($length)
            ->get();

        $data = $posts->map(function (NewsPost $post) {
            $editUrl = route('admin.news-posts.edit', $post);
            $deleteUrl = route('admin.news-posts.destroy', $post);
            $status = $post->is_published ? 'Publish' : 'Draft';
            $dateLabel = $post->published_at ? $post->published_at->format('Y-m-d H:i') : '-';

            return [
                'title' => e($post->title),
                'category' => e(optional($post->category)->name ?? '-'),
                'status' => '<span class="badge ' . ($post->is_published ? 'bg-success' : 'bg-secondary') . '">' . $status . '</span>',
                'published_at' => e($dateLabel),
                'author' => e(optional($post->author)->name ?? '-'),
                'actions' => '<a class="btn btn-sm btn-outline-light me-1" href="' . $editUrl . '">Edit</a>'
                    . '<form method="POST" action="' . $deleteUrl . '" class="d-inline" onsubmit="return confirm(\'Hapus berita ini?\')">'
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
        $categories = NewsCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.news_posts.form', [
            'post' => new NewsPost(),
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'news_category_id' => ['required', 'exists:news_categories,id'],
            'title' => ['required', 'string', 'max:180'],
            'excerpt' => ['nullable', 'string', 'max:300'],
            'content' => ['required', 'string'],
            'media_type' => ['required', 'in:image,youtube,instagram'],
            'embed_url' => ['nullable', 'url', 'max:500', 'required_if:media_type,youtube,instagram'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['slug'] = $this->uniqueSlug(Str::slug($data['title']), null);
        $data['is_published'] = (bool) ($data['is_published'] ?? false);
        $data['created_by'] = $request->user()->id ?? null;

        if (!empty($data['published_at'])) {
            $data['published_at'] = \Carbon\Carbon::parse($data['published_at']);
        } elseif ($data['is_published']) {
            $data['published_at'] = now();
        }

        if ($data['media_type'] === 'image') {
            $data['embed_url'] = null;
        } elseif ($data['media_type'] === 'youtube' && !empty($data['embed_url'])) {
            $data['embed_url'] = $this->normalizeYoutubeEmbedUrl($data['embed_url']);
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('news', 'public');
        }

        NewsPost::create($data);

        return redirect()
            ->route('admin.news-posts.index')
            ->with('success', 'Berita berhasil ditambahkan.');
    }

    public function edit(NewsPost $newsPost)
    {
        $categories = NewsCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.news_posts.form', [
            'post' => $newsPost,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, NewsPost $newsPost)
    {
        $data = $request->validate([
            'news_category_id' => ['required', 'exists:news_categories,id'],
            'title' => ['required', 'string', 'max:180'],
            'excerpt' => ['nullable', 'string', 'max:300'],
            'content' => ['required', 'string'],
            'media_type' => ['required', 'in:image,youtube,instagram'],
            'embed_url' => ['nullable', 'url', 'max:500', 'required_if:media_type,youtube,instagram'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'remove_thumbnail' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = $this->uniqueSlug(Str::slug($data['title']), $newsPost->id);
        $data['is_published'] = (bool) ($data['is_published'] ?? false);

        if (!empty($data['published_at'])) {
            $data['published_at'] = \Carbon\Carbon::parse($data['published_at']);
        } elseif ($data['is_published'] && !$newsPost->published_at) {
            $data['published_at'] = now();
        } elseif (!$data['is_published']) {
            $data['published_at'] = null;
        }

        if ($data['media_type'] === 'image') {
            $data['embed_url'] = null;
        } elseif ($data['media_type'] === 'youtube' && !empty($data['embed_url'])) {
            $data['embed_url'] = $this->normalizeYoutubeEmbedUrl($data['embed_url']);
        }

        if ($request->boolean('remove_thumbnail') && $newsPost->thumbnail_path) {
            Storage::disk('public')->delete($newsPost->thumbnail_path);
            $data['thumbnail_path'] = null;
        }

        if ($request->hasFile('thumbnail')) {
            if ($newsPost->thumbnail_path) {
                Storage::disk('public')->delete($newsPost->thumbnail_path);
            }
            $data['thumbnail_path'] = $request->file('thumbnail')->store('news', 'public');
        }

        $newsPost->update($data);

        return redirect()
            ->route('admin.news-posts.index')
            ->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(NewsPost $newsPost)
    {
        if ($newsPost->thumbnail_path) {
            Storage::disk('public')->delete($newsPost->thumbnail_path);
        }

        $newsPost->delete();

        return redirect()
            ->route('admin.news-posts.index')
            ->with('success', 'Berita berhasil dihapus.');
    }

    private function uniqueSlug(string $base, ?int $ignoreId): string
    {
        $slug = $base ?: Str::random(8);
        $counter = 1;

        while (NewsPost::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function normalizeYoutubeEmbedUrl(string $url): string
    {
        $url = trim($url);
        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
        $path = parse_url($url, PHP_URL_PATH) ?? '';
        $query = parse_url($url, PHP_URL_QUERY) ?? '';

        parse_str($query, $queryParams);
        $videoId = null;

        if (str_contains($host, 'youtu.be')) {
            $videoId = ltrim($path, '/');
        } elseif (str_contains($host, 'youtube.com')) {
            if (str_starts_with($path, '/watch')) {
                $videoId = $queryParams['v'] ?? null;
            } elseif (str_starts_with($path, '/shorts/')) {
                $videoId = trim(str_replace('/shorts/', '', $path), '/');
            } elseif (str_starts_with($path, '/embed/')) {
                $videoId = trim(str_replace('/embed/', '', $path), '/');
            } elseif (str_starts_with($path, '/live/')) {
                $videoId = trim(str_replace('/live/', '', $path), '/');
            }
        }

        if (!$videoId) {
            return $url;
        }

        return 'https://www.youtube.com/embed/' . $videoId;
    }
}
