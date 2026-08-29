@extends('layouts.app')

@section('title', 'Input Nilai Seleksi')

@section('content')
    @php
        $scoreFields = [
            'tahfidz_score' => 'Tahfidz',
            'tajwid_score' => 'Tajwid',
            'arabic_score' => 'Bahasa Arab',
            'tpa_score' => 'TPA',
        ];
        $scoreAverages = collect($scoreFields)->mapWithKeys(function ($label, $field) use ($registrations) {
            $values = $registrations->pluck($field)->filter(fn ($value) => $value !== null && $value !== '')->map(fn ($value) => (float) $value);
            return [$field => ['label' => $label, 'average' => $values->avg(), 'count' => $values->count()]];
        });
        $rankingByRegistration = $registrations
            ->mapWithKeys(function ($registration) use ($scoreFields) {
                $scores = collect(array_keys($scoreFields))
                    ->map(fn ($field) => $registration->{$field})
                    ->filter(fn ($value) => $value !== null && $value !== '')
                    ->map(fn ($value) => (float) $value);

                return [$registration->id => $scores->count() ? $scores->avg() : null];
            })
            ->sortByDesc(fn ($average) => $average ?? -1)
            ->keys()
            ->values()
            ->mapWithKeys(fn ($registrationId, $position) => [$registrationId => $position + 1]);
        $allScores = $registrations->flatMap(fn ($registration) => collect(array_keys($scoreFields))->map(fn ($field) => $registration->{$field})->filter(fn ($value) => $value !== null && $value !== '')->map(fn ($value) => (float) $value));
    @endphp
    @if (session('success'))
        <div class="alert alert-success border-0 rounded-3">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 rounded-3">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h4 class="mb-1">Input Nilai Seleksi</h4>
            <div class="text-muted">Kelola nilai seluruh calon santri dari satu halaman.</div>
        </div>
        <a href="{{ route('admin.registrations.index') }}" class="btn btn-outline-light btn-sm">Data Pendaftar</a>
    </div>

    <div class="card trezo-card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label small fw-semibold">Cari Peserta</label>
                    <input name="search" value="{{ request('search') }}" class="form-control" placeholder="Nama atau nomor pendaftaran">
                </div>
                <div class="col-lg-2 col-md-3">
                    <label class="form-label small fw-semibold">Kelompok</label>
                    <select name="gender" class="form-select">
                        <option value="">Semua</option>
                        <option value="male" @selected(request('gender') === 'male')>Ikhwan</option>
                        <option value="female" @selected(request('gender') === 'female')>Akhwat</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-3">
                    <label class="form-label small fw-semibold">Jenjang</label>
                    <select name="education_level" class="form-select">
                        <option value="">Semua Jenjang</option>
                        <option value="SMP_NEW" @selected(request('education_level') === 'SMP_NEW')>SMP Baru</option>
                        <option value="SMA_NEW" @selected(request('education_level') === 'SMA_NEW')>SMA Baru</option>
                        <option value="SMA_OLD" @selected(request('education_level') === 'SMA_OLD')>SMA Lanjutan</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-6">
                    <button class="btn btn-primary w-100">Cari</button>
                </div>
                <div class="col-lg-2 col-md-6">
                    <a href="{{ route('admin.registrations.assessments') }}" class="btn btn-outline-light w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 align-items-start">
        <div class="col-xl-9">
            <div class="card trezo-card">
                <div class="card-body p-0">
            <form method="POST" action="{{ route('admin.registrations.assessments.save') }}">
                @csrf
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="min-width: 1480px">
                    <thead>
                        <tr>
                            <th class="ps-3">No. Pendaftar</th>
                            <th>Nama Santri</th>
                            <th>Jenjang</th>
                            <th>Kelompok</th>
                            <th>Tahfidz</th>
                            <th>Tajwid</th>
                            <th>Bahasa Arab</th>
                            <th>TPA</th>
                            <th>Wawancara</th>
                            <th>Catatan Lisan</th>
                            <th>Rata-rata</th>
                            <th>Ranking</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($registrations as $registration)
                            @php
                                $studentName = $registration->studentProfile?->full_name ?? $registration->santriContinuation?->full_name ?? '-';
                                $levelLabels = ['SMP_NEW' => 'SMP Baru', 'SMA_NEW' => 'SMA Baru', 'SMA_OLD' => 'SMA Lanjutan'];
                            @endphp
                            <tr>
                                <td class="ps-3"><a href="{{ route('admin.registrations.show', $registration) }}" class="fw-semibold">{{ $registration->registration_no }}</a></td>
                                <td class="fw-semibold">{{ $studentName }}</td>
                                <td>{{ $levelLabels[$registration->education_level] ?? $registration->education_level }}</td>
                                <td><span class="badge {{ $registration->gender === 'female' ? 'bg-warning text-dark' : 'bg-info' }}">{{ $registration->gender === 'female' ? 'Akhwat' : 'Ikhwan' }}</span></td>
                                @foreach (['tahfidz_score', 'tajwid_score', 'arabic_score', 'tpa_score'] as $field)
                                    <td><input type="number" name="assessments[{{ $registration->id }}][{{ $field }}]" class="form-control form-control-sm" min="0" max="100" step="0.01" value="{{ $registration->{$field} }}" placeholder="0-100" aria-label="{{ $field }}"></td>
                                @endforeach
                                    <td>
                                        <select name="assessments[{{ $registration->id }}][interview_recommendation]" class="form-select form-select-sm" required>
                                            @foreach (['sangat_direkomendasikan' => 'Sangat Direkomendasikan', 'direkomendasikan' => 'Direkomendasikan', 'tidak_direkomendasikan' => 'Tidak Direkomendasikan'] as $key => $label)
                                                <option value="{{ $key }}" @selected(($registration->interview_recommendation ?? 'direkomendasikan') === $key)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><textarea name="assessments[{{ $registration->id }}][oral_exam_notes]" class="form-control form-control-sm" rows="1" placeholder="Catatan...">{{ $registration->oral_exam_notes }}</textarea></td>
                                    @php
                                        $registrationAverage = $rankingByRegistration->has($registration->id)
                                            ? collect(array_keys($scoreFields))->map(fn ($field) => $registration->{$field})->filter(fn ($value) => $value !== null && $value !== '')->map(fn ($value) => (float) $value)->avg()
                                            : null;
                                        $registrationRank = $rankingByRegistration->get($registration->id);
                                    @endphp
                                    <td class="fw-semibold text-success">{{ $registrationAverage !== null ? number_format($registrationAverage, 2, ',', '.') : '-' }}</td>
                                    <td>
                                        @if ($registrationAverage !== null)
                                            <span class="badge bg-success">#{{ $registrationRank }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('admin.registrations.show', $registration) }}" class="btn btn-sm btn-outline-primary">
                                            Detail
                                        </a>
                                    </td>
                            </tr>
                        @empty
                            <tr><td colspan="13" class="text-center text-muted py-5">Tidak ada data pendaftar sesuai filter.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center gap-2 p-3 border-top">
                <span class="text-muted small">{{ $registrations->count() }} pendaftar ditampilkan. Perubahan pada semua baris akan disimpan sekaligus.</span>
                <button class="btn btn-primary" type="submit"><i class="material-symbols-outlined">save</i> Simpan Semua Nilai</button>
            </div>
            </form>
                </div>
            </div>
        </div>

        <div class="col-xl-3">
            <div class="card trezo-card sticky-xl-top" style="top: 20px;">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="material-symbols-outlined text-success">analytics</i>
                        <h5 class="mb-0">Rata-rata Nilai</h5>
                    </div>
                    <p class="text-muted small mb-3">Berdasarkan nilai yang sudah diinput pada hasil filter ini.</p>

                    <div class="border rounded-3 p-3 mb-3 bg-light">
                        <div class="text-muted small">Rata-rata keseluruhan</div>
                        <div class="display-6 fw-semibold text-success">{{ $allScores->count() ? number_format($allScores->avg(), 2, ',', '.') : '-' }}</div>
                        <div class="text-muted small">{{ $allScores->count() }} nilai terisi</div>
                    </div>

                    @foreach ($scoreAverages as $score)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted small">{{ $score['label'] }}</span>
                            <span class="fw-semibold">{{ $score['count'] ? number_format($score['average'], 2, ',', '.') : '-' }}</span>
                        </div>
                    @endforeach

                    <div class="mt-3">
                        <div class="text-muted small mb-2">Rekomendasi wawancara</div>
                        @foreach (['sangat_direkomendasikan' => 'Sangat Direkomendasikan', 'direkomendasikan' => 'Direkomendasikan', 'tidak_direkomendasikan' => 'Tidak Direkomendasikan'] as $key => $label)
                            <div class="d-flex justify-content-between small mb-1">
                                <span>{{ $label }}</span>
                                <span class="fw-semibold">{{ $registrations->where('interview_recommendation', $key)->count() }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection