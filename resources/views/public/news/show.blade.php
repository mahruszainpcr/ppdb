<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>{{ $post->title }} - Ma'had Darussalam Al-Islami</title>
    <style>
        :root {
            --bg: #0b2f23;
            --bg2: #0f3a2b;
            --text: #0f172a;
            --muted: #475569;
            --white: #fff;
            --line: rgba(15, 23, 42, .10);
            --green: #1E7F5C;
            --soft: #E8F5F0;
            --radius: 18px;
            --shadow: 0 10px 30px rgba(2, 6, 23, .10);
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Noto Sans", "Helvetica Neue";
            color: var(--text);
            background: #fff;
            line-height: 1.6;
        }

        a {
            color: inherit;
            text-decoration: none
        }

        img {
            max-width: 100%;
            display: block
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px
        }

        .hero {
            background: linear-gradient(180deg, var(--bg2), var(--bg));
            color: #fff;
            padding: 48px 0 30px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 700;
            color: var(--green);
            background: var(--soft);
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid rgba(30, 127, 92, .16);
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
</head>

<body>
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
            @if ($post->thumbnail_url)
                <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}"
                    style="border-radius: 14px; margin-bottom: 16px;">
            @endif

            {!! $post->content !!}

            <a class="back" href="{{ url('/') }}">← Kembali ke beranda</a>
        </div>
    </main>
</body>

</html>
