@extends('layouts.app')

@section('title', 'Input Nilai Seleksi')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.min.css">
    <style>
        .assessment-group { border: 1px solid var(--trezo-border); border-radius: 14px; overflow: hidden; }
        .assessment-group + .assessment-group { margin-top: 1.25rem; }
        .assessment-group-header { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1rem 1.25rem; background: #f6faf7; border-bottom: 1px solid var(--trezo-border); }
        .assessment-group-header h5 { margin: 0; font-size: 1rem; }
        .assessment-group-header small { color: #6b7f75; }
        .assessment-table th { white-space: nowrap; }
        .assessment-table td { vertical-align: middle; }
        .assessment-table .form-control, .assessment-table .form-select { min-width: 82px; }
        .dataTables_wrapper { padding: 1rem 1.25rem; }
        .dataTables_wrapper .row { align-items: center; gap: .5rem 0; }
        @media (max-width: 767.98px) {
            .assessment-group-header { align-items: flex-start; flex-direction: column; }
            .dataTables_wrapper { padding: .75rem; }
        }
    </style>
@endpush

@section('content')
    @php
        $scoreFields = [
            'oral_question_1' => 'Soal 1',
            'oral_question_2' => 'Soal 2',
            'oral_question_3' => 'Soal 3',
            'arabic_score' => 'Bahasa Arab',
            'tpa_score' => 'TPA',
        ];
        $scoreAverages = collect($scoreFields)->mapWithKeys(function ($label, $field) use ($registrations) {
            $values = $registrations->pluck($field)->filter(fn ($value) => $value !== null && $value !== '')->map(fn ($value) => (float) $value);
            return [$field => ['label' => $label, 'average' => $values->avg(), 'count' => $values->count()]];
        });
        $rankingAverages = $registrations
            ->mapWithKeys(function ($registration) use ($scoreFields) {
                $scores = collect(array_keys($scoreFields))
                    ->map(fn ($field) => $registration->{$field})
                    ->map(fn ($value) => $value === null || $value === '' ? 0.0 : (float) $value);

                return [$registration->id => round((float) $scores->sum() / 5, 2)];
            })
            ->sortByDesc(fn ($average) => $average ?? -1);
        $rankingByRegistration = [];
        $rankingPosition = 0;
        foreach ($rankingAverages as $registrationId => $average) {
            $rankingByRegistration[$registrationId] = $average > 0 ? ++$rankingPosition : null;
        }
        $allScores = $registrations->flatMap(fn ($registration) => collect(array_keys($scoreFields))->map(fn ($field) => $registration->{$field})->filter(fn ($value) => $value !== null && $value !== '')->map(fn ($value) => (float) $value));
        $assessmentGroups = [
            ['key' => 'ikhwan-smp', 'label' => 'Ikhwan - SMP', 'gender' => 'male', 'levels' => ['SMP_NEW']],
            ['key' => 'ikhwan-sma', 'label' => 'Ikhwan - SMA', 'gender' => 'male', 'levels' => ['SMA_NEW', 'SMA_OLD']],
            ['key' => 'akhwat-smp', 'label' => 'Akhwat - SMP', 'gender' => 'female', 'levels' => ['SMP_NEW']],
            ['key' => 'akhwat-sma', 'label' => 'Akhwat - SMA', 'gender' => 'female', 'levels' => ['SMA_NEW', 'SMA_OLD']],
            ['key' => 'belum-smp', 'label' => 'Belum Ditentukan - SMP', 'gender' => null, 'levels' => ['SMP_NEW']],
            ['key' => 'belum-sma', 'label' => 'Belum Ditentukan - SMA', 'gender' => null, 'levels' => ['SMA_NEW', 'SMA_OLD']],
        ];
        $assessmentGroups = collect($assessmentGroups)->map(function (array $group) use ($registrations, $scoreFields) {
            $items = $registrations
                ->filter(fn ($registration) => ($registration->gender === $group['gender']) && in_array($registration->education_level, $group['levels'], true))
                ->map(function ($registration) use ($scoreFields) {
                    $scores = collect(array_keys($scoreFields))
                        ->map(fn ($field) => $registration->{$field})
                        ->map(fn ($value) => $value === null || $value === '' ? 0.0 : (float) $value);

                    $registration->ranking_average = round((float) $scores->sum() / 5, 2);
                    return $registration;
                })
                ->sortByDesc(fn ($registration) => $registration->ranking_average ?? -1)
                ->values();

            $rank = 0;
            $items = $items->map(function ($registration) use (&$rank) {
                $registration->ranking_position = $registration->ranking_average > 0 ? ++$rank : null;
                return $registration;
            });

            $group['registrations'] = $items;
            return $group;
        });
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
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('admin.registrations.assessments.import') }}" enctype="multipart/form-data" class="d-flex gap-2">
                @csrf
                <input type="file" name="assessment_file" class="form-control form-control-sm" accept=".csv,.txt" required>
                <button class="btn btn-outline-success btn-sm" type="submit"><i class="material-symbols-outlined">upload_file</i> Import Excel</button>
            </form>
            <a href="{{ route('admin.registrations.assessments.export', request()->query()) }}" class="btn btn-success btn-sm"><i class="material-symbols-outlined">download</i> Export Excel</a>
        </div>
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

    <div class="card trezo-card mb-3">
        <div class="card-body">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="material-symbols-outlined text-success">analytics</i>
                <h5 class="mb-0">Rata-rata Nilai</h5>
            </div>
            <p class="text-muted small mb-3">Berdasarkan nilai yang sudah diinput pada hasil filter ini.</p>

            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <div class="border rounded-3 p-3 bg-light h-100">
                        <div class="text-muted small">Rata-rata keseluruhan</div>
                        <div class="display-6 fw-semibold text-success">{{ $allScores->count() ? number_format($allScores->avg(), 2, ',', '.') : '-' }}</div>
                        <div class="text-muted small">{{ $allScores->count() }} nilai terisi</div>
                    </div>
                </div>
                @foreach ($scoreAverages as $score)
                    <div class="col-lg-2 col-md-3 col-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted small">{{ $score['label'] }}</div>
                            <div class="fs-4 fw-semibold">{{ $score['count'] ? number_format($score['average'], 2, ',', '.') : '-' }}</div>
                            <div class="text-muted small">{{ $score['count'] }} nilai terisi</div>
                        </div>
                    </div>
                @endforeach
                <div class="col-lg-3 col-md-6">
                    <div class="border rounded-3 p-3 h-100">
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

    <form method="POST" action="{{ route('admin.registrations.assessments.save') }}" id="assessment-form">
        @csrf
        @foreach ($assessmentGroups as $group)
            <section class="assessment-group bg-white" data-group="{{ $group['key'] }}">
                <div class="assessment-group-header">
                    <div>
                        <h5>{{ $group['label'] }}</h5>
                        <small>{{ $group['registrations']->count() }} pendaftar, diurutkan berdasarkan ranking nilai</small>
                    </div>
                    <span class="badge bg-light text-success">Ranking {{ $group['label'] }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 assessment-table" id="table-{{ $group['key'] }}" style="min-width: 1480px">
                        <thead>
                            <tr>
                                <th>Ranking</th><th>No. Pendaftar</th><th>Nama Santri</th><th>Bahasa Arab</th><th>TPA</th>
                                <th>Wawancara</th><th>Soal 1</th><th>Soal 2</th><th>Soal 3</th><th>Status Tahsin</th>
                                <th>Rata-rata</th><th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($group['registrations'] as $registration)
                                @php $studentName = $registration->studentProfile?->full_name ?? $registration->santriContinuation?->full_name ?? '-'; @endphp
                                <tr>
                                    <td data-order="{{ $registration->ranking_position ?? 999999 }}">
                                        @if ($registration->ranking_position)
                                            <span class="badge bg-success">#{{ $registration->ranking_position }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td><a href="{{ route('admin.registrations.show', $registration) }}" class="fw-semibold">{{ $registration->registration_no }}</a></td>
                                    <td class="fw-semibold">{{ $studentName }}</td>
                                    <td><input type="number" name="assessments[{{ $registration->id }}][arabic_score]" class="form-control form-control-sm" min="0" max="100" step="0.01" value="{{ $registration->arabic_score }}" placeholder="0-100"></td>
                                    <td><input type="number" name="assessments[{{ $registration->id }}][tpa_score]" class="form-control form-control-sm" min="0" max="100" step="0.01" value="{{ $registration->tpa_score }}" placeholder="0-100"></td>
                                    <td><select name="assessments[{{ $registration->id }}][interview_recommendation]" class="form-select form-select-sm" required>@foreach (['sangat_direkomendasikan' => 'Sangat Direkomendasikan', 'direkomendasikan' => 'Direkomendasikan', 'tidak_direkomendasikan' => 'Tidak Direkomendasikan'] as $key => $label)<option value="{{ $key }}" @selected(($registration->interview_recommendation ?? 'direkomendasikan') === $key)>{{ $label }}</option>@endforeach</select></td>
                                    <td><input type="number" name="assessments[{{ $registration->id }}][oral_question_1]" class="form-control form-control-sm" min="0" max="100" value="{{ $registration->oral_question_1 ?? 0 }}" placeholder="0"></td>
                                    <td><input type="number" name="assessments[{{ $registration->id }}][oral_question_2]" class="form-control form-control-sm" min="0" max="100" value="{{ $registration->oral_question_2 ?? 0 }}" placeholder="0"></td>
                                    <td><input type="number" name="assessments[{{ $registration->id }}][oral_question_3]" class="form-control form-control-sm" min="0" max="100" value="{{ $registration->oral_question_3 ?? 0 }}" placeholder="0"></td>
                                    <td><select name="assessments[{{ $registration->id }}][tahsin_status]" class="form-select form-select-sm">@foreach (['pending' => 'Pending', 'diterima' => 'Diterima', 'tidak_diterima' => 'Tidak Diterima'] as $key => $label)<option value="{{ $key }}" @selected(($registration->tahsin_status ?? 'pending') === $key)>{{ $label }}</option>@endforeach</select></td>
                                    <td class="fw-semibold text-success" data-order="{{ $registration->ranking_average ?? -1 }}">{{ $registration->ranking_average === null ? '-' : number_format($registration->ranking_average, 2, ',', '.') }}</td>
                                    <td><a href="{{ route('admin.registrations.show', $registration) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endforeach
        <div class="d-flex justify-content-between align-items-center gap-2 p-3 border-top">
            <span class="text-muted small">{{ $registrations->count() }} pendaftar ditampilkan. Perubahan pada semua baris akan disimpan sekaligus.</span>
            <button class="btn btn-primary" type="submit"><i class="material-symbols-outlined">save</i> Simpan Semua Nilai</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.assessment-table').forEach(function (table) {
                new DataTable(table, {
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Semua']],
                    order: [[0, 'asc']],
                    columnDefs: [{ targets: [11], orderable: false, searchable: false }],
                    language: {
                        search: 'Cari:',
                        lengthMenu: 'Tampilkan _MENU_',
                        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ pendaftar',
                        infoEmpty: 'Belum ada pendaftar',
                        zeroRecords: 'Pendaftar tidak ditemukan',
                        paginate: { first: 'Awal', last: 'Akhir', next: 'Berikutnya', previous: 'Sebelumnya' }
                    }
                });
            });
        });
    </script>
@endpush
