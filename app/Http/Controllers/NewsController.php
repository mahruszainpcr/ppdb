<?php

namespace App\Http\Controllers;

use App\Models\NewsPost;

class NewsController extends Controller
{
    public function show(string $slug)
    {
        $post = NewsPost::query()
            ->with('category')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.news.show', compact('post'));
    }
}
