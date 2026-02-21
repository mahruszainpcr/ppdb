@extends('welcome')

@section('title', ($post->title ?? 'Berita') . ' - Ma\'had Darussalam Al-Islami')

@push('styles')
    <style>
        .post-media-img {
            width: 100%;
            border-radius: 14px;
            margin-bottom: 16px;
            display: block;
        }

        .post-media-embed {
            position: relative;
            padding-top: 56.25%;
            background: #000;
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 16px;
        }

        .post-media-embed iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .hero {
            background: linear-gradient(180deg, var(--bg2), var(--bg));
            color: #fff;
            min-height: 0;
            padding: 48px 0 16px;
        }

        .hero::after {
            display: none;
        }

        .content {
            background: #fff;
            margin-top: -20px;
            border-radius: var(--radius);
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
            padding: 22px;
        }

        .meta {
            color: rgba(255, 255, 255, .85);
            font-size: 12px;
            margin-top: 6px;
        }

        .content h1 {
            margin: 0 0 8px;
            font-size: clamp(22px, 4vw, 34px);
        }

        .back {
            display: inline-block;
            font-size: 12px;
            margin-top: 16px;
            color: var(--muted);
        }
    </style>
@endpush

@section('content')
    <x-landing-header />

    <header class="hero">
        <div class="container">
            <span class="pill">{{ $post->category->name ?? 'Berita' }}</span>
            <h1 style="margin:12px 0 6px;">{{ $post->title }}</h1>
            <div class="meta">
                {{ optional($post->published_at ?? $post->created_at)->format('d M Y H:i') }}
            </div>
        </div>
    </header>

    <main class="container">
        <div class="content">
            @if (($post->media_type ?? 'image') === 'instagram' && !empty($post->embed_url))
                <div class="post-media-embed">
                    <blockquote class="instagram-media"
                        data-instgrm-permalink="{{ $post->embed_url }}" data-instgrm-version="14"
                        style="background:#FFF; border:0; margin:0; padding:0; width:100%;"></blockquote>
                </div>
            @elseif (($post->media_type ?? 'image') !== 'image' && !empty($post->embed_url))
                <div class="post-media-embed">
                    <iframe src="{{ $post->embed_url }}" title="{{ $post->title }}" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                </div>
            @elseif ($post->thumbnail_url)
                <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" class="post-media-img">
            @endif

            {!! $post->content !!}

            <a class="back" href="{{ url('/') }}">â† Kembali ke beranda</a>
        </div>
    </main>

    <x-landing-footer />
@endsection

@if (($post->media_type ?? 'image') === 'instagram' && !empty($post->embed_url))
    @push('scripts')
        <script async src="https://www.instagram.com/embed.js"></script>
    @endpush
@endif
