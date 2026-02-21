<?php

namespace App\Http\Controllers;

use App\Models\NewsPost;

class LandingController extends Controller
{
    public function index()
    {
        $newsPosts = NewsPost::query()
            ->with('category')
            ->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        if ($newsPosts->isEmpty()) {
            $newsPosts = NewsPost::query()
                ->with('category')
                ->orderByDesc('created_at')
                ->limit(8)
                ->get();
        }

        return view('welcome', compact('newsPosts'));
    }
}
