@extends('welcome')

@section('title', 'Absensi ' . $event->name . ' - Ma\'had Darussalam Al-Islami')

@push('styles')
    <style>
        .attendance-page { min-height: 72vh; padding: 112px 0 76px; background: linear-gradient(145deg, #f4f8f5 0%, #fff 68%); }
        .attendance-wrap { max-width: 820px; margin: auto; }
        .attendance-panel { background: #fff; border: 1px solid rgba(15, 23, 42, .1); border-radius: 16px; padding: clamp(22px, 5vw, 38px); box-shadow: 0 14px 36px rgba(2, 6, 23, .1); }
        .attendance-panel-header { display: flex; align-items: flex-start; gap: 16px; margin-bottom: 26px; }
        .attendance-panel-icon { display: grid; place-items: center; width: 52px; height: 52px; flex: 0 0 52px; border-radius: 14px; background: #e8f5f0; color: #1e7f5c; }
        .attendance-panel-icon .material-symbols-outlined { font-size: 29px; }
        .attendance-panel h1 { margin: 0 0 8px; color: #0b2f23; font-size: clamp(26px, 5vw, 38px); }
        .attendance-event-meta { color: #64748b; font-size: 14px; margin-bottom: 26px; }
        .attendance-event-meta span { margin-right: 14px; }
        .attendance-label { display: block; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
        .attendance-input { width: 100%; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font: inherit; }
        .attendance-input:focus { outline: 3px solid rgba(30, 127, 92, .18); border-color: #1e7f5c; }
        .attendance-button { display: inline-flex; align-items: center; gap: 8px; border: 0; border-radius: 8px; padding: 12px 18px; margin-top: 14px; background: #1e7f5c; color: #fff; font-weight: 700; cursor: pointer; }
        .attendance-button::after {  font-family: 'Material Symbols Outlined'; font-size: 18px; }
        .attendance-alert { padding: 12px 14px; border-radius: 8px; margin-bottom: 18px; background: #e8f5f0; color: #0f5e42; }
        .attendance-error { color: #b42318; font-size: 14px; margin-top: 7px; }
        .participant-heading { display: flex; align-items: end; justify-content: space-between; gap: 14px; margin: 28px 0 12px; }
        .participant-heading h2 { margin: 0; color: #0b2f23; font-size: 20px; }
        .participant-heading p { margin: 0; color: #64748b; font-size: 13px; }
        .participant-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(290px, 1fr)); gap: 12px; }
        .participant-option { position: relative; display: flex; gap: 13px; align-items: flex-start; min-height: 160px; padding: 18px; border: 1px solid #dbe4df; border-radius: 12px; background: #fff; cursor: pointer; transition: border-color .2s, background .2s, transform .2s; }
        .participant-option:hover { border-color: #79b69c; transform: translateY(-2px); }
        .participant-option:has(input:checked) { border-color: #1e7f5c; background: #f0faf5; box-shadow: 0 8px 20px rgba(30, 127, 92, .1); }
        .participant-option input { width: 18px; height: 18px; margin: 3px 0 0; accent-color: #1e7f5c; }
        .participant-icon { display: grid; place-items: center; width: 42px; height: 42px; flex: 0 0 42px; border-radius: 10px; background: #e8f5f0; color: #1e7f5c; }
        .participant-icon .material-symbols-outlined { font-size: 23px; }
        .participant-content { min-width: 0; }
        .participant-name { display: block; font-weight: 800; color: #0b2f23; font-size: 16px; line-height: 1.25; }
        .participant-info { display: flex; align-items: center; gap: 5px; color: #64748b; font-size: 13px; margin-top: 9px; }
        .participant-info .material-symbols-outlined { color: #1e7f5c; font-size: 17px; }
        .participant-status { display: inline-flex; align-items: center; gap: 4px; margin-top: 12px; padding: 4px 8px; border-radius: 999px; background: #eef2f0; color: #64748b; font-size: 12px; font-weight: 700; }
        .participant-status .material-symbols-outlined { font-size: 15px; }
        .back-link { display: inline-block; margin-top: 18px; color: #1e7f5c; font-weight: 700; font-size: 14px; }
        .attendance-note { display: flex; align-items: flex-start; gap: 8px; margin-top: 12px; color: #64748b; font-size: 13px; }
        .attendance-note .material-symbols-outlined { color: #1e7f5c; font-size: 18px; }
        @media (max-width: 520px) { .attendance-event-meta span { display: block; margin: 4px 0; } .participant-heading { align-items: flex-start; flex-direction: column; } }
    </style>
@endpush

@section('content')
    <x-landing-header />
    <main class="attendance-page">
        <div class="container attendance-wrap">
            <section class="attendance-panel">
                <div class="attendance-panel-header">
                    <div class="attendance-panel-icon" aria-hidden="true"><span class="material-symbols-outlined">event_available</span></div>
                    <div>
                        <h1>{{ $event->name }}</h1>
                        <div class="attendance-event-meta">
                            <span>Tanggal: {{ $event->event_date?->format('d M Y') ?? 'Menyesuaikan' }}</span>
                            <span>Lokasi: {{ $event->location ?: 'Akan diinformasikan' }}</span>
                        </div>
                    </div>
                </div>

                @if (session('attendance_status'))
                    <div class="attendance-alert">{{ session('attendance_status') }}</div>
                @endif

                <form method="POST" action="{{ route('attendance.lookup', $event) }}">
                    @csrf
                    <label class="attendance-label" for="phone">No. WhatsApp Wali Santri</label>
                    <input class="attendance-input" id="phone" name="phone" type="tel" value="{{ old('phone', $phone) }}"
                        placeholder="Contoh: 081234567890" required autocomplete="tel">
                    <div class="attendance-note"><span class="material-symbols-outlined">lock</span><span>Nomor ini hanya digunakan untuk mencocokkan data peserta pada event ini.</span></div>
                    @error('phone') <div class="attendance-error">{{ $message }}</div> @enderror
                    <button class="attendance-button" type="submit">Tampilkan Data Peserta</button>
                </form>

                @if ($phone !== null)
                    @if ($participants->isEmpty())
                        <div class="attendance-error" style="margin-top: 22px;">Data peserta dengan nomor WhatsApp tersebut tidak ditemukan.</div>
                    @else
                        <form method="POST" action="{{ route('attendance.store', $event) }}">
                            @csrf
                            <input type="hidden" name="phone" value="{{ $phone }}">
                            <div class="participant-heading">
                                <div>
                                    <h2>Data Calon Santri</h2>
                                    <p>Pilih satu atau lebih peserta yang hadir.</p>
                                </div>
                                <p>{{ $participants->count() }} peserta ditemukan</p>
                            </div>
                            <div class="participant-list">
                                @foreach ($participants as $participant)
                                    @php
                                        $name = $participant->studentProfile?->full_name ?? $participant->santriContinuation?->full_name ?? '-';
                                        $alreadyPresent = $participant->eventAttendances->isNotEmpty();
                                    @endphp
                                    <label class="participant-option">
                                        <input type="checkbox" name="registration_ids[]" value="{{ $participant->id }}" @disabled($alreadyPresent)>
                                        <span class="participant-icon"><span class="material-symbols-outlined">person</span></span>
                                        <span class="participant-content">
                                            <span class="participant-name">{{ $name }}</span>
                                            <span class="participant-info"><span class="material-symbols-outlined">badge</span>{{ $participant->registration_no }}</span>
                                            <span class="participant-info"><span class="material-symbols-outlined">school</span>{{ $participant->studentProfile?->school_origin ?: 'Asal sekolah belum diisi' }}</span>
                                            <span class="participant-info"><span class="material-symbols-outlined">menu_book</span>{{ $participant->gender_label }} · {{ $participant->education_level }}</span>
                                            @if ($alreadyPresent)
                                                <span class="participant-status"><span class="material-symbols-outlined">check_circle</span>Sudah tercatat hadir</span>
                                            @endif
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @error('registration_ids') <div class="attendance-error">{{ $message }}</div> @enderror
                            <button class="attendance-button" type="submit">Catat Kehadiran</button>
                        </form>
                    @endif
                @endif
                <a class="back-link" href="{{ route('attendance.index') }}">← Pilih event lain</a>
            </section>
        </div>
    </main>
    <x-landing-footer />
@endsection
