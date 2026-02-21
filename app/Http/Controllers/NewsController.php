<?php

namespace App\Http\Controllers;

use App\Models\NewsCategory;
use App\Models\NewsPost;

class NewsController extends Controller
{
    public function index()
    {
        $baseQuery = NewsPost::query()
            ->with('category')
            ->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->orderByDesc('published_at')
            ->orderByDesc('created_at');

        $videoPosts = (clone $baseQuery)
            ->where('media_type', 'youtube')
            ->take(6)
            ->get();

        $instagramPosts = (clone $baseQuery)
            ->where('media_type', 'instagram')
            ->take(6)
            ->get();

        $categoryGroups = NewsCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function (NewsCategory $category) use ($baseQuery) {
                $posts = (clone $baseQuery)
                    ->where('news_category_id', $category->id)
                    ->where('media_type', 'image')
                    ->take(6)
                    ->get();

                return (object) [
                    'category' => $category,
                    'posts' => $posts,
                ];
            })
            ->filter(fn($group) => $group->posts->isNotEmpty())
            ->values();

        return view('public.news.index', compact('videoPosts', 'instagramPosts', 'categoryGroups'));
    }

    public function show(string $slug)
    {
        $post = NewsPost::query()
            ->with('category')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.news.show', compact('post'));
    }
}
