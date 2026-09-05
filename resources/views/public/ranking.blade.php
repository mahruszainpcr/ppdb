<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ranking Santri | Ma'had Darussalam Al-Islami</title>
    <meta name="description" content="Daftar ranking peserta yang sudah tercatat hadir pada kegiatan Try Out PPDB Ma'had Darussalam Al-Islami.">
    <link rel="icon" href="https://mahaddarussalampalas.ponpes.id/logo.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('/assets/css/google-icon.css') }}">
    <style>
        :root { --ink: #12382b; --green: #1e7f5c; --deep: #0b2f23; --gold: #c9a24d; --muted: #64766d; --line: #dfebe5; --soft: #eef7f2; }
        * { box-sizing: border-box; }
        body { margin: 0; padding-top: 60px; color: #17251f; background: #f7faf8; font-family: "Public Sans", sans-serif; }
        a { color: inherit; text-decoration: none; }
        .container { width: min(1080px, calc(100% - 32px)); margin: 0 auto; }
        .nav { position: fixed; top: 0; left: 0; right: 0; z-index: 9999; background: rgba(15,58,43,.94); color: #fff; box-shadow: 0 6px 24px rgba(0,0,0,.12); }
        .nav-inner { min-height: 76px; display: flex; align-items: center; justify-content: space-between; gap: 20px; }
        .brand { display: flex; align-items: center; gap: 10px; }
        .brand .logo { width: 40px; height: 40px; padding: 5px; border: 1px solid rgba(255,255,255,.2); border-radius: 11px; background: rgba(255,255,255,.08); }
        .brand .logo img { display: block; width: 100%; height: 100%; object-fit: contain; }
        .brand strong, .brand span { display: block; line-height: 1.2; }
        .brand strong { font-size: .88rem; }
        .brand span { color: rgba(255,255,255,.7); font-size: .7rem; margin-top: 3px; }
        .menu { display: flex; align-items: center; gap: 18px; color: rgba(255,255,255,.86); font-size: .8rem; }
        .menu a:hover, .menu a[aria-current="page"] { color: #f5d98f; }
        .cta { display: flex; gap: 8px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: .62rem .85rem; border-radius: 8px; font-size: .76rem; font-weight: 700; }
        .btn-primary { color: var(--deep); background: #f5d98f; }
        .btn-ghost { color: #fff; border: 1px solid rgba(255,255,255,.25); }
        .nav-toggle, .mobile-menu { display: none; }
        .mobile-menu.open { display: block; padding: 12px 0 18px; }
        .mobile-links { display: grid; gap: 13px; color: rgba(255,255,255,.9); font-size: .82rem; }
        .mobile-cta { display: flex; gap: 8px; margin-top: 16px; }
        .hero { padding: 74px 0 92px; color: #fff; background: radial-gradient(circle at 82% 20%, rgba(201,162,77,.24), transparent 25%), linear-gradient(130deg, #0b2f23, #1b684a); }
        .eyebrow { color: #f5d98f; font-size: .73rem; font-weight: 800; letter-spacing: .11em; text-transform: uppercase; }
        h1 { max-width: 700px; margin: 12px 0 14px; font-size: clamp(2rem, 5vw, 3.7rem); line-height: 1.05; letter-spacing: 0; }
        .hero p { max-width: 650px; margin: 0; color: rgba(255,255,255,.82); line-height: 1.7; }
        .content { margin-top: -40px; padding-bottom: 64px; position: relative; }
        .ranking-panel { overflow: hidden; border: 1px solid var(--line); border-radius: 18px; background: #fff; box-shadow: 0 18px 42px rgba(12,48,35,.1); }
        .panel-head { display: flex; align-items: end; justify-content: space-between; gap: 16px; padding: 24px 26px; border-bottom: 1px solid var(--line); }
        .panel-head h2 { margin: 0 0 5px; color: var(--ink); font-size: 1.15rem; }
        .panel-head p { margin: 0; color: var(--muted); font-size: .8rem; }
        .period { padding: .45rem .7rem; border-radius: 999px; color: var(--green); background: var(--soft); font-size: .75rem; font-weight: 700; white-space: nowrap; }
        .group-heading { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 18px 26px 11px; color: var(--ink); font-size: .88rem; font-weight: 800; }
        .group-heading span { color: var(--muted); font-size: .72rem; font-weight: 600; }
        .group-empty { padding: 24px; border-top: 1px solid #edf3ef; }
        table { width: 100%; border-collapse: collapse; }
        th { padding: 13px 26px; color: var(--muted); background: #fbfdfc; font-size: .68rem; letter-spacing: .07em; text-align: left; text-transform: uppercase; }
        td { padding: 17px 26px; border-top: 1px solid #edf3ef; font-size: .86rem; }
        td:first-child, th:first-child { width: 92px; }
        .rank { display: inline-grid; width: 34px; height: 34px; place-items: center; border-radius: 10px; color: var(--green); background: var(--soft); font-weight: 800; }
        .rank.top { color: #76580d; background: #fff5d6; }
        .ranking-top-five { background: #fff8df; }
        .ranking-top-five td { border-top-color: #f1e2ad; }
        .direct-pass { display: inline-flex; align-items: center; gap: 6px; margin-top: 5px; padding: 4px 8px; border-radius: 999px; color: #76580d; background: #fff1b8; font-size: .68rem; font-weight: 700; }
        .student { color: var(--ink); font-weight: 700; }
        .school { color: var(--muted); }
        .empty { padding: 56px 24px; color: var(--muted); text-align: center; }
        .note { margin-top: 15px; color: var(--muted); font-size: .73rem; line-height: 1.6; }
        footer { margin-top: 60px; padding: 46px 0 28px; color: #fff; background: linear-gradient(180deg, #0f3a2b, #0b2f23); }
        .footer-grid { display: grid; grid-template-columns: 1.3fr 1fr 1fr 1fr; gap: 18px; }
        .footer-grid h5 { margin: 0 0 10px; font-size: 13px; }
        .footer-grid a { display: block; margin: 8px 0; color: rgba(255,255,255,.86); font-size: 12px; }
        .footer-grid a:hover { color: #fff; text-decoration: underline; }
        .footer-about p { margin: 10px 0 0; color: rgba(255,255,255,.86); font-size: 12px; }
        .footer-bottom { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-top: 26px; padding-top: 16px; border-top: 1px solid rgba(255,255,255,.12); color: rgba(255,255,255,.82); font-size: 12px; }
        @media (max-width: 980px) { .menu, .cta { display: none; } .nav-toggle { display: inline-flex; width: 40px; height: 40px; color: #fff; border: 1px solid rgba(255,255,255,.25); border-radius: 9px; background: transparent; } .footer-grid { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 575px) { .panel-head { align-items: start; flex-direction: column; padding: 20px; } .group-heading { padding-left: 15px; padding-right: 15px; } th, td { padding: 13px 15px; } .hero { padding: 58px 0 75px; } .footer-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <x-landing-header />

    <header class="hero">
        <div class="container">
            <div class="eyebrow">Hasil seleksi Try Out PPDB</div>
            <h1>Ranking Peserta Try Out PPDB</h1>
            <p>Ranking seluruh pendaftar berdasarkan nilai seleksi yang sudah tersedia. Daftar disusun terpisah untuk Ikhwan dan Akhwat.</p>
        </div>
    </header>

    <main class="content">
        <div class="container">
            <section class="ranking-panel" aria-labelledby="ranking-title">
                <div class="panel-head">
                    <div><h2 id="ranking-title">Daftar Ranking Nilai</h2><p>Hanya peserta dengan lima nilai lengkap dan lebih dari 0 yang diberi ranking.</p></div>
                    <div class="period">Semua pendaftar</div>
                </div>
                <div class="direct-pass mx-3 mt-3 mb-1">5 besar setiap kelompok: Lolos langsung tanpa ujian Try Out PPDB</div>
                @if ($rankingIkhwan->isEmpty() && $rankingAkhwat->isEmpty())
                    <div class="empty">Belum ada data pendaftar.</div>
                @else
                    @foreach ([['key' => 'ikhwan', 'label' => 'Ikhwan', 'items' => $rankingIkhwan], ['key' => 'akhwat', 'label' => 'Akhwat', 'items' => $rankingAkhwat]] as $group)
                        <div class="group-heading">{{ $group['label'] }} <span>{{ $group['items']->count() }} peserta</span></div>
                        @if ($group['items']->isEmpty())
                            <div class="empty group-empty">Belum ada pendaftar {{ strtolower($group['label']) }}.</div>
                        @else
                            <div style="overflow-x:auto">
                                <table>
                                    <thead><tr><th>Peringkat</th><th>Nama Santri</th><th>Asal Sekolah</th></tr></thead>
                                    <tbody>
                                        @foreach ($group['items'] as $item)
                                            <tr class="{{ $item['rank'] <= 5 ? 'ranking-top-five' : '' }}">
                                                <td><span class="rank {{ $item['rank'] <= 3 ? 'top' : '' }}">{{ $item['rank'] }}</span></td>
                                                <td class="student">
                                                    {{ $item['name'] }}
                                                    @if ($item['rank'] <= 5)
                                                        <div class="direct-pass">Lolos langsung tanpa ujian Try Out PPDB</div>
                                                    @endif
                                                </td>
                                                <td class="school">{{ $item['school'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endforeach
                @endif
            </section>
            <p class="note">Ranking diurutkan berdasarkan rata-rata Soal 1, Soal 2, Soal 3, TPA, dan Bahasa Arab. Nilai tetap tidak ditampilkan di halaman publik; rincian nilai dan hasil seleksi hanya dapat dilihat oleh wali.</p>
        </div>
    </main>
    <x-landing-footer />
</body>
</html>
