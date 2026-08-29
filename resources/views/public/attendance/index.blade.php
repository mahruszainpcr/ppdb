@extends('welcome')

@section('title', 'Absensi Kegiatan - Ma\'had Darussalam Al-Islami')

@push('styles')
    <style>
        .attendance-page { min-height: 72vh; padding: 112px 0 76px; background: linear-gradient(145deg, #f4f8f5 0%, #fff 68%); }
        .attendance-intro { display: grid; grid-template-columns: minmax(0, 1fr) 170px; align-items: center; gap: 32px; max-width: 900px; margin: 0 auto 34px; text-align: left; }
        .attendance-copy { max-width: 620px; }
        .attendance-kicker { color: #1e7f5c; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; font-size: 12px; }
        .attendance-intro h1 { margin: 8px 0 12px; color: #0b2f23; font-size: clamp(30px, 5vw, 48px); line-height: 1.1; }
        .attendance-intro p { color: #475569; margin: 0; max-width: 560px; }
        .attendance-illustration { position: relative; display: grid; place-items: center; width: 150px; height: 150px; margin-left: auto; border-radius: 42% 58% 55% 45%; background: #dcefe6; color: #1e7f5c; transform: rotate(6deg); }
        .attendance-illustration::after { content: ''; position: absolute; inset: 17px; border: 1px dashed rgba(30, 127, 92, .35); border-radius: 45% 55% 48% 52%; }
        .attendance-illustration .material-symbols-outlined { position: relative; z-index: 1; font-size: 64px; transform: rotate(-6deg); }
        .event-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; max-width: 900px; margin: auto; }
        .event-card { background: #fff; border: 1px solid rgba(15, 23, 42, .1); border-radius: 12px; padding: 22px; box-shadow: 0 10px 30px rgba(2, 6, 23, .08); transition: transform .2s, box-shadow .2s; }
        .event-card:hover { transform: translateY(-3px); box-shadow: 0 16px 34px rgba(2, 6, 23, .12); }
        .event-card h2 { margin: 0 0 12px; color: #0b2f23; font-size: 20px; }
        .event-meta { color: #64748b; font-size: 14px; margin: 5px 0; }
        .event-card .btn { margin-top: 14px; background: #1e7f5c; color: #fff; }
        .event-card .btn::after {  font-family: 'Material Symbols Outlined'; font-size: 18px; }
        .attendance-empty { text-align: center; color: #64748b; padding: 28px; }
        @media (max-width: 620px) { .attendance-intro { grid-template-columns: 1fr; gap: 18px; text-align: center; } .attendance-copy { margin: auto; } .attendance-illustration { width: 100px; height: 100px; margin: auto; order: -1; } .attendance-illustration .material-symbols-outlined { font-size: 46px; } }
    </style>
@endpush

@section('content')
    <x-landing-header />
    <main class="attendance-page">
        <div class="container">
            <div class="attendance-intro">
                <div class="attendance-copy">
                    <div class="attendance-kicker">Layanan Peserta</div>
                    <h1>Hadir di kegiatan hari ini?</h1>
                    <p>Pilih event yang diikuti, lalu masukkan nomor WhatsApp wali untuk menemukan data calon santri.</p>
                </div>
                <div class="attendance-illustration" aria-hidden="true"><span class="material-symbols-outlined">how_to_reg</span></div>
            </div>

            @if ($events->isEmpty())
                <div class="attendance-empty">Belum ada kegiatan yang dibuka untuk absensi.</div>
            @else
                <div class="event-grid">
                    @foreach ($events as $event)
                        <article class="event-card">
                            <h2>{{ $event->name }}</h2>
                            <div class="event-meta">Tanggal: {{ $event->event_date?->format('d M Y') ?? 'Menyesuaikan' }}</div>
                            <div class="event-meta">Lokasi: {{ $event->location ?: 'Akan diinformasikan' }}</div>
                            <a class="btn" href="{{ route('attendance.event', $event) }}">Buka Absensi</a>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </main>
    <x-landing-footer />
@endsection
