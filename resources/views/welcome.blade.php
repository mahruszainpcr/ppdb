<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,400,0,0" />
    <title>Ma’had Darussalam Al-Islami Rumbai</title>
    <meta name="description"
        content="Ma’had Darussalam Al-Islami Rumbai (Palas) — pendidikan beradab berbasis Al-Qur’an & As-Sunnah, program tahfidz, diniyah, bahasa Arab, dan pembinaan karakter." />
    <meta name="keywords"
        content="mahad darussalam palas, darussalam al-islami rumbai, pondok pesantren palas, tahfidz, diniyah, bahasa arab, rumbai" />
    <meta name="author" content="Ma’had Darussalam Al-Islami Rumbai" />
    <link rel="canonical" href="{{ url('/') }}" />

    <meta property="og:title" content="Ma’had Darussalam Al-Islami Rumbai" />
    <meta property="og:description"
        content="Pendidikan beradab berbasis Al-Qur’an & As-Sunnah. Program tahfidz, diniyah, bahasa Arab, dan pembinaan karakter." />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url('/') }}" />
    <meta property="og:image" content="https://mahaddarussalampalas.ponpes.id/logo.png" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Ma’had Darussalam Al-Islami Rumbai" />
    <meta name="twitter:description"
        content="Pendidikan beradab berbasis Al-Qur’an & As-Sunnah. Program tahfidz, diniyah, bahasa Arab, dan pembinaan karakter." />
    <meta name="twitter:image" content="https://mahaddarussalampalas.ponpes.id/logo.png" />

    <meta name="robots" content="index, follow" />
    <link rel="icon" href="https://mahaddarussalampalas.ponpes.id/logo.png" type="image/png" />
    <style>
        :root {
            --bg: #0b2f23;
            --bg2: #0f3a2b;
            --text: #0f172a;
            --muted: #475569;
            --white: #fff;
            --line: rgba(15, 23, 42, .10);

            --green: #1E7F5C;
            --green-2: #0f5e42;
            --soft: #E8F5F0;

            --gold: #C9A24D;
            --gold-2: #b48b38;

            --radius: 18px;
            --shadow: 0 10px 30px rgba(2, 6, 23, .10);
            --shadow2: 0 18px 50px rgba(2, 6, 23, .18);
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Noto Sans", "Helvetica Neue";
            color: var(--text);
            background: #fff;
            line-height: 1.5;
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

        /* NAV */
        .nav {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            z-index: 10;
            padding: 16px 0;
        }

        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
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
            letter-spacing: .5px;
        }

        .brand strong {
            display: block;
            font-size: 14px;
            line-height: 1.15
        }

        .brand span {
            display: block;
            font-size: 12px;
            opacity: .85
        }

        .menu {
            display: flex;
            align-items: center;
            gap: 18px;
            color: rgba(255, 255, 255, .92);
            font-size: 13px;
        }

        .menu a {
            opacity: .9
        }

        .menu a:hover {
            opacity: 1;
            text-decoration: underline
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
            padding: 10px 14px;
            border-radius: 12px;
            font-weight: 600;
            border: 1px solid transparent;
            font-size: 13px;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-primary {
            background: linear-gradient(180deg, var(--gold), var(--gold-2));
            color: #1a1205;
            box-shadow: 0 12px 26px rgba(201, 162, 77, .25);
        }

        .btn-ghost {
            background: rgba(255, 255, 255, .10);
            color: #fff;
            border-color: rgba(255, 255, 255, .20);
            backdrop-filter: blur(6px);
        }

        /* HERO */
        .hero {
            position: relative;
            min-height: 86vh;
            background:
                linear-gradient(120deg, rgba(11, 47, 35, .86), rgba(11, 47, 35, .55)),
                url("https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1600&q=60");
            background-size: cover;
            background-position: center;
            color: #fff;
            overflow: hidden;
        }

        .hero::after {
            content: "";
            position: absolute;
            inset: auto -20% -40% -20%;
            height: 380px;
            background: radial-gradient(closest-side, rgba(201, 162, 77, .22), transparent 65%);
            pointer-events: none;
            transform: rotate(-10deg);
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            gap: 40px;
            align-items: end;
            padding: 110px 0 40px;
            position: relative;
            z-index: 1;
        }

        .hero-left {
            padding-bottom: 22px;
        }

        .kicker {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .10);
            border: 1px solid rgba(255, 255, 255, .18);
            font-size: 12px;
            width: fit-content;
        }

        .dot {
            width: 7px;
            height: 7px;
            border-radius: 99px;
            background: var(--gold)
        }

        .hero h1 {
            font-size: clamp(30px, 4.2vw, 54px);
            line-height: 1.08;
            margin: 14px 0 14px;
            letter-spacing: -.6px;
        }

        .hero p {
            margin: 0 0 18px;
            max-width: 56ch;
            color: rgba(255, 255, 255, .90);
            font-size: 16px;
        }

        .hero-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap
        }

        .hero-meta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 18px;
            color: rgba(255, 255, 255, .85);
            font-size: 12px;
        }

        .pill {
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, .20);
            background: rgba(255, 255, 255, .08);
        }

        .hero-right {
            display: flex;
            justify-content: flex-end;
        }

        .hero-card {
            width: min(360px, 100%);
            background: rgba(255, 255, 255, .10);
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: var(--radius);
            padding: 16px;
            backdrop-filter: blur(8px);
            box-shadow: var(--shadow2);
        }

        .hero-card h3 {
            margin: 0 0 8px;
            font-size: 14px;
            letter-spacing: .2px
        }

        .hero-card ul {
            margin: 0;
            padding-left: 18px;
            color: rgba(255, 255, 255, .90);
            font-size: 13px
        }

        .hero-card li {
            margin: 6px 0
        }

        /* SECTION */
        section {
            padding: 72px 0
        }

        .section-title {
            text-align: center;
            margin-bottom: 28px;
        }

        .section-title .top {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 12px;
            font-weight: 700;
            color: var(--green);
            background: var(--soft);
            padding: 7px 12px;
            border-radius: 999px;
            border: 1px solid rgba(30, 127, 92, .16);
        }

        .section-title h2 {
            margin: 12px 0 0;
            font-size: clamp(22px, 3vw, 34px);
            letter-spacing: -.4px;
        }

        .section-title p {
            margin: 10px auto 0;
            max-width: 70ch;
            color: var(--muted);
            font-size: 14px;
        }

        /* VALUES (3 columns) */
        .values {
            padding-top: 54px;
            background: linear-gradient(180deg, #fff, #fff 60%, rgba(232, 245, 240, .55));
            border-top: 1px solid var(--line);
        }

        .value-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-top: 18px;
        }

        .card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 18px;
            box-shadow: var(--shadow);
        }

        .icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: rgba(30, 127, 92, .10);
            border: 1px solid rgba(30, 127, 92, .18);
            display: grid;
            place-items: center;
            margin-bottom: 12px;
        }

        .card h3 {
            margin: 0 0 6px;
            font-size: 16px
        }

        .card p {
            margin: 0;
            color: var(--muted);
            font-size: 13px
        }

        /* SPLIT (image + text) */
        .split {
            padding: 0;
            background: #fff;
        }

        .split-wrap {
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            gap: 0;
            border-radius: 22px;
            overflow: hidden;
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
        }

        .split-img {
            min-height: 330px;
            background:
                linear-gradient(120deg, rgba(30, 127, 92, .10), rgba(201, 162, 77, .08)),
                url("https://images.unsplash.com/photo-1588072432836-7b5c7a10f0f0?auto=format&fit=crop&w=1400&q=60");
            background-size: cover;
            background-position: center;
        }

        .split-content {
            background: linear-gradient(180deg, var(--bg2), var(--bg));
            color: #fff;
            padding: 26px 26px 24px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .split-content h3 {
            margin: 0 0 10px;
            font-size: 22px;
            letter-spacing: -.2px
        }

        .split-content p {
            margin: 0 0 16px;
            color: rgba(255, 255, 255, .88);
            font-size: 13px
        }

        .split-content .btn {
            align-self: flex-start
        }

        /* PROGRAM GRID */
        .program-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-top: 16px;
        }

        .program-card .icon {
            background: rgba(201, 162, 77, .10);
            border-color: rgba(201, 162, 77, .20)
        }

        .program-card h3 {
            font-size: 15px
        }

        .program-card p {
            font-size: 13px
        }

        /* CURRICULUM BAND */
        .band {
            background:
                linear-gradient(120deg, rgba(11, 47, 35, .82), rgba(11, 47, 35, .62)),
                url("{{ url('assets/landing/aktivitas-santri.png') }}");
            background-size: cover;
            background-position: center;
            color: #fff;
            padding: 72px 0;
        }

        .band h2 {
            margin: 0 0 8px;
            text-align: center;
            font-size: clamp(22px, 3.2vw, 34px)
        }

        .band p {
            margin: 0 auto 18px;
            max-width: 75ch;
            text-align: center;
            color: rgba(255, 255, 255, .88);
            font-size: 13px
        }

        .badge-row {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 18px;
        }

        .badge {
            width: 180px;
            padding: 14px 14px;
            border-radius: 18px;
            background: rgba(255, 255, 255, .10);
            border: 1px solid rgba(255, 255, 255, .18);
            backdrop-filter: blur(7px);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .badge .seal {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            background: rgba(201, 162, 77, .20);
            border: 1px solid rgba(201, 162, 77, .30);
            display: grid;
            place-items: center;
            font-weight: 900;
            color: #1a1205;
        }

        .badge strong {
            display: block;
            font-size: 13px
        }

        .badge span {
            display: block;
            font-size: 12px;
            opacity: .85
        }

        /* QUICK LAUNCH */
        .quick-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-top: 16px;
        }

        .quick {
            position: relative;
            overflow: hidden;
            border-radius: 18px;
            border: 1px solid var(--line);
            background:
                linear-gradient(120deg, rgba(30, 127, 92, .10), rgba(201, 162, 77, .08));
            padding: 14px;
            min-height: 86px;
            box-shadow: var(--shadow);
        }

        .quick .mini {
            width: 36px;
            height: 36px;
            border-radius: 14px;
            background: #fff;
            border: 1px solid var(--line);
            display: grid;
            place-items: center;
            margin-bottom: 10px;
        }

        .quick strong {
            display: block;
            font-size: 13px;
            margin-bottom: 4px
        }

        .quick span {
            display: block;
            font-size: 12px;
            color: var(--muted)
        }

        /* NEWS */
        .news-band {
            margin-top: 26px;
            border-radius: 22px;
            overflow: hidden;
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
            background:
                linear-gradient(120deg, rgba(11, 47, 35, .88), rgba(11, 47, 35, .64)),
                url("https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=1600&q=60");
            background-size: cover;
            background-position: center;
            color: #fff;
        }

        .news-band-inner {
            padding: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
        }

        .news-band h3 {
            margin: 0;
            font-size: 18px
        }

        .news-band p {
            margin: 4px 0 0;
            color: rgba(255, 255, 255, .88);
            font-size: 12px;
            max-width: 70ch
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-top: 16px;
        }

        .news-card {
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid var(--line);
            background: #fff;
            box-shadow: var(--shadow);
        }

        .thumb {
            height: 150px;
            background:
                linear-gradient(120deg, rgba(30, 127, 92, .18), rgba(201, 162, 77, .14)),
                url("https://images.unsplash.com/photo-1519455953755-af066f52f1a6?auto=format&fit=crop&w=1400&q=60");
            background-size: cover;
            background-position: center;
        }

        .news-body {
            padding: 14px
        }

        .tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            font-weight: 800;
            color: var(--green);
            background: var(--soft);
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid rgba(30, 127, 92, .16);
        }

        .news-body h4 {
            margin: 10px 0 6px;
            font-size: 14px;
            letter-spacing: -.2px
        }

        .news-body p {
            margin: 0;
            color: var(--muted);
            font-size: 12px
        }

        .news-foot {
            padding: 0 14px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: var(--muted);
            font-size: 12px;
        }

        /* FOOTER */
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
            color: rgba(255, 255, 255, .86);
            font-size: 12px;
            margin: 8px 0
        }

        .footer-grid a:hover {
            color: #fff;
            text-decoration: underline
        }

        .footer-about p {
            margin: 10px 0 0;
            color: rgba(255, 255, 255, .86);
            font-size: 12px
        }

        .footer-bottom {
            margin-top: 26px;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, .12);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            color: rgba(255, 255, 255, .82);
            font-size: 12px;
        }

        /* Responsive */
        @media (max-width: 980px) {
            .hero-grid {
                grid-template-columns: 1fr;
                gap: 18px;
                padding-top: 100px
            }

            .hero-right {
                justify-content: flex-start
            }

            .value-grid,
            .program-grid,
            .news-grid {
                grid-template-columns: 1fr 1fr
            }

            .quick-grid {
                grid-template-columns: 1fr 1fr
            }

            .footer-grid {
                grid-template-columns: 1fr 1fr
            }

            .menu,
            .cta {
                display: none
            }

            .nav-toggle {
                display: inline-flex
            }
        }

        @media (max-width: 640px) {
            section {
                padding: 56px 0
            }

            .value-grid,
            .program-grid,
            .news-grid {
                grid-template-columns: 1fr
            }

            .split-wrap {
                grid-template-columns: 1fr
            }

            .split-img {
                min-height: 240px
            }

            .footer-grid {
                grid-template-columns: 1fr
            }

            .hero-meta {
                gap: 8px
            }
        }

        .nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;

            /* efek visual */
            background: rgba(15, 58, 43, 0.92);
            /* hijau gelap semi transparan */
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);

            transition: background 0.3s ease, box-shadow 0.3s ease;
        }

        /* bayangan lembut */
        .nav.scrolled {
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.18);
        }

        /* agar konten tidak ketutup navbar */
        body {
            padding-top: 60px;
            /* sesuaikan tinggi navbar */
        }
    </style>
</head>

<body>

    <!-- HERO -->
    <header class="hero" id="home">
        <x-landing-header />
        <div class="container">
            <div class="hero-grid">
                <div class="hero-left">
                    <div class="kicker"><span class="dot"></span> Ahlan wa sahlan di Ma’had Darussalam Al-Islami
                        Rumbai</div>
                    <h1>Belajar Ilmu, Menjadi Cahaya Dunia &amp; Akhirat</h1>
                    <p>
                        Mencetak calon dai berAdab mulia sesuai Al-Qur’an dan As-Sunnah dengan pemahaman Salafus
                        Shalih,
                        dalam lingkungan yang beradab—aman, nyaman, dan mendidik.
                    </p>

                    <div class="hero-actions">
                        <a class="btn btn-primary" href="#program">Lihat Program</a>
                        <a class="btn btn-ghost" href="#kontak">Hubungi Kami</a>
                    </div>

                    <div class="hero-meta">
                        <span class="pill">NPSN 70034877</span>
                        <span class="pill">NSPP 510014710047</span>
                        <span class="pill">Tahun Ajaran 2026–2027</span>
                    </div>
                </div>

                <div class="hero-right">
                    <div class="hero-card">
                        <h3>Keunggulan Utama</h3>
                        <ul>
                            <li>Manhaj Salaf: implementasi ilmu & amal sesuai Al-Qur’an & As-Sunnah.</li>
                            <li>Bahasa Arab Harian: percakapan & materi terstruktur.</li>
                            <li>Pengajar Bersanad: Qur’an & ilmu syar’i dengan sanad jelas.</li>
                            <li>Zero Bullying: kebijakan tegas untuk lingkungan aman & beradab.</li>
                            <li>Kesetaraan Kemenag: jalur kesetaraan pendidikan resmi.</li>
                            <li>Tahfidz 6+1 Tahun: enam tahun pendidikan + satu tahun pendalaman.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- VALUES -->
    <section class="values" id="pilar">
        <div class="container">
            <div class="section-title">
                <div class="top">3 Pilar Darussalam</div>
                <h2>Pondasi Pendidikan yang Jelas dan Terarah</h2>
                <p>Fokus pembinaan Ma’had Darussalam bertumpu pada tiga pilar utama untuk membentuk generasi berilmu dan
                    beradab.</p>
            </div>

            <div class="value-grid">
                <div class="card">
                    <div class="icon">📖</div>
                    <h3>Pilar 1 :Al-Qur’an</h3>
                    <p>Tahfidz bertahap dengan muraja’ah terjadwal sebagai pondasi iman dan amal.</p>
                </div>
                <div class="card">
                    <div class="icon">🗣️</div>
                    <h3>Pilar 2 :Bahasa Arab</h3>
                    <p>Pembiasaan harian dan materi terstruktur: nahwu, sharaf, percakapan, dan khat.</p>
                </div>
                <div class="card">
                    <div class="icon">🤝</div>
                    <h3>Pilar 3 :Adab</h3>
                    <p>Pembinaan adab & karakter dengan nilai SAPA: Sahabat, Amanah, Saling hormat, Adil, Bertanggung
                        jawab.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SPLIT FEATURE -->
    <section class="split" id="visimisi">
        <div class="container">
            <div class="split-wrap">
                <div class="split-img" aria-label="Kegiatan pembelajaran"><img
                        src="{{ url('assets/landing/image.png') }}" alt=""></div>
                <div class="split-content">
                    <h3>Lingkungan Beradab — aman, nyaman, dan mendidik.</h3>
                    <p><strong>Visi:</strong> Terwujudnya insan religius yang cerdas dan berAdab mulia berdasarkan
                        Al-Qur’an dan As-Sunnah sesuai pemahaman Salafus Shalih.</p>
                    <p><strong>Misi:</strong> Membekali aqidah, Adab, dan keterampilan hidup; memberdayakan
                        yatim/piatu/fakir/miskin dengan pendidikan terjangkau; mengembangkan kepemimpinan & kemandirian
                        santri; peningkatan kompetensi tenaga pengajar; serta kolaborasi pendidikan nasional &
                        internasional.</p>
                    <a class="btn btn-primary" href="#kontak">Konsultasi &amp; Kontak</a>
                </div>
            </div>
        </div>
    </section>

    <!-- PROGRAM -->
    <section id="program">
        <div class="container">
            <div class="section-title">
                <div class="top">Program Pendidikan</div>
                <h2>Madrasah, Tahfidz, dan Keterampilan Berbasis Karakter</h2>
                <p>Program dirancang agar santri tumbuh seimbang: kuat aqidahnya, baik Adabnya, dan siap mandiri.</p>
            </div>

            <div class="program-grid">
                <div class="card program-card">
                    <div class="icon">🕌</div>
                    <h3>Tahfidz Al-Qur’an</h3>
                    <p>Target hafalan bertahap dengan muraja’ah terjadwal, membangun kedekatan santri dengan Al-Qur’an.
                    </p>
                </div>
                <div class="card program-card">
                    <div class="icon">📚</div>
                    <h3>Diniyah &amp; Bahasa Arab</h3>
                    <p>Pembelajaran nahwu, sharaf, percakapan, dan khat dalam materi terstruktur dan pembiasaan harian.
                    </p>
                </div>
                <div class="card program-card">
                    <div class="icon">🌱</div>
                    <h3>Kemandirian &amp; Leadership</h3>
                    <p>Pembinaan adab, organisasi santri, dan kewirausahaan sederhana untuk melatih kepemimpinan.</p>
                </div>
                <div class="card program-card" id="fasilitas">
                    <div class="icon">🛡️</div>
                    <h3>Zero Bullying</h3>
                    <p>Kebijakan tegas untuk menjaga lingkungan aman, beradab, dan nyaman bagi seluruh santri.</p>
                </div>
                <div class="card program-card">
                    <div class="icon">🧑‍🏫</div>
                    <h3>Pengajar Bersanad</h3>
                    <p>Qur’an dan ilmu syar’i diajarkan oleh pengajar dengan sanad yang jelas dan pembinaan yang
                        terarah.</p>
                </div>
                <div class="card program-card">
                    <div class="icon">✅</div>
                    <h3>Kesetaraan Kemenag</h3>
                    <p>Jalur kesetaraan pendidikan resmi serta legalitas lembaga yang jelas (NPSN & NSPP).</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CURRICULUM BAND -->
    <section class="band">
        <div class="container">
            <h2>Kurikulum &amp; Program Pendidikan</h2>
            <p>Pembinaan berkesinambungan dengan fokus adab, ilmu, dan kemandirian untuk membangun generasi berilmu dan
                beradab.</p>

            <div class="badge-row">
                <div class="badge">
                    <div class="seal">Q</div>
                    <div><strong>Al-Qur’an</strong><span>Tahfidz & Muraja’ah</span></div>
                </div>
                <div class="badge">
                    <div class="seal">A</div>
                    <div><strong>Bahasa Arab</strong><span>Pembiasaan Harian</span></div>
                </div>
                <div class="badge">
                    <div class="seal">A</div>
                    <div><strong>Adab</strong><span>Adab & Karakter (SAPA)</span></div>
                </div>
            </div>
        </div>
    </section>

    <!-- QUICK LAUNCH -->
    <section>
        <div class="container">
            <div class="section-title">
                <div class="top">Quick Launch</div>
                <h2>Akses Cepat Informasi</h2>
                <p>Pilih menu untuk melihat informasi utama tentang Ma’had Darussalam.</p>
            </div>

            <div class="quick-grid">
                <a class="quick" href="#visimisi">
                    <div class="mini">🎯</div>
                    <strong>Visi & Misi</strong>
                    <span>Arah pendidikan & tujuan pembinaan</span>
                </a>
                <a class="quick" href="#pilar">
                    <div class="mini">🏛️</div>
                    <strong>3 Pilar</strong>
                    <span>Al-Qur’an, Bahasa Arab, Adab</span>
                </a>
                <a class="quick" href="#program">
                    <div class="mini">📌</div>
                    <strong>Program</strong>
                    <span>Tahfidz, Diniyah, Leadership</span>
                </a>
                <a class="quick" href="#kontak">
                    <div class="mini">☎️</div>
                    <strong>Kontak</strong>
                    <span>Informasi & konsultasi pendaftaran</span>
                </a>
            </div>

            <div class="news-band" style="margin-top:22px;">
                <div class="news-band-inner">
                    <div>
                        <h3>Ma’had Darussalam Al-Islami Rumbai</h3>
                        <p>Membangun generasi berilmu dan beradab; cahaya bagi dunia &amp; akhirat.</p>
                    </div>
                    <a class="btn btn-primary" href="#kontak">Info Pendaftaran</a>
                </div>
            </div>

            <div class="news-grid" style="margin-top:18px;">
                @if (!empty($newsPosts) && $newsPosts->count())
                    @foreach ($newsPosts as $post)
                        <article class="news-card">
                            <div class="thumb"
                                style="background-image: url('{{ $post->thumbnail_url ?? 'https://images.unsplash.com/photo-1519455953755-af066f52f1a6?auto=format&fit=crop&w=1400&q=60' }}');">
                            </div>
                            <div class="news-body">
                                <span class="tag">{{ $post->category->name ?? 'INFO' }}</span>
                                <h4>{{ $post->title }}</h4>
                                <p>
                                    {{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 120) }}
                                </p>
                            </div>
                            <div class="news-foot">
                                <span>{{ optional($post->published_at ?? $post->created_at)->format('d M Y') }}</span>
                                <a href="{{ route('news.show', $post->slug) }}">Selengkapnya →</a>
                            </div>
                        </article>
                    @endforeach
                @else
                    <article class="news-card">
                        <div class="thumb"></div>
                        <div class="news-body">
                            <span class="tag">INFO</span>
                            <h4>Program Tahfidz 6+1 Tahun</h4>
                            <p>Enam tahun pendidikan dengan satu tahun pendalaman untuk memperkuat hafalan dan pembinaan.
                            </p>
                        </div>
                        <div class="news-foot">
                            <span>Tahun Ajaran 2026–2027</span>
                            <span>Selengkapnya →</span>
                        </div>
                    </article>

                    <article class="news-card">
                        <div class="thumb"></div>
                        <div class="news-body">
                            <span class="tag">LINGKUNGAN</span>
                            <h4>Zero Bullying &amp; Pendampingan</h4>
                            <p>Kebijakan tegas untuk menjaga keamanan dan kenyamanan belajar santri setiap hari.</p>
                        </div>
                        <div class="news-foot">
                            <span>Beradab &amp; Aman</span>
                            <span>Selengkapnya →</span>
                        </div>
                    </article>

                    <article class="news-card">
                        <div class="thumb"></div>
                        <div class="news-body">
                            <span class="tag">BAHASA</span>
                            <h4>Bahasa Arab Harian</h4>
                            <p>Percakapan dan materi terstruktur (nahwu, sharaf, khat) untuk membangun kompetensi bahasa.
                            </p>
                        </div>
                        <div class="news-foot">
                            <span>Program Inti</span>
                            <span>Selengkapnya →</span>
                        </div>
                    </article>
                @endif
            </div>
        </div>
    </section>

        <x-landing-footer />

</body>

</html>





