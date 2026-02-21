<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,400,0,0" />
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
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px
        }

        .hero {
            position: relative;
            background: linear-gradient(180deg, var(--bg2), var(--bg));
            color: #fff;
            padding: 90px 0 30px;
        }

        .nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            padding: 14px 0;
            background: rgba(15, 58, 43, 0.92);
            backdrop-filter: blur(8px);
        }

        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .menu {
            display: flex;
            align-items: center;
            gap: 18px;
            color: rgba(255, 255, 255, .92);
            font-size: 13px;
        }

        .menu a {
            opacity: .9;
        }

        .menu a:hover {
            opacity: 1;
            text-decoration: underline;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
        }

        .logo {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .20);
            display: grid;
            place-items: center;
            font-weight: 800;
        }

        .brand strong {
            display: block;
            font-size: 14px;
            line-height: 1.15;
        }

        .brand span {
            display: block;
            font-size: 12px;
            opacity: .85;
        }

        .cta {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-toggle {
            display: none;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, .25);
            background: rgba(255, 255, 255, .10);
            color: #fff;
            cursor: pointer;
        }

        .nav-toggle .material-symbols-outlined {
            font-size: 22px;
            line-height: 1;
        }

        .mobile-menu {
            display: none;
            margin-top: 12px;
            padding: 12px;
            border-radius: 14px;
            background: rgba(15, 58, 43, 0.98);
            border: 1px solid rgba(255, 255, 255, .12);
        }

        .mobile-menu.open {
            display: block;
        }

        .mobile-links {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .mobile-links a {
            padding: 8px 10px;
            border-radius: 10px;
            background: rgba(255, 255, 255, .06);
            color: rgba(255, 255, 255, .95);
        }

        .mobile-cta {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 12px;
            font-weight: 600;
            border: 1px solid transparent;
            font-size: 12px;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-primary {
            background: linear-gradient(180deg, #C9A24D, #b48b38);
            color: #1a1205;
        }

        .btn-ghost {
            background: rgba(255, 255, 255, .10);
            color: #fff;
            border-color: rgba(255, 255, 255, .20);
            backdrop-filter: blur(6px);
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

        footer {
            margin-top: 60px;
            background: linear-gradient(180deg, var(--bg2), var(--bg));
            color: #fff;
            padding: 46px 0 28px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.3fr 1fr 1fr 1fr;
            gap: 18px;
        }

        .footer-grid h5 {
            margin: 0 0 10px;
            font-size: 13px;
            letter-spacing: .2px
        }

        .footer-grid a {
            display: block;
            color: rgba(255, 255, 255, .85);
            font-size: 12px;
            margin-bottom: 6px
        }

        .footer-bottom {
            margin-top: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            color: rgba(255, 255, 255, .82);
            font-size: 12px;
        }

        body {
            padding-top: 60px;
        }

        @media (max-width: 980px) {
            .menu,
            .cta {
                display: none
            }

            .nav-toggle {
                display: inline-flex
            }

            .footer-grid {
                grid-template-columns: 1fr 1fr
            }
        }

        @media (max-width: 640px) {
            .footer-grid {
                grid-template-columns: 1fr
            }
        }
    </style>
</head>

<body>
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
            @if ($post->thumbnail_url)
                <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}"
                    style="border-radius: 14px; margin-bottom: 16px;">
            @endif

            {!! $post->content !!}

            <a class="back" href="{{ url('/') }}">← Kembali ke beranda</a>
        </div>
    </main>

    <x-landing-footer />
</body>

</html>






