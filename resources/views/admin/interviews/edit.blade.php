@extends('layouts.app')
@section('title', 'Wawancara PPDB')
@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h4 class="mb-1">Wawancara PPDB</h4>
            <div>{{ $registration->studentProfile?->full_name ?? $registration->santriContinuation?->full_name ?? '-' }} · {{ $registration->registration_no }}</div>
        </div>
        <a href="{{ route('admin.registrations.show', $registration) }}" class="btn btn-outline-secondary">Kembali ke Detail Santri</a>
    </div>
    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <div class="card trezo-card mb-3"><div class="card-body">
        <h5>Hasil Wawancara Tersimpan</h5>
        <div class="d-flex gap-3 flex-wrap">
            <span>Nilai dasar: <strong>{{ $interview?->base_score ?? '-' }}</strong></span>
            <span>Bonus: <strong>+{{ $interview?->bonus_points ?? 0 }}</strong></span>
            <span>Nilai akhir: <strong>{{ $interview?->total_score ?? '-' }}</strong></span>
            <strong>{{ $interview?->recommendation_label ?? 'Belum dinilai' }}</strong>
            @if ($interview?->has_relative)<span class="badge bg-info text-dark">Ada saudara di Darussalam</span>@endif
        </div>
        @if ($interview?->disqualification_reasons)
            <ul class="text-danger mt-2 mb-0">@foreach ($interview->disqualification_reasons as $reason)<li>{{ $reason }}</li>@endforeach</ul>
        @endif
        @if ($interview)
            <div class="small text-muted mt-2">Terakhir disimpan oleh {{ $interview->examiner?->name ?? '-' }} · {{ $interview->updated_at?->format('d/m/Y H:i') }}</div>
        @endif
    </div></div>
    <form method="POST" action="{{ route('admin.interviews.save', $registration) }}">
        @csrf
        <div class="card trezo-card mb-3"><div class="card-body">
            <p class="small text-muted">A = 3, B = 2, C = 1. Nilai dasar = jumlah poin ÷ poin maksimal × 100. Nilai akhir = nilai dasar + bonus, maksimal 105. Di atas 70 sangat direkomendasikan; 70 ke bawah direkomendasikan. Jawaban C pada pertanyaan bertanda khusus mengesampingkan nilai dan bonus.</p>
            <input type="hidden" name="distant_city_bonus" value="0">
            <div class="form-check mb-3">
                <input type="checkbox" id="distant-city" name="distant_city_bonus" value="1" class="form-check-input" @checked(old('distant_city_bonus', $interview?->distant_city_bonus ?? false))>
                <label class="form-check-label" for="distant-city">Berikan bonus +5 untuk santri dari luar kota yang jauh</label>
            </div>
            <label for="relative-details" class="form-label">Nama saudara / hubungan / kelas di Darussalam</label>
            <input id="relative-details" name="relative_details" class="form-control" maxlength="255" value="{{ old('relative_details', $interview?->relative_details) }}">
            <div class="form-text">Flag saudara otomatis aktif jika jawaban pertanyaan saudara pada bagian anak adalah A atau B. Keterangan ini disimpan jika flag aktif.</div>
        </div></div>
        @foreach ($sections as $section => $group)
            <div class="card trezo-card mb-3"><div class="card-body">
                <h5>Wawancara {{ $group['label'] }}</h5>
                <div class="table-responsive">
                    <table class="table align-middle" style="min-width: 600px">
                        <thead><tr><th style="width: 40%">Pertanyaan</th><th>Jawaban / Nilai</th></tr></thead>
                        <tbody>
                            @foreach ($group['questions'] as $key => $question)
                                @php $value = old("answers.$section.$key", $interview?->answers[$section][$key] ?? null); @endphp
                                <tr>
                                    <td><span id="question-{{ $section }}-{{ $key }}">{{ $loop->iteration }}. {{ $question['label'] }}</span>
                                        @if ($question['reject_c'] ?? false)<div class="small text-danger">C: otomatis tidak direkomendasikan</div>@endif
                                    </td>
                                    <td>
                                        <div role="radiogroup" aria-labelledby="question-{{ $section }}-{{ $key }}">
                                            <div class="form-check mb-2">
                                                <input type="radio" id="answer-{{ $section }}-{{ $key }}-empty" name="answers[{{ $section }}][{{ $key }}]" value="" class="form-check-input" @checked($value === null || $value === '')>
                                                <label class="form-check-label text-muted" for="answer-{{ $section }}-{{ $key }}-empty">Belum dijawab</label>
                                            </div>
                                            @foreach ($question['options'] as $grade => $label)
                                                <div class="form-check mb-2">
                                                    <input type="radio" id="answer-{{ $section }}-{{ $key }}-{{ $grade }}" name="answers[{{ $section }}][{{ $key }}]" value="{{ $grade }}" class="form-check-input" @checked($value === $grade)>
                                                    <label class="form-check-label" for="answer-{{ $section }}-{{ $key }}-{{ $grade }}"><strong>{{ $grade }} ({{ config('ppdb_interview.weights')[$grade] }})</strong> — {{ $label }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error("answers.$section.$key")<div class="text-danger small">{{ $message }}</div>@enderror
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div></div>
        @endforeach
        <div class="card trezo-card mb-3"><div class="card-body">
            <label for="interview-notes" class="form-label">Catatan Wawancara</label>
            <textarea id="interview-notes" name="notes" rows="4" maxlength="4000" class="form-control">{{ old('notes', $interview?->notes) }}</textarea>
            <div class="form-text mb-3">Jawaban belum lengkap dapat disimpan. Nilai akhir dihitung setelah seluruh pertanyaan terisi. Hasil ini merupakan rekomendasi wawancara; penetapan kelulusan tetap melalui hasil seleksi.</div>
            <button class="btn btn-primary">Simpan Wawancara</button>
        </div></div>
    </form>
@endsection
