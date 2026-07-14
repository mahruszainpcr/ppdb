@extends('welcome')

@section('title', ($post->title ?? 'Berita') . ' - Ma\'had Darussalam Al-Islami')

@php
    $shareDescription = \Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 160);
    $shareImage = $post->thumbnail_url ?? asset('assets/images/welcome.png');
@endphp

@php
    $metaKeywords = collect([
        $post->title,
        $post->category?->name,
        'Pondok Pesantren Pekanbaru',
        'Mahad Darussalam',
        'Mahad Darussalam Rumbai',
        'Pondok Pesantren Rumbai',
        'Berita Pondok',
        'Berita Mahad'
    ])->filter()->unique()->implode(', ');
@endphp

@section('meta_title', $post->title . ' - Mahad Darussalam Rumbai')
@section('meta_description', $shareDescription)
@section('meta_keywords', $metaKeywords)
@section('meta_url', url()->current())
@section('meta_type', 'article')
@section('meta_image', $shareImage)

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
            <div style="display:flex;align-items:flex-start;gap:12px;justify-content:space-between;">
                <div>
                    <span class="pill">{{ $post->category->name ?? 'Berita' }}</span>
                    <h1 style="margin:12px 0 6px;">{{ $post->title }}</h1>
                    <div class="meta">
                        {{ optional($post->published_at ?? $post->created_at)->format('d M Y H:i') }}
                    </div>
                </div>

                <div class="share-buttons" style="display:flex;gap:8px;align-items:center;">
                    <button id="btn-share" class="btn btn-sm btn-primary" title="Bagikan" style="display:flex;gap:6px;align-items:center;padding:6px 10px;border-radius:8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 12v7a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-7" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 6l-4-4-4 4" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 2v14" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Bagikan
                    </button>
                </div>
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

            <div style="margin-top:18px;">
                <a class="back" href="{{ url('/') }}">Kembali ke beranda</a>
            </div>
        </div>
    </main>

    <x-landing-footer />
@endsection

@if (($post->media_type ?? 'image') === 'instagram' && !empty($post->embed_url))
    @push('scripts')
        <script async src="https://www.instagram.com/embed.js"></script>
    @endpush
@endif

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pageUrl = '{{ url()->current() }}';
            const title = {!! json_encode($post->title) !!};
            const description = {!! json_encode($shareDescription) !!};
            const text = title + '\n' + pageUrl;
            const waUrl = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(text);

            const btnShare = document.getElementById('btn-share');

            if (btnShare) {
                btnShare.addEventListener('click', function () {
                    if (navigator.share) {
                        navigator.share({
                            title: title,
                            text: description,
                            url: pageUrl
                        }).catch(() => {
                            window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(text), '_blank');
                        });
                    } else {
                        window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(text), '_blank');
                    }
                });
            }
        });
    </script>
@endpush
