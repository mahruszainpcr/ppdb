@extends('welcome')

@section('title', 'PPDB - Persyaratan dan Timeline')

@push('styles')
    <style>
        .ppdb-page {
            padding: 42px 0 0;
        }

        .ppdb-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .ppdb-hero {
            position: relative;
            overflow: hidden;
            border-radius: 26px;
            border: 1px solid rgba(201, 162, 77, .26);
            background:
                radial-gradient(circle at top right, rgba(201, 162, 77, .28), transparent 58%),
                linear-gradient(140deg, #0f3a2b 0%, #1b6b4f 52%, #15563f 100%);
            color: #fff;
            padding: 38px 34px;
            box-shadow: 0 18px 46px rgba(2, 22, 16, .22);
        }

        .ppdb-hero-grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 18px;
            align-items: stretch;
        }

        .ppdb-hero-main,
        .ppdb-hero-side {
            position: relative;
            z-index: 1;
        }

        .ppdb-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, .22);
            background: rgba(255, 255, 255, .08);
            font-size: 12px;
            font-weight: 700;
            padding: 7px 12px;
        }

        .ppdb-kicker span {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #d1a952;
        }

        .ppdb-hero h1 {
            margin: 14px 0 10px;
            font-size: clamp(28px, 4vw, 46px);
            line-height: 1.1;
            letter-spacing: -.3px;
        }

        .ppdb-hero p {
            margin: 0;
            max-width: 64ch;
            color: rgba(255, 255, 255, .90);
        }

        .ppdb-actions {
            margin-top: 22px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .ppdb-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 14px;
            font-weight: 700;
            border: 1px solid transparent;
            text-decoration: none;
            white-space: nowrap;
        }

        .ppdb-btn-primary {
            background: linear-gradient(180deg, #d3b163 0%, #b7892c 100%);
            color: #1f1807;
            box-shadow: 0 14px 28px rgba(183, 137, 44, .35);
        }

        .ppdb-btn-secondary {
            background: rgba(255, 255, 255, .10);
            color: #fff;
            border-color: rgba(255, 255, 255, .26);
        }

        .ppdb-metrics {
            margin-top: 22px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .ppdb-metric {
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, .16);
            background: rgba(255, 255, 255, .10);
            padding: 14px 16px;
        }

        .ppdb-metric strong {
            display: block;
            font-size: 24px;
            line-height: 1.1;
            margin-bottom: 4px;
        }

        .ppdb-metric span {
            color: rgba(255, 255, 255, .86);
            font-size: 13px;
        }

        .ppdb-glance {
            height: 100%;
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, .18);
            background: rgba(255, 255, 255, .08);
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            backdrop-filter: blur(6px);
        }

        .ppdb-glance h3 {
            margin: 0;
            font-size: 18px;
        }

        .ppdb-glance-list {
            margin: 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 8px;
        }

        .ppdb-glance-list li {
            display: grid;
            grid-template-columns: 22px 1fr;
            gap: 8px;
            font-size: 13px;
            color: rgba(255, 255, 255, .93);
            align-items: start;
        }

        .ppdb-glance-list .material-symbols-outlined {
            font-size: 18px;
            line-height: 1;
            color: #f3d995;
            margin-top: 1px;
        }

        .ppdb-illus {
            margin-top: auto;
            border-radius: 14px;
            border: 1px dashed rgba(255, 255, 255, .25);
            background:
                radial-gradient(circle at 20% 20%, rgba(243, 217, 149, .26), transparent 45%),
                linear-gradient(135deg, rgba(255, 255, 255, .08), rgba(255, 255, 255, .02));
            min-height: 120px;
            display: grid;
            place-items: center;
            text-align: center;
            padding: 10px;
        }

        .ppdb-illus .material-symbols-outlined {
            font-size: 38px;
            color: #f7dfaa;
            display: block;
            margin-bottom: 6px;
        }

        .ppdb-illus span {
            font-size: 12px;
            color: rgba(255, 255, 255, .92);
        }

        .ppdb-highlight-grid {
            margin-top: 16px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .ppdb-highlight {
            border-radius: 14px;
            border: 1px solid var(--line);
            background: linear-gradient(180deg, #fff 0%, #f6fbf8 100%);
            padding: 13px;
        }

        .ppdb-highlight-top {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            color: #0f5e42;
            font-weight: 700;
            font-size: 13px;
        }

        .ppdb-highlight-top .material-symbols-outlined {
            font-size: 18px;
        }

        .ppdb-highlight p {
            margin: 0;
            font-size: 13px;
            color: #1e293b;
        }

        .ppdb-section {
            margin-top: 28px;
            border-radius: 24px;
            border: 1px solid var(--line);
            background: #fff;
            box-shadow: var(--shadow);
            padding: 24px;
        }

        .ppdb-title {
            margin: 0;
            font-size: clamp(22px, 2.8vw, 32px);
            letter-spacing: -.2px;
        }

        .ppdb-title-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ppdb-title-wrap .material-symbols-outlined {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            background: #eaf6f1;
            color: #0f5e42;
            font-size: 20px;
            border: 1px solid rgba(15, 94, 66, .14);
        }

        .ppdb-lead {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: 14px;
            max-width: 74ch;
        }

        .ppdb-grid {
            margin-top: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .ppdb-card {
            border-radius: 16px;
            border: 1px solid var(--line);
            background: linear-gradient(180deg, #ffffff 0%, #f8fcfa 100%);
            padding: 16px;
        }

        .ppdb-card h3 {
            margin: 0 0 10px;
            font-size: 17px;
        }

        .ppdb-list {
            margin: 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 10px;
        }

        .ppdb-list li {
            display: grid;
            grid-template-columns: 22px 1fr;
            gap: 10px;
            align-items: start;
            font-size: 14px;
            color: #1e293b;
        }

        .ppdb-check {
            width: 22px;
            height: 22px;
            border-radius: 999px;
            background: #e9f6f0;
            color: #1e7f5c;
            border: 1px solid rgba(30, 127, 92, .24);
            font-size: 12px;
            display: grid;
            place-items: center;
            font-weight: 700;
            margin-top: 1px;
        }

        .ppdb-timeline {
            margin-top: 20px;
            position: relative;
            display: grid;
            gap: 12px;
        }

        .ppdb-timeline::before {
            content: "";
            position: absolute;
            left: 11px;
            top: 10px;
            bottom: 10px;
            width: 2px;
            background: linear-gradient(180deg, #1e7f5c 0%, rgba(30, 127, 92, .15) 100%);
        }

        .ppdb-step {
            position: relative;
            margin-left: 34px;
            border-radius: 14px;
            border: 1px solid var(--line);
            background: #fff;
            padding: 14px;
        }

        .ppdb-step::before {
            content: "";
            position: absolute;
            left: -30px;
            top: 17px;
            width: 16px;
            height: 16px;
            border-radius: 999px;
            border: 2px solid #fff;
            background: #1e7f5c;
            box-shadow: 0 0 0 2px rgba(30, 127, 92, .35);
        }

        .ppdb-step-date {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            padding: 5px 10px;
            border-radius: 999px;
            background: #eef8f3;
            border: 1px solid rgba(30, 127, 92, .20);
            color: #0f5e42;
            font-size: 12px;
            font-weight: 700;
        }

        .ppdb-step h4 {
            margin: 0;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ppdb-step h4 .material-symbols-outlined {
            font-size: 18px;
            color: #0f5e42;
        }

        .ppdb-step p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 14px;
        }

        .ppdb-note {
            margin-top: 18px;
            border-radius: 14px;
            border: 1px solid rgba(183, 137, 44, .28);
            background: #fff9ec;
            color: #5f4a1b;
            padding: 13px 14px;
            font-size: 13px;
        }

        .ppdb-policy {
            margin-top: 16px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .ppdb-policy-card {
            border-radius: 14px;
            border: 1px solid var(--line);
            background: #fff;
            padding: 14px;
        }

        .ppdb-policy-card h4 {
            margin: 0 0 8px;
            font-size: 15px;
        }

        .ppdb-policy-card p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
        }

        .ppdb-cta-band {
            margin-top: 28px;
            border-radius: 24px;
            border: 1px solid rgba(15, 58, 43, .14);
            background:
                radial-gradient(circle at left top, rgba(201, 162, 77, .22), transparent 40%),
                linear-gradient(145deg, #f8fbf8 0%, #eef7f2 60%, #e7f3ed 100%);
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .ppdb-cta-band h3 {
            margin: 0 0 6px;
            font-size: clamp(20px, 2.2vw, 28px);
        }

        .ppdb-cta-band p {
            margin: 0;
            color: var(--muted);
            max-width: 58ch;
            font-size: 14px;
        }

        .ppdb-info-grid {
            margin-top: 20px;
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 16px;
            align-items: start;
        }

        .ppdb-panel {
            border-radius: 16px;
            border: 1px solid var(--line);
            background: #fff;
            padding: 16px;
        }

        .ppdb-panel h3 {
            margin: 0 0 10px;
            font-size: 18px;
        }

        .ppdb-subtitle {
            margin: 12px 0 8px;
            font-size: 14px;
            font-weight: 700;
            color: #0f5e42;
        }

        .ppdb-tight-list {
            margin: 0;
            padding-left: 18px;
            display: grid;
            gap: 6px;
            color: #1e293b;
            font-size: 14px;
        }

        .ppdb-badge-list {
            margin-top: 10px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .ppdb-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid rgba(30, 127, 92, .22);
            background: #ecf7f2;
            color: #0f5e42;
        }

        .ppdb-price-grid {
            margin-top: 12px;
            display: grid;
            gap: 10px;
        }

        .ppdb-price-card {
            border-radius: 12px;
            border: 1px solid var(--line);
            background: #f9fcfa;
            padding: 12px;
        }

        .ppdb-price-card h4 {
            margin: 0 0 8px;
            font-size: 15px;
        }

        .ppdb-price-card p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
        }

        @media (max-width: 900px) {
            .ppdb-hero-grid,
            .ppdb-metrics,
            .ppdb-grid,
            .ppdb-policy,
            .ppdb-info-grid,
            .ppdb-highlight-grid {
                grid-template-columns: 1fr;
            }

            .ppdb-hero {
                padding: 28px 22px;
                border-radius: 20px;
            }

            .ppdb-section,
            .ppdb-cta-band {
                border-radius: 18px;
                padding: 18px;
            }
        }
    </style>
@endpush

@section('content')
    <x-landing-header />

    <main class="ppdb-page">
        <div class="ppdb-container">
            <section class="ppdb-hero" aria-labelledby="ppdb-page-title">
                <div class="ppdb-hero-grid">
                    <div class="ppdb-hero-main">
                        <div class="ppdb-kicker"><span></span> Penerimaan Santri Baru {{ $ppdbAcademicYear }}</div>
                        <h1 id="ppdb-page-title">Informasi PPDB: Persyaratan dan Timeline Seleksi</h1>
                        <p>Halaman ini merangkum kebutuhan utama calon santri dalam alur yang jelas: cek jadwal, pahami
                            program, lalu lanjut daftar.</p>
                        <p style="margin-top:8px; font-size:13px; color:rgba(255,255,255,.86);">
                            Periode aktif: <strong>{{ $periodName ?? '-' }}</strong>
                            @if (!empty($periodWave))
                                (Gelombang {{ $periodWave }})
                            @endif
                        </p>
                            @php
                            $registrationClosed = $ppdbPeriod?->isRegistrationClosed() ?? false;
                        @endphp
                        <div class="ppdb-actions">
                            @if ($registrationClosed)
                                <button class="ppdb-btn ppdb-btn-primary" type="button" disabled>Pendaftaran Ditutup</button>
                            @else
                                <a class="ppdb-btn ppdb-btn-primary" href="{{ url('/register') }}">Daftar Sekarang</a>
                            @endif
                            <a class="ppdb-btn ppdb-btn-secondary" href="#persyaratan">Lihat Profil & Ketentuan</a>
                        </div>
                        @if ($registrationClosed)
                            <div class="ppdb-note" style="margin-top:14px; background: rgba(255,255,255,0.15); color: #fff; border-color: rgba(255,255,255,0.2);">
                                Pendaftaran PPDB {{ $periodName ?? 'periode aktif' }} telah ditutup sejak
                                {{ optional($ppdbPeriod?->registration_close_date)->translatedFormat('d M Y') ?? 'tanggal yang ditentukan' }}.
                                Informasi seleksi dan jadwal selanjutnya tersedia di halaman ini.
                            </div>
                        @endif
                        <div class="ppdb-metrics">
                            @foreach (($heroMetrics ?? []) as $metric)
                                <div class="ppdb-metric">
                                    <strong>{{ $metric['value'] }}</strong>
                                    <span>{{ $metric['label'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <aside class="ppdb-hero-side">
                        <div class="ppdb-glance">
                            <h3>Ringkasan Cepat</h3>
                            <ul class="ppdb-glance-list">
                                <li>
                                    <span class="material-symbols-outlined">payments</span>
                                    <span>{{ $brochure['registration_fee'] ?? '-' }}</span>
                                </li>
                                <li>
                                    <span class="material-symbols-outlined">school</span>
                                    <span>{{ implode(' | ', $brochure['levels'] ?? []) }}</span>
                                </li>
                                <li>
                                    <span class="material-symbols-outlined">menu_book</span>
                                    <span>{{ implode(' | ', $brochure['programs'] ?? []) }}</span>
                                </li>
                                <li>
                                    <span class="material-symbols-outlined">place</span>
                                    <span>{{ $brochure['address'] ?? '-' }}</span>
                                </li>
                            </ul>
                            <div class="ppdb-illus" aria-hidden="true">
                                <div>
                                    <span class="material-symbols-outlined">diversity_3</span>
                                    <span>Alur lebih sederhana: lihat jadwal, pilih jalur, dan daftar.</span>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>
            <section class="ppdb-highlight-grid" aria-label="Informasi Utama PPDB">
                <article class="ppdb-highlight">
                    <div class="ppdb-highlight-top">
                        <span class="material-symbols-outlined">account_balance</span>
                        <span>Pembayaran Pendaftaran</span>
                    </div>
                    <p>{{ $brochure['registration_fee_account'] ?? '-' }}</p>
                </article>
                <article class="ppdb-highlight">
                    <div class="ppdb-highlight-top">
                        <span class="material-symbols-outlined">bookmark_manager</span>
                        <span>Jalur Pembiayaan</span>
                    </div>
                    <p>Program beasiswa dan mandiri tersedia, dengan rincian biaya di bagian pembiayaan.</p>
                </article>
                <article class="ppdb-highlight">
                    <div class="ppdb-highlight-top">
                        <span class="material-symbols-outlined">public</span>
                        <span>Tautan Pendaftaran</span>
                    </div>
                    <p>{{ $brochure['registration_link'] ?? url('/register') }}</p>
                </article>
            </section>

            <section class="ppdb-section" id="persyaratan" aria-labelledby="persyaratan-title">
                <div class="ppdb-title-wrap">
                    <span class="material-symbols-outlined">menu_book</span>
                    <h2 class="ppdb-title" id="persyaratan-title">Profil, Visi, dan Nilai Utama</h2>
                </div>
                <p class="ppdb-lead">Susunan informasi dibuat bertahap dari gambaran lembaga, visi-misi, hingga nilai inti
                    agar mudah dipahami dalam sekali lihat.</p>
                <div class="ppdb-grid">
                    <article class="ppdb-card">
                        <h3>{{ $brochure['school_name'] ?? "Ma'had Darussalam Al-Islami Rumbai" }}</h3>
                        <p style="margin:0 0 10px; font-size:13px; color:var(--muted);">
                            {{ $brochure['foundation'] ?? 'Yayasan Al-Marwa Riau' }}<br>
                            NPSN: {{ $brochure['npsn'] ?? '-' }} | NSPP: {{ $brochure['nspp'] ?? '-' }}
                        </p>
                        <div class="ppdb-subtitle" style="margin-top:0;">Visi</div>
                        <p style="margin:0; color:#1e293b; font-size:14px;">{{ $brochure['vision'] ?? '-' }}</p>
                        <div class="ppdb-subtitle">Misi</div>
                        <ul class="ppdb-list">
                            @foreach (($brochure['missions'] ?? []) as $index => $mission)
                                <li><span class="ppdb-check">{{ $index + 1 }}</span><span>{{ $mission }}</span></li>
                            @endforeach
                        </ul>
                    </article>
                    <article class="ppdb-card">
                        <h3>Fasilitas Utama dan Karakter</h3>
                        <div class="ppdb-subtitle" style="margin-top:0;">Keunggulan</div>
                        <ul class="ppdb-list">
                            @foreach (($brochure['featured_values'] ?? []) as $index => $value)
                                <li><span class="ppdb-check">{{ $index + 1 }}</span><span>{{ $value }}</span></li>
                            @endforeach
                        </ul>
                        <div class="ppdb-subtitle">{{ $brochure['pillar_title'] ?? '3 Pilar dan SAPA SAHABAT' }}</div>
                        <ul class="ppdb-list">
                            @foreach (($brochure['pillars'] ?? []) as $index => $pillar)
                                <li><span class="ppdb-check">{{ $index + 1 }}</span><span>{{ $pillar }}</span></li>
                            @endforeach
                        </ul>
                    </article>
                </div>
                <div class="ppdb-note">
                    Catatan: data jadwal pada halaman ini ditarik dari pengaturan periode di database.
                    @if (!empty($ppdbPeriod?->information_note))
                        <br><br>{{ $ppdbPeriod->information_note }}
                    @else
                        Silakan cek halaman ini secara berkala agar tidak tertinggal informasi terbaru.
                    @endif
                </div>
                <div class="ppdb-policy">
                    <article class="ppdb-policy-card">
                        <h4>PPDB Awal (sampai {{ optional($ppdbPeriod?->registration_close_date)->translatedFormat('d M Y') ?? 'batas periode aktif' }})</h4>
                        <p>Fasilitas harga sama dengan tahun lalu (SPP, uang masuk, dan seragam). Terbuka untuk program
                            mandiri dan beasiswa duafa.</p>
                    </article>
                    <article class="ppdb-policy-card">
                        <h4>Setelah {{ $closeNextDayLabel ?? 'penutupan periode aktif' }}</h4>
                        <p>Pendaftaran hanya untuk santri mandiri, dengan penyesuaian uang masuk mengikuti kebijakan
                            periode berjalan.</p>
                    </article>
                </div>
            </section>

            <section class="ppdb-section" id="timeline" aria-labelledby="timeline-title">
                <div class="ppdb-title-wrap">
                    <span class="material-symbols-outlined">event</span>
                    <h2 class="ppdb-title" id="timeline-title">Timeline PPDB</h2>
                </div>
                <p class="ppdb-lead">Urutan dibuat linear agar wali santri tidak perlu menebak langkah berikutnya. Setiap
                    tahap menampilkan tanggal dan tujuan proses secara ringkas.</p>
                <div class="ppdb-timeline">
                    @foreach (($timelineItems ?? []) as $item)
                        <article class="ppdb-step">
                            <div class="ppdb-step-date">{{ $item['date'] }}</div>
                            <h4><span class="material-symbols-outlined">task_alt</span>{{ $item['title'] }}</h4>
                            <p>{{ $item['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="ppdb-section" id="detail-program" aria-labelledby="detail-program-title">
                <div class="ppdb-title-wrap">
                    <span class="material-symbols-outlined">apartment</span>
                    <h2 class="ppdb-title" id="detail-program-title">Detail Program, Fasilitas, dan Pembiayaan</h2>
                </div>
                <p class="ppdb-lead">Tambahan informasi berikut dirangkum untuk periode {{ $ppdbAcademicYear ?? 'aktif' }} agar orang tua dapat
                    membandingkan opsi program dengan lebih cepat.</p>
                <div class="ppdb-info-grid">
                    <article class="ppdb-panel">
                        <h3>Fasilitas Ma'had Darussalam Al-Islami</h3>
                        <ul class="ppdb-tight-list">
                            @foreach (($brochure['facilities_main'] ?? []) as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>

                        <div class="ppdb-subtitle">Fasilitas Lainnya</div>
                        <ul class="ppdb-tight-list">
                            @foreach (($brochure['facilities_extra'] ?? []) as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </article>

                    <article class="ppdb-panel">
                        <h3>Penerimaan Santri {{ $ppdbAcademicYear }}</h3>
                        <div class="ppdb-subtitle">Tingkat dan Program</div>
                        <ul class="ppdb-tight-list">
                            @foreach (($brochure['levels'] ?? []) as $level)
                                <li>{{ $level }}</li>
                            @endforeach
                            @foreach (($brochure['programs'] ?? []) as $program)
                                <li>{{ $program }}</li>
                            @endforeach
                        </ul>
                        <div class="ppdb-badge-list">
                            <span class="ppdb-badge">Pilihan pembiayaan: Beasiswa</span>
                            <span class="ppdb-badge">Pilihan pembiayaan: Mandiri</span>
                        </div>

                        <div class="ppdb-subtitle">Syarat/Materi Tes</div>
                        <ul class="ppdb-tight-list">
                            @foreach (($brochure['test_materials'] ?? []) as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>

                        <div class="ppdb-subtitle">Biaya Pendaftaran</div>
                        <p style="margin:0; color:#1e293b; font-size:14px;">{{ $brochure['registration_fee'] ?? '-' }}</p>
                        <p style="margin:6px 0 0; color:var(--muted); font-size:13px;">
                            {{ $brochure['registration_fee_account'] ?? '-' }}<br>
                            Daftar: {{ $brochure['registration_link'] ?? '-' }}
                        </p>

                        <div class="ppdb-subtitle">Ringkasan Pembiayaan (Sesuai Poster)</div>
                        <div class="ppdb-price-grid">
                            <div class="ppdb-price-card">
                                <h4>Program Beasiswa (Duafa) - Kuota Terbatas</h4>
                                <ul class="ppdb-tight-list" style="font-size:13px;">
                                    @foreach (($brochure['beasiswa'] ?? []) as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="ppdb-price-card">
                                <h4>Program Mandiri - Kuota Terbatas</h4>
                                <ul class="ppdb-tight-list" style="font-size:13px;">
                                    @foreach (($brochure['mandiri'] ?? []) as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="ppdb-price-card">
                                <h4>Santri Lama dan Hafalan Beasiswa</h4>
                                <p style="margin-bottom:8px;">{{ $brochure['santri_lama'] ?? '-' }}</p>
                                <ul class="ppdb-tight-list" style="font-size:13px;">
                                    @foreach (($brochure['hafalan_beasiswa'] ?? []) as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="ppdb-note" style="margin-top:12px;">
                            {{ $brochure['installment_note'] ?? '-' }}
                            @if (!empty($brochure['notes']))
                                <br><br>
                                @foreach ($brochure['notes'] as $note)
                                    - {{ $note }}<br>
                                @endforeach
                            @endif
                        </div>
                    </article>
                </div>
            </section>

            <section class="ppdb-cta-band" aria-labelledby="ppdb-cta-title">
                <div>
                    <h3 id="ppdb-cta-title">Siap Daftarkan Ananda?</h3>
                    <p>Mulai dari pendaftaran online, lalu ikuti langkah sesuai timeline. Jika butuh bantuan, tim panitia siap
                        mendampingi melalui kontak resmi di bawah halaman.</p>
                    <p style="margin-top:8px; font-size:13px;">
                        <strong>Kontak Ikhwan:</strong>
                        {{ implode(' | ', $brochure['contacts_ikhwan'] ?? []) }}<br>
                        <strong>Kontak Akhwat:</strong>
                        {{ implode(' | ', $brochure['contacts_akhwat'] ?? []) }}<br>
                        <strong>Alamat:</strong> {{ $brochure['address'] ?? '-' }}<br>
                        <strong>Sosial Media:</strong> {{ $brochure['social_handle'] ?? '-' }}
                    </p>
                </div>
                <a class="ppdb-btn ppdb-btn-primary" href="{{ url('/register') }}">Mulai Pendaftaran</a>
            </section>
        </div>
    </main>

    <x-landing-footer />
@endsection
