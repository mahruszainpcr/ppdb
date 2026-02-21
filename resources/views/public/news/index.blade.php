@extends('welcome')

@section('title', 'Informasi - Ma\'had Darussalam Al-Islami')

@push('styles')
    <style>
        .info-section {
            padding: 40px 0 0;
        }

        .info-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        .info-title {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 14px;
            color: #fff;
            background: #1e7f5c;
            padding: 8px 12px;
            border-radius: 8px;
        }

        .info-title span {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 4px;
            background: #d1a952;
        }

        .info-link {
            font-size: 12px;
            color: var(--muted);
        }

        .info-link:hover {
            text-decoration: underline;
        }

        .video-grid {
            display: grid;
            grid-template-columns: 2.1fr 1fr;
            gap: 16px;
        }

        .video-card {
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--line);
            background: #fff;
            box-shadow: var(--shadow);
        }

        .video-embed {
            position: relative;
            padding-top: 56.25%;
            background: #000;
        }

        .video-embed iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .video-body {
            padding: 12px 14px 14px;
        }

        .video-body h4 {
            margin: 0;
            font-size: 14px;
        }

        .video-body span {
            display: block;
            margin-top: 6px;
            color: var(--muted);
            font-size: 11px;
        }

        .video-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .video-item {
            display: grid;
            grid-template-columns: 96px 1fr;
            gap: 10px;
            align-items: center;
            border-radius: 12px;
            border: 1px solid var(--line);
            background: #fff;
            padding: 10px;
            box-shadow: var(--shadow);
        }

        .video-thumb {
            position: relative;
            width: 96px;
            aspect-ratio: 16 / 9;
            border-radius: 8px;
            overflow: hidden;
            background-size: cover;
            background-position: center;
        }

        .video-meta {
            font-size: 11px;
            color: var(--muted);
            margin-bottom: 4px;
        }

        .video-item h5 {
            margin: 0;
            font-size: 12px;
        }

        .ig-grid,
        .category-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .ig-card,
        .category-card {
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--line);
            background: #fff;
            box-shadow: var(--shadow);
        }

        .ig-card .thumb,
        .category-card .thumb {
            aspect-ratio: 4 / 3;
            background-size: cover;
            background-position: center;
        }

        .ig-card .thumb.embed {
            position: relative;
            aspect-ratio: auto;
            height: auto;
            padding-top: 56.25%;
            background: #fff;
        }

        .ig-card .thumb.embed blockquote {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
            margin: 0;
        }

        .ig-body,
        .category-body {
            padding: 12px 14px 14px;
        }

        .ig-body h4,
        .category-body h4 {
            margin: 0;
            font-size: 13px;
        }

        .ig-body span,
        .category-body span {
            display: block;
            margin-top: 6px;
            color: var(--muted);
            font-size: 11px;
        }

        .empty-note {
            border: 1px dashed var(--line);
            border-radius: 12px;
            padding: 16px;
            font-size: 12px;
            color: var(--muted);
            background: #fff;
        }

        @media (max-width: 980px) {
            .video-grid {
                grid-template-columns: 1fr;
            }

            .ig-grid,
            .category-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .ig-grid,
            .category-grid {
                grid-template-columns: 1fr;
            }

            .video-item {
                grid-template-columns: 72px 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <x-landing-header />

    <main class="container info-section">
        <div class="info-header">
            <div class="info-title"><span></span> Video Informatif</div>
            <a class="info-link" href="{{ route('news.index') }}">Video Informatif Lainnya →</a>
        </div>

        @if ($videoPosts->count())
            @php
                $mainVideo = $videoPosts->first();
                $sideVideos = $videoPosts->slice(1, 3);
            @endphp
            <div class="video-grid">
                <div class="video-card">
                    <div class="video-embed">
                        <iframe src="{{ $mainVideo->embed_url }}" title="{{ $mainVideo->title }}" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                    </div>
                    <div class="video-body">
                        <h4>{{ $mainVideo->title }}</h4>
                        <span>{{ optional($mainVideo->published_at ?? $mainVideo->created_at)->format('d M Y') }}</span>
                    </div>
                </div>

                <div class="video-list">
                    @foreach ($sideVideos as $video)
                        <a class="video-item" href="{{ route('news.show', $video->slug) }}">
                            <div class="video-thumb"
                                style="background-image: url('{{ $video->thumbnail_url ?? 'https://images.unsplash.com/photo-1519455953755-af066f52f1a6?auto=format&fit=crop&w=800&q=60' }}');">
                            </div>
                            <div>
                                <div class="video-meta">
                                    {{ optional($video->published_at ?? $video->created_at)->format('d M Y') }}
                                </div>
                                <h5>{{ $video->title }}</h5>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @else
            <div class="empty-note">Belum ada video informatif.</div>
        @endif

        <div class="info-header" style="margin-top:28px;">
            <div class="info-title"><span></span> Instagram</div>
        </div>

        @if ($instagramPosts->count())
            @php $hasInstagramEmbed = true; @endphp
            <div class="ig-grid">
                @foreach ($instagramPosts as $post)
                    <div class="ig-card">
                        <div class="thumb embed">
                            <blockquote class="instagram-media" data-instgrm-permalink="{{ $post->embed_url }}"
                                data-instgrm-version="14"
                                style="background:#FFF; border:0; border-radius:0; margin:0; padding:0; width:100%;">
                            </blockquote>
                        </div>
                        <div class="ig-body">
                            <a href="{{ route('news.show', $post->slug) }}">
                                <h4>{{ $post->title }}</h4>
                            </a>
                            <span>{{ optional($post->published_at ?? $post->created_at)->format('d M Y') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-note">Belum ada konten Instagram.</div>
        @endif

        @if ($categoryGroups->count())
            @foreach ($categoryGroups as $group)
                <div class="info-header" style="margin-top:28px;">
                    <div class="info-title"><span></span> {{ $group->category->name }}</div>
                </div>
                <div class="category-grid">
                    @foreach ($group->posts as $post)
                        <a class="category-card" href="{{ route('news.show', $post->slug) }}">
                            <div class="thumb"
                                style="background-image: url('{{ $post->thumbnail_url ?? 'https://images.unsplash.com/photo-1519455953755-af066f52f1a6?auto=format&fit=crop&w=800&q=60' }}');">
                            </div>
                            <div class="category-body">
                                <h4>{{ $post->title }}</h4>
                                <span>{{ optional($post->published_at ?? $post->created_at)->format('d M Y') }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endforeach
        @else
            <div class="empty-note" style="margin-top:24px;">Belum ada berita di kategori.</div>
        @endif
    </main>

    <x-landing-footer />
@endsection

@if (!empty($hasInstagramEmbed))
    @push('scripts')
        <script async src="https://www.instagram.com/embed.js"></script>
    @endpush
@endif
