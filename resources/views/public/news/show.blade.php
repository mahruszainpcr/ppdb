@extends('welcome')

@section('title', ($post->title ?? 'Berita') . ' - Ma\'had Darussalam Al-Islami')

@php
    $shareDescription = \Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 160);
    $shareImage = $post->thumbnail_url ?? asset('assets/images/welcome.png');
@endphp

@section('meta_title', $post->title)
@section('meta_description', $shareDescription)
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
                    <button id="btn-copy" class="btn btn-sm btn-outline-light" title="Salin link" style="display:flex;gap:6px;align-items:center;padding:6px 10px;border-radius:8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 1H4a2 2 0 0 0-2 2v12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><rect x="8" y="5" width="14" height="14" rx="2" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Salin
                    </button>

                    <button id="btn-wa" class="btn btn-sm btn-success" title="Bagikan ke WhatsApp" style="display:flex;gap:6px;align-items:center;padding:6px 10px;border-radius:8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21 12.3a9 9 0 1 0-2.6 6.1L22 22l-3.7-1.2A8.9 8.9 0 0 0 21 12.3z" stroke="#fff" stroke-width="0" fill="#25D366"/><path d="M17.5 14.2c-.3-.1-1.6-.8-1.9-.9-.4-.1-.7-.1-1 .1s-1.2.9-1.5 1.1c-.3.1-.6.1-.9-.1s-1.1-.4-2.1-1.3c-.8-.7-1.3-1.6-1.5-2.1-.2-.5 0-.8.2-1 .2-.2.4-.6.6-.9.2-.3.2-.6 0-.9-.2-.3-1-2.4-1.4-3.3-.4-.9-.8-1-1-1H6.5c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.4s1 2.8 1.1 3c.1.2.2.3.2.5v.6c0 .2-.1.5-.2.8-.2.2-1 2.2-1.4 3-.4.8-.2 1.4.1 1.7.3.3.7.8 2 1.3 1.3.5 2.2.6 2.6.6.4 0 1.1-.3 1.8-.6.7-.4 2.2-1.5 2.5-1.9.3-.4.3-.8.2-1 .0-.2-.3-.4-.6-.5z" fill="#fff"/></svg>
                        WA
                    </button>

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

            const btnWa = document.getElementById('btn-wa');
            const btnCopy = document.getElementById('btn-copy');
            const btnShare = document.getElementById('btn-share');

            if (btnWa) {
                btnWa.addEventListener('click', function (e) {
                    e.preventDefault();
                    window.open(waUrl, '_blank');
                });
            }

            if (btnCopy) {
                btnCopy.addEventListener('click', async function () {
                    try {
                        await navigator.clipboard.writeText(pageUrl);
                        alert('Link disalin ke clipboard');
                    } catch (err) {
                        prompt('Salin link berikut:', pageUrl);
                    }
                });
            }

            if (btnShare) {
                btnShare.addEventListener('click', function () {
                    if (navigator.share) {
                        navigator.share({
                            title: title,
                            text: description,
                            url: pageUrl
                        }).catch(() => {
                            window.open(waUrl, '_blank');
                        });
                    } else {
                        window.open(waUrl, '_blank');
                    }
                });
            }
        });
    </script>
@endpush
