@extends('layouts.app')
@section('title', 'Detail Pendaftar')

@php
    use Illuminate\Support\Str;

    $sp = $registration->studentProfile;
    $pp = $registration->parentProfile;
    $st = $registration->statement;
    $continuation = $registration->santriContinuation;

    // label map biar dokumen enak dibaca
    $docLabels = [
        'PAYMENT_PROOF' => 'Bukti Pembayaran Pendaftaran (Rp150.000)',
        'KK' => 'Kartu Keluarga (KK)',
        'BIRTH_CERT' => 'Akte Kelahiran',
        'KTP_FATHER' => 'KTP Ayah',
        'KTP_MOTHER' => 'KTP Ibu',
        'SKTM' => 'Surat Kurang Mampu (Beasiswa)',
        'GOOD_BEHAVIOR' => 'Surat Keterangan Berkelakuan Baik',
    ];

    $educationLabel = match ($registration->education_level) {
        'SMP_NEW' => 'SMP Baru',
        'SMA_NEW' => 'SMA Baru',
        'SMA_OLD' => 'SMA Lanjutan (Santri Lama)',
        default => 'Jenjang belum dipilih',
    };
    $registrationStatusLabel = match ($registration->status) {
        'draft' => 'Draft',
        'submitted' => 'Sudah Dikirim',
        'verified' => 'Terverifikasi',
        'revision_requested' => 'Perlu Perbaikan',
        default => ucfirst(str_replace('_', ' ', $registration->status ?? '-')),
    };
    $graduationLabel = match ($registration->graduation_status) {
        'lulus' => 'Lulus',
        'tidak_lulus' => 'Tidak Lulus',
        'cadangan' => 'Cadangan',
        default => 'Menunggu Penilaian',
    };
    if ($registration->graduation_status === 'lulus') {
        $graduationLabel = match ($registration->admission_decision) {
            'regular' => 'Lulus Reguler',
            'takhosus' => 'Lulus Takhosus',
            'scholarship' => 'Lulus Beasiswa',
            default => $graduationLabel,
        };
    }
    $graduationClass = match ($registration->graduation_status) {
        'lulus' => 'is-success',
        'tidak_lulus' => 'is-danger',
        'cadangan' => 'is-warning',
        default => 'is-info',
    };
    $studentName = $sp?->full_name ?? $continuation?->full_name ?? 'Nama santri belum tersedia';
    $documentTotal = $registration->documents->count();
    $documentUploaded = $registration->documents->whereNotNull('file_path')->count();
    $documentPercent = $documentTotal ? intval(($documentUploaded / $documentTotal) * 100) : 0;
    $auditFieldLabels = [
        'admission_decision' => 'Keputusan kelulusan PPDB',
        'question_1_grade' => 'Tahsin lisan soal 1',
        'question_2_grade' => 'Tahsin lisan soal 2',
        'question_3_grade' => 'Tahsin lisan soal 3',
        'notes' => 'Catatan ujian tahsin',
        'graduation_status' => 'Status kelulusan',
        'admin_note' => 'Catatan admin',
        'oral_question_1' => 'Nilai lisan soal 1',
        'oral_question_2' => 'Nilai lisan soal 2',
        'oral_question_3' => 'Nilai lisan soal 3',
        'oral_exam_notes' => 'Catatan tes lisan',
        'tahsin_status' => 'Status tahsin',
        'tahfidz_score' => 'Nilai tahfidz',
        'tajwid_score' => 'Nilai tajwid',
        'arabic_score' => 'Nilai Bahasa Arab',
        'tpa_score' => 'Nilai TPA',
        'interview_recommendation' => 'Rekomendasi wawancara',
        'funding_type' => 'Jenis pembiayaan',
        'education_level' => 'Jenjang pendidikan',
        'gender' => 'Jenis kelamin',
        'status' => 'Status pendaftaran',
    ];
@endphp

@push('styles')
    <style>
        .registration-detail-page {
            --scan-ink: #16352a;
            --scan-muted: #6b7f75;
            --scan-line: #dce9e1;
            --scan-gold: #c9a24d;
        }

        .registration-hero {
            position: relative;
            overflow: hidden;
            color: #fff;
            border-radius: 18px;
            background: linear-gradient(120deg, #103c2c 0%, #176044 60%, #287b58 100%);
            box-shadow: 0 16px 32px rgba(16, 60, 44, .16);
        }

        .registration-hero::after {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            right: -70px;
            top: -110px;
            border: 1px solid rgba(255, 255, 255, .17);
            border-radius: 50%;
            box-shadow: 0 0 0 24px rgba(255, 255, 255, .04), 0 0 0 48px rgba(255, 255, 255, .03);
        }

        .registration-hero-content { position: relative; z-index: 1; }
        .registration-kicker { color: #f5d98f; font-size: .74rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        .registration-hero h1 { max-width: 760px; font-size: clamp(1.7rem, 3vw, 2.5rem); letter-spacing: 0; }
        .registration-number { color: rgba(255, 255, 255, .82); font-size: .9rem; }
        .registration-actions { display: flex; align-items: flex-end; flex-wrap: wrap; gap: 20px; width: 100%; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, .18); }
        .registration-action-group { display: flex; flex-direction: column; gap: 8px; }
        .registration-action-label { color: rgba(255, 255, 255, .72); font-size: .7rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; }
        .registration-action-buttons { display: flex; flex-wrap: wrap; gap: 8px; }
        .registration-actions .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 42px; padding: 10px 14px; border-radius: 9px; font-size: .82rem; font-weight: 600; line-height: 1.3; transition: background .15s, border-color .15s; }
        .registration-actions .btn i { font-size: 18px; line-height: 1; }
        .registration-actions .action-back { margin-right: auto; color: #fff; border: 1px solid transparent; background: transparent; }
        .registration-actions .action-back:hover { background: rgba(255, 255, 255, .12); }
        .registration-actions .action-data { color: #fff; border: 1px solid rgba(255, 255, 255, .35); background: rgba(255, 255, 255, .06); }
        .registration-actions .action-data:hover { color: #fff; background: rgba(255, 255, 255, .16); border-color: #fff; }
        .registration-actions .action-assessment { color: #173f2e; background: #f2d58a; border: 1px solid #f2d58a; }
        .registration-actions .action-assessment:hover { color: #173f2e; background: #ffe6a7; border-color: #ffe6a7; }
        .registration-actions .btn:focus-visible { outline: 3px solid #fff; outline-offset: 3px; }
        .registration-action-group + .registration-action-group { border-left: 1px solid rgba(255, 255, 255, .18); padding-left: 20px; }
        .registration-summary { margin-top: -22px; position: relative; z-index: 2; }
        .scan-summary-card { height: 100%; border: 1px solid var(--scan-line); border-radius: 14px; background: #fff; box-shadow: 0 8px 22px rgba(22, 53, 42, .06); }
        .scan-summary-card .card-body { padding: 1.15rem; }
        .scan-summary-label { color: var(--scan-muted); font-size: .75rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
        .scan-summary-value { color: var(--scan-ink); font-size: 1.08rem; font-weight: 700; line-height: 1.35; }
        .scan-status { display: inline-flex; align-items: center; gap: .45rem; padding: .38rem .65rem; border-radius: 999px; font-size: .78rem; font-weight: 700; }
        .scan-status::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
        .scan-status.is-success { color: #167345; background: #e7f6ed; }
        .scan-status.is-danger { color: #b42318; background: #fff0ef; }
        .scan-status.is-warning { color: #8a6100; background: #fff7dc; }
        .scan-status.is-info { color: #17627a; background: #e8f5fa; }
        .scan-progress { height: 7px; background: #edf3ef; }
        .scan-progress .progress-bar { background: linear-gradient(90deg, #1c7a50, #c9a24d); }
        .registration-detail-page .trezo-card { border-radius: 14px; }
        .audit-item { position: relative; padding-left: 1.4rem; border-left: 2px solid #dce9e1; }
        .audit-item::before { content: ''; position: absolute; width: 9px; height: 9px; left: -5px; top: .35rem; border-radius: 50%; background: #1c7a50; box-shadow: 0 0 0 4px #e7f6ed; }
        .audit-item + .audit-item { margin-top: 1.15rem; }
        .audit-change { color: #53685d; font-size: .8rem; }
        @media (max-width: 575.98px) {
            .registration-hero { border-radius: 12px; }
            .registration-summary { margin-top: -10px; }
            .registration-actions { gap: 16px; }
            .registration-action-group { width: 100%; }
            .registration-action-group + .registration-action-group { border-left: 0; padding-left: 0; }
            .registration-action-buttons { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .registration-actions .btn { padding: 10px; }
        }
    </style>
@endpush

@section('content')
    <div class="registration-detail-page">
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="registration-hero p-4 p-lg-5 mb-4">
        <div class="registration-hero-content d-flex flex-wrap justify-content-between align-items-end gap-4">
            <div>
                <div class="registration-kicker mb-2">Hasil Scan Pendaftaran</div>
                <h1 class="registration-kicker mb-2">{{ $studentName }}</h1>
                <div class="registration-number">Nomor pendaftaran <strong>{{ $registration->registration_no }}</strong></div>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <span class="badge bg-success text-white">{{ $educationLabel }}</span>
                    <span class="badge bg-success text-white">Pembiayaan {{ $registration->funding_type_label }}</span>
                    <span class="badge bg-success text-white">{{ $registration->gender_label }}</span>
                </div>
            </div>
            <div class="registration-actions" aria-label="Aksi santri">
                <a href="{{ route('admin.registrations.index') }}" class="btn action-back"><i class="ri-arrow-left-line" aria-hidden="true"></i><span>Kembali</span></a>
                <div class="registration-action-group" role="group" aria-label="Data Santri">
                    <span class="registration-action-label">Data Santri</span>
                    <div class="registration-action-buttons">
                        <a href="{{ route('admin.registrations.proof.pdf', $registration) }}" class="btn action-data"><i class="ri-file-pdf-line" aria-hidden="true"></i><span>Download PDF</span></a>
                        <a href="{{ route('admin.registrations.edit', $registration) }}" class="btn action-data"><i class="ri-edit-line" aria-hidden="true"></i><span>Edit Data</span></a>
                    </div>
                </div>
                @if (in_array(auth()->user()->role, ['admin', 'ustadz'], true))
                    <div class="registration-action-group" role="group" aria-label="Penilaian">
                        <span class="registration-action-label">Penilaian</span>
                        <div class="registration-action-buttons">
                            <button type="button" class="btn action-assessment" data-bs-toggle="modal" data-bs-target="#modalUjianTahsin"><i class="ri-mic-line" aria-hidden="true"></i><span>Input Ujian Tahsin</span></button>
                            <a href="{{ route('admin.interviews.edit', $registration) }}" class="btn action-assessment"><i class="ri-chat-check-line" aria-hidden="true"></i><span>Input Wawancara</span></a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if ($registration->interview)
        <div class="card trezo-card mb-4"><div class="card-body">
            <h5>Hasil Wawancara</h5>
            <span class="fw-semibold">{{ $registration->interview->recommendation_label }}</span>
            <span class="ms-2">Nilai akhir: {{ $registration->interview->total_score ?? '-' }} (bonus +{{ $registration->interview->bonus_points }})</span>
            @if ($registration->interview->has_relative)
                <span class="badge bg-info text-dark ms-2">Ada saudara di Darussalam</span>
                <div class="small mt-2">{{ $registration->interview->relative_details }}</div>
            @endif
            @foreach ($registration->interview->disqualification_reasons ?? [] as $reason)<div class="text-danger small mt-1">{{ $reason }}</div>@endforeach
        </div></div>
    @endif
    <div class="row g-3 registration-summary mb-4">
        <div class="col-md-4">
            <div class="scan-summary-card">
                <div class="card-body">
                    <div class="scan-summary-label mb-2">Status pendaftaran</div>
                    <div class="scan-summary-value mb-2">{{ $registrationStatusLabel }}</div>
                    <div class="small text-muted">Akun wali: {{ $registration->user->name ?? '-' }}</div>
                    <div class="small text-muted">{{ $registration->user->phone ?? 'Nomor telepon belum ada' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="scan-summary-card">
                <div class="card-body">
                    <div class="scan-summary-label mb-2">Hasil seleksi</div>
                    <button type="button" class="scan-status {{ $graduationClass }} border-0" data-bs-toggle="modal" data-bs-target="#modalKelulusanNote">
                        {{ $graduationLabel }}
                    </button>
                    <div class="small text-muted mt-2">Klik status untuk melihat dan memperbarui penilaian.</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="scan-summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="scan-summary-label">Kelengkapan berkas</div>
                        <strong class="text-success">{{ $documentPercent }}%</strong>
                    </div>
                    <div class="progress scan-progress mb-2">
                        <div class="progress-bar" role="progressbar" style="width: {{ $documentPercent }}%" aria-valuenow="{{ $documentPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="small text-muted">{{ $documentUploaded }} dari {{ $documentTotal }} dokumen sudah diunggah</div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Informasi Pendaftar</h4>
            <div class="text-muted small">Periksa detail berikut sebelum melakukan verifikasi atau penilaian.</div>
        </div>
        <div class="d-flex gap-2">
            @if (auth()->user()?->role === 'admin')
                <form method="POST" action="{{ route('admin.registrations.destroy', $registration) }}"
                    onsubmit="return confirm('Yakin hapus data pendaftaran ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm">Hapus Data</button>
                </form>
            @endif

        </div>
    </div>

    @if ($registration->education_level === 'SMA_OLD')
        <div class="card trezo-card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h5 class="mb-0">Formulir Lanjutan Santri Ulya</h5>
                    <a href="{{ route('admin.registrations.continuation.edit', $registration) }}"
                        class="btn btn-outline-primary btn-sm">Edit Form Lanjutan</a>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="text-muted small">Jenis Kelamin</div>
                        <div class="fw-semibold">{{ $registration->gender_label }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Kelas Terakhir</div>
                        <div class="fw-semibold">{{ $continuation?->last_class ?? 'IX Wustho' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Asrama</div>
                        <div class="fw-semibold">{{ $continuation?->dormitory ?? '-' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Pilihan Kasur/Lemari</div>
                        <div class="fw-semibold">
                            {{ $continuation?->bedding_option === 'buy' ? 'Membeli paket bedding' : ($continuation?->bedding_option === 'not_buy' ? 'Tidak membeli paket bedding' : '-') }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Ayah/Wali</div>
                        <div class="fw-semibold">{{ $continuation?->father_name ?? '-' }}</div>
                        <div class="text-muted">{{ $continuation?->father_phone ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Ibu/Wali</div>
                        <div class="fw-semibold">{{ $continuation?->mother_name ?? '-' }}</div>
                        <div class="text-muted">{{ $continuation?->mother_phone ?? '-' }}</div>
                    </div>
                    <div class="col-md-12">
                        <div class="text-muted small">Bukti Transfer Pendaftaran Rp150.000</div>
                        @if ($continuation?->payment_proof_path)
                            <a href="{{ asset('storage/' . $continuation->payment_proof_path) }}" target="_blank"
                                class="btn btn-outline-success btn-sm mt-1">Lihat Bukti Transfer</a>
                        @else
                            <div class="fw-semibold">Belum diunggah</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card trezo-card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h5 class="mb-1">Riwayat Aktivitas</h5>
                    <div class="text-muted small">Catatan siapa yang mengisi nilai atau memperbarui pendaftaran ini.</div>
                </div>
                <span class="badge bg-light text-success">{{ $registration->audits->count() }} aktivitas</span>
            </div>

            @forelse ($registration->audits as $audit)
                @php
                    $auditAction = match ($audit->action) {
                        'oral_exam_created' => 'Mengisi ujian tahsin lisan',
                        'interview_created' => 'Mengisi wawancara PPDB',
                        'interview_updated' => 'Memperbarui wawancara PPDB',
                        'oral_exam_updated' => 'Memperbarui ujian tahsin lisan',
                        'assessment_updated' => 'Memperbarui nilai / hasil seleksi',
                        default => 'Memperbarui data pendaftaran',
                    };
                @endphp
                <div class="audit-item">
                    <div class="d-flex flex-wrap justify-content-between gap-2">
                        <div class="fw-semibold">{{ $auditAction }}</div>
                        <div class="text-muted small">{{ $audit->created_at?->format('d M Y, H:i') }}</div>
                    </div>
                    <div class="text-muted small mb-2">Oleh {{ $audit->user?->name ?? 'User tidak diketahui' }}</div>
                    @if ($audit->changes)
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($audit->changes as $field => $change)
                                <span class="audit-change">{{ $auditFieldLabels[$field] ?? str_replace('_', ' ', ucfirst($field)) }}:
                                    @if ($field === 'admission_decision' || in_array($audit->action, ['oral_exam_created', 'oral_exam_updated', 'interview_created', 'interview_updated'], true))
                                        {{ is_scalar($change['old'] ?? null) ? $change['old'] : 'Belum diisi' }} &rarr;
                                    @endif
                                    <strong>{{ is_scalar($change['new'] ?? null) ? $change['new'] : '-' }}</strong>
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-muted small py-2">Belum ada aktivitas perubahan yang tercatat.</div>
            @endforelse
        </div>
    </div>

    {{-- Tabs --}}
    <div class="card trezo-card">
        <div class="card-body">
            <ul class="nav nav-tabs" id="regTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-santri" data-bs-toggle="tab" data-bs-target="#pane-santri"
                        type="button" role="tab">
                        Data Santri
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-ortu" data-bs-toggle="tab" data-bs-target="#pane-ortu" type="button"
                        role="tab">
                        Ortu/Wali
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-pernyataan" data-bs-toggle="tab" data-bs-target="#pane-pernyataan"
                        type="button" role="tab">
                        Pernyataan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-dokumen" data-bs-toggle="tab" data-bs-target="#pane-dokumen"
                        type="button" role="tab">
                        Dokumen
                    </button>
                </li>
            </ul>

            <div class="tab-content pt-3" id="regTabsContent">
                {{-- TAB 1: Data Santri --}}
                <div class="tab-pane fade show active" id="pane-santri" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="card trezo-card">
                                <div class="card-body">
                                    <h6 class="mb-3">Identitas</h6>

                                    <div class="row g-2">
                                        <div class="col-12">
                                            <div class="text-muted small">Nama Lengkap (KK)</div>
                                            <div class="fw-semibold">{{ $sp?->full_name ?? '-' }}</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="text-muted small">NISN</div>
                                            <div class="fw-semibold">{{ $sp?->nisn ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small">NIK</div>
                                            <div class="fw-semibold">{{ $sp?->nik ?? '-' }}</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="text-muted small">Tempat Lahir</div>
                                            <div class="fw-semibold">{{ $sp?->birth_place ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small">Tanggal Lahir</div>
                                            <div class="fw-semibold">{{ $sp?->birth_date ?? '-' }}</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="text-muted small">Jenis Kelamin</div>
                                            <div class="fw-semibold">{{ $registration->gender_label }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small">Status Anak</div>
                                            <div class="fw-semibold">{{ $sp?->orphan_status ?? '-' }}</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="text-muted small">Agama</div>
                                            <div class="fw-semibold">{{ $sp?->religion ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small">Kewarganegaraan</div>
                                            <div class="fw-semibold">{{ $sp?->nationality ?? '-' }}</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="text-muted small">Gol. Darah</div>
                                            <div class="fw-semibold">{{ $sp?->blood_type ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small">Jumlah Saudara / Anak ke</div>
                                            <div class="fw-semibold">{{ $sp?->siblings_count ?? '-' }} /
                                                {{ $sp?->child_number ?? '-' }}</div>
                                        </div>

                                        <div class="col-12">
                                            <div class="text-muted small">Penyakit Berat</div>
                                            <div class="fw-semibold">{{ $sp?->medical_history ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card trezo-card">
                                <div class="card-body">
                                    <h6 class="mb-3">Alamat & Pendidikan</h6>

                                    <div class="row g-2">
                                        <div class="col-12">
                                            <div class="text-muted small">Alamat (KK)</div>
                                            <div class="fw-semibold">{{ $sp?->address ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted small">Provinsi</div>
                                            <div class="fw-semibold">{{ $sp?->province ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted small">Kab/Kota</div>
                                            <div class="fw-semibold">{{ $sp?->city ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted small">Kecamatan</div>
                                            <div class="fw-semibold">{{ $sp?->district ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted small">Kode Pos</div>
                                            <div class="fw-semibold">{{ $sp?->postal_code ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="text-muted small">Asal Sekolah</div>
                                            <div class="fw-semibold">{{ $sp?->school_origin ?? '-' }}</div>
                                        </div>

                                        <hr class="opacity-25 my-2">

                                        <div class="col-md-6">
                                            <div class="text-muted small">Hobi</div>
                                            <div class="fw-semibold">{{ $sp?->hobby ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small">Cita-cita</div>
                                            <div class="fw-semibold">{{ $sp?->ambition ?? '-' }}</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="text-muted small">Motivasi Masuk</div>
                                            <div class="fw-semibold">{{ $sp?->motivation ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small">Program Pilihan</div>
                                            <div class="fw-semibold">{{ $sp?->program_choice ?? '-' }}</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="text-muted small">Hafalan Al-Qur'an</div>
                                            <div class="fw-semibold">{{ $sp?->quran_memorization_level ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small">Kemampuan Baca</div>
                                            <div class="fw-semibold">{{ $sp?->quran_reading_level ?? '-' }}</div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 2: Ortu/Wali --}}
                <div class="tab-pane fade" id="pane-ortu" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="card trezo-card">
                                <div class="card-body">
                                    <h6 class="mb-3">Data Ayah / Wali</h6>

                                    <div class="row g-2">
                                        <div class="col-12">
                                            <div class="text-muted small">No KK</div>
                                            <div class="fw-semibold">{{ $pp?->kk_number ?? '-' }}</div>
                                        </div>

                                        <div class="col-12">
                                            <div class="text-muted small">Nama Ayah/Wali</div>
                                            <div class="fw-semibold">{{ $pp?->father_name ?? '-' }}</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="text-muted small">NIK Ayah</div>
                                            <div class="fw-semibold">{{ $pp?->father_nik ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small">Agama</div>
                                            <div class="fw-semibold">{{ $pp?->father_religion ?? '-' }}</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="text-muted small">Tempat Lahir</div>
                                            <div class="fw-semibold">{{ $pp?->father_birth_place ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small">Tanggal Lahir</div>
                                            <div class="fw-semibold">{{ $pp?->father_birth_date ?? '-' }}</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="text-muted small">Pendidikan</div>
                                            <div class="fw-semibold">{{ $pp?->father_education ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small">Pekerjaan</div>
                                            <div class="fw-semibold">{{ $pp?->father_job ?? '-' }}</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="text-muted small">Penghasilan/Bulan</div>
                                            <div class="fw-semibold">{{ $pp?->father_income ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small">No WA Ayah</div>
                                            <div class="fw-semibold">{{ $pp?->father_phone ?? '-' }}</div>
                                        </div>

                                        <div class="col-12">
                                            <div class="text-muted small">Alamat</div>
                                            <div class="fw-semibold">{{ $pp?->father_address ?? '-' }}</div>
                                            <div class="text-muted small">
                                                {{ $pp?->father_province ?? '-' }} • {{ $pp?->father_city ?? '-' }} •
                                                {{ $pp?->father_district ?? '-' }} • {{ $pp?->father_village ?? '-' }} •
                                                {{ $pp?->father_postal_code ?? '-' }}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card trezo-card">
                                <div class="card-body">
                                    <h6 class="mb-3">Data Ibu</h6>

                                    <div class="row g-2">
                                        <div class="col-12">
                                            <div class="text-muted small">Nama Ibu</div>
                                            <div class="fw-semibold">{{ $pp?->mother_name ?? '-' }}</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="text-muted small">NIK Ibu</div>
                                            <div class="fw-semibold">{{ $pp?->mother_nik ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small">Agama</div>
                                            <div class="fw-semibold">{{ $pp?->mother_religion ?? '-' }}</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="text-muted small">Tempat Lahir</div>
                                            <div class="fw-semibold">{{ $pp?->mother_birth_place ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small">Tanggal Lahir</div>
                                            <div class="fw-semibold">{{ $pp?->mother_birth_date ?? '-' }}</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="text-muted small">Pendidikan</div>
                                            <div class="fw-semibold">{{ $pp?->mother_education ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small">Pekerjaan</div>
                                            <div class="fw-semibold">{{ $pp?->mother_job ?? '-' }}</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="text-muted small">Penghasilan/Bulan</div>
                                            <div class="fw-semibold">{{ $pp?->mother_income ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small">No WA Ibu</div>
                                            <div class="fw-semibold">{{ $pp?->mother_phone ?? '-' }}</div>
                                        </div>

                                        <hr class="opacity-25 my-2">
                                        <div class="col-12">
                                            <div class="text-muted small">Ustadz Favorit</div>
                                            <div class="fw-semibold">{{ $pp?->favorite_ustadz ?? '-' }}</div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 3: Pernyataan --}}
                <div class="tab-pane fade" id="pane-pernyataan" role="tabpanel">
                    <div class="card trezo-card">
                        <div class="card-body">
                            <h6 class="mb-3">Pernyataan & Kesanggupan</h6>

                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <div class="border rounded-4 p-3"
                                        style="border-color: var(--trezo-border) !important;">
                                        <div class="text-muted small">Bersedia mengabdi bila lulus?</div>
                                        <div class="fs-6 fw-semibold">
                                            {{ $st?->willing_to_serve ? 'IYA' : 'TIDAK / BELUM' }}</div>
                                        @if ($st && !$st->willing_to_serve)
                                            <div class="alert alert-warning mt-2 mb-0">
                                                Jika memilih <b>Tidak</b>, pendaftaran tidak diproses selanjutnya.
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="border rounded-4 p-3"
                                        style="border-color: var(--trezo-border) !important;">
                                        <div class="text-muted small">Persetujuan</div>
                                        <ul class="mb-0">
                                            <li><b>Anti pelanggaran moral</b>:
                                                {{ $st?->agree_morality ? 'SETUJU' : 'BELUM' }}</li>
                                            <li><b>Tata tertib & visi misi</b>:
                                                {{ $st?->agree_rules ? 'SETUJU' : 'BELUM' }}</li>
                                            <li><b>Kesanggupan pembayaran</b>:
                                                {{ $st?->agree_payment ? 'SETUJU' : 'BELUM' }}</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="border rounded-4 p-3"
                                        style="border-color: var(--trezo-border) !important;">
                                        <div class="text-muted small mb-2">Waktu submit pernyataan</div>
                                        <div class="fw-semibold">{{ $st?->submitted_at ?? '-' }}</div>
                                    </div>
                                </div>

                                {{-- teks kesanggupan panjang bisa ditampilkan ringkas --}}
                                <div class="col-12">
                                    <div class="accordion" id="accPayment">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapsePayment">
                                                    Lihat ringkasan ketentuan pembayaran (accordion)
                                                </button>
                                            </h2>
                                            <div id="collapsePayment" class="accordion-collapse collapse"
                                                data-bs-parent="#accPayment">
                                                <div class="accordion-body text-muted">
                                                    <ul class="mb-0">
                                                        <li>Uang pendaftaran Rp150.000 (rekening sesuai ketentuan).</li>
                                                        <li>Tanda jadi maksimal 7 hari setelah diterima: Beasiswa Rp1jt,
                                                            Mandiri Rp2jt.</li>
                                                        <li>Infak 50% maksimal 30 hari sejak pengumuman, jika tidak dianggap
                                                            mengundurkan diri.</li>
                                                        <li>Semua pembayaran diniatkan sebagai infak dan tidak dapat diminta
                                                            kembali.</li>
                                                        <li>Bersedia pengabdian 1 tahun setelah lulus.</li>
                                                        <li>Khusus beasiswa: wajib tuntas sampai SMA, jika mundur tanpa
                                                            alasan syar’i wajib mengembalikan dana beasiswa.</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-muted small mt-2">
                                        (Catatan: detail nominal & rekening bisa ditampilkan full sesuai kebutuhan di modul
                                        periode/ketentuan.)
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 4: Dokumen + Viewer --}}
                <div class="tab-pane fade" id="pane-dokumen" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-lg-4">
                            <div class="card trezo-card">
                                <div class="card-body">
                                    <h6 class="mb-3">Daftar Dokumen</h6>

                                    <div class="list-group">
                                        @forelse($registration->documents as $doc)
                                            @php
                                                $label = $docLabels[$doc->type] ?? $doc->type;
                                                $path = $doc->file_path ? asset('storage/' . $doc->file_path) : null;
                                                $ext = $doc->file_path
                                                    ? Str::lower(pathinfo($doc->file_path, PATHINFO_EXTENSION))
                                                    : null;
                                                $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
                                                $isPdf = $ext === 'pdf';
                                            @endphp

                                            <a href="#" class="list-group-item list-group-item-action"
                                                data-doc-label="{{ $label }}" data-doc-url="{{ $path }}"
                                                data-doc-ext="{{ $ext }}" data-doc-isimg="{{ $isImg ? 1 : 0 }}"
                                                data-doc-ispdf="{{ $isPdf ? 1 : 0 }}" onclick="selectDoc(event,this)">
                                                <div class="d-flex justify-content-between">
                                                    <div class="fw-semibold">{{ $label }}</div>
                                                    @if ($doc->file_path)
                                                        <span class="badge bg-success">Ada</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">Missing</span>
                                                    @endif
                                                </div>
                                                <div class="text-muted small">
                                                    {{ $doc->file_path ? 'File: ' . basename($doc->file_path) : 'Belum diunggah' }}
                                                </div>
                                            </a>
                                        @empty
                                            <div class="text-muted">Belum ada dokumen.</div>
                                        @endforelse
                                    </div>

                                    <div class="text-muted small mt-3">
                                        Klik dokumen untuk melihat preview (PDF/gambar).
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="card trezo-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <h6 class="mb-0" id="docTitle">Preview Dokumen</h6>
                                            <div class="text-muted small" id="docMeta">Pilih dokumen di sebelah kiri.
                                            </div>
                                        </div>
                                        <a id="docOpen" class="btn btn-sm btn-outline-light d-none"
                                            target="_blank">Buka di tab baru</a>
                                    </div>

                                    <div id="docEmpty" class="text-center text-muted p-5">
                                        Belum ada dokumen yang dipilih.
                                    </div>

                                    {{-- Image preview --}}
                                    <div id="docImgWrap" class="d-none">
                                        <img id="docImg" src="" alt="Dokumen" class="img-fluid rounded-4"
                                            style="max-height: 70vh; width: 100%; object-fit: contain;">
                                    </div>

                                    {{-- PDF preview --}}
                                    <div id="docPdfWrap" class="d-none">
                                        <iframe id="docPdf" src=""
                                            style="width:100%; height: 70vh; border:0; border-radius:16px;"></iframe>
                                    </div>

                                    {{-- Unsupported preview --}}
                                    <div id="docUnknown" class="d-none">
                                        <div class="alert alert-info mb-0">
                                            Preview tidak tersedia untuk tipe file ini. Silakan buka di tab baru / download.
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div> {{-- tab-content --}}
        </div>
    </div>
    @if (in_array(auth()->user()->role, ['admin', 'ustadz'], true))
        @php $oralExam = $registration->oralExam; @endphp
        <div class="modal fade" id="modalUjianTahsin" tabindex="-1" aria-labelledby="modalUjianTahsinLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <form method="POST" action="{{ route('admin.oral-exams.save', $registration) }}" class="modal-content">
                    @csrf
                    <input type="hidden" name="exam_registration_id" value="{{ $registration->id }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUjianTahsinLabel">Input Ujian Tahsin Lisan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="fw-semibold">{{ $studentName }}</div>
                        <div class="text-muted mb-3">{{ $registration->registration_no }} · {{ $educationLabel }}</div>
                        <p class="text-muted small">Pilih nilai A/B/C untuk setiap soal. Kosong berarti belum dinilai.</p>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead><tr><th scope="col">Soal 1</th><th scope="col">Soal 2</th><th scope="col">Soal 3</th></tr></thead>
                                <tbody><tr>
                                    @foreach ([1, 2, 3] as $number)
                                        @php $field = 'question_' . $number . '_grade'; @endphp
                                        <td>
                                            <select name="{{ $field }}" class="form-select @error($field) is-invalid @enderror" aria-label="Nilai soal {{ $number }}">
                                                <option value="">Belum dinilai</option>
                                                @foreach (['A', 'B', 'C'] as $grade)
                                                    <option value="{{ $grade }}" @selected(old($field, $oralExam?->{$field}) === $grade)>{{ $grade }}</option>
                                                @endforeach
                                            </select>
                                            @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </td>
                                    @endforeach
                                </tr></tbody>
                            </table>
                        </div>
                        <div class="row g-3 mb-3">
                            @foreach (['tpa_score' => 'Nilai TPA', 'arabic_score' => 'Nilai Bahasa Arab'] as $field => $label)
                                <div class="col-sm-6">
                                    <label for="tahsin-{{ $field }}" class="form-label">{{ $label }}</label>
                                    <input type="number" id="tahsin-{{ $field }}" name="{{ $field }}" class="form-control @error($field) is-invalid @enderror" min="0" max="100" step="0.01" placeholder="0 - 100" value="{{ old($field, $registration->{$field}) }}">
                                    @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            @endforeach
                        </div>
                        <label for="tahsin-notes" class="form-label">Catatan Ujian Tahsin</label>
                        <textarea id="tahsin-notes" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="4" maxlength="2000" placeholder="Catatan bacaan, makhraj, atau tajwid santri">{{ old('notes', $oralExam?->notes) }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if ($oralExam)
                            <div class="small text-muted mt-3">Terakhir disimpan oleh {{ $oralExam->examiner?->name ?? '-' }} pada {{ $oralExam->updated_at?->format('d/m/Y H:i') }}.</div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Nilai Ujian</button>
                    </div>
                </form>
            </div>
        </div>
        @if ($errors->any() && (string) old('exam_registration_id') === (string) $registration->id)
            @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalUjianTahsin')).show();
                    });
                </script>
            @endpush
        @endif
    @endif
    <div class="modal fade" id="modalKelulusanNote" tabindex="-1" aria-labelledby="modalKelulusanNoteLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content trezo-card">
                <form method="POST" action="{{ route('admin.registrations.graduation', $registration) }}">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalSetKelulusanLabel">Set / Update Kelulusan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                            <div class="border rounded-4 p-3 mb-4 bg-light">
                                <h6 class="mb-3">Tes Lisan</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Soal 1</label>
                                        <input type="number" name="oral_question_1" class="form-control" min="0" max="100" value="{{ old('oral_question_1', $registration->oral_question_1 ?? 0) }}" placeholder="0">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Soal 2</label>
                                        <input type="number" name="oral_question_2" class="form-control" min="0" max="100" value="{{ old('oral_question_2', $registration->oral_question_2 ?? 0) }}" placeholder="0">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Soal 3</label>
                                        <input type="number" name="oral_question_3" class="form-control" min="0" max="100" value="{{ old('oral_question_3', $registration->oral_question_3 ?? 0) }}" placeholder="0">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Status Tahsin</label>
                                        <select name="tahsin_status" class="form-select">
                                            @foreach (['pending' => 'Pending', 'diterima' => 'Diterima', 'tidak_diterima' => 'Tidak Diterima'] as $key => $label)
                                                <option value="{{ $key }}" @selected(old('tahsin_status', $registration->tahsin_status ?? 'pending') === $key)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Catatan Lisan</label>
                                        <textarea name="oral_exam_notes" class="form-control" rows="3"
                                            placeholder="Contoh: Tajwid baik, perlu memperbaiki makhraj huruf...">{{ old('oral_exam_notes', $registration->oral_exam_notes) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="border rounded-4 p-3 mb-4 bg-light">
                                <h6 class="mb-3">Penilaian Seleksi</h6>
                                <div class="row g-3">
                                    @php
                                        $oralAverage = collect([
                                            (int) ($registration->oral_question_1 ?? 0),
                                            (int) ($registration->oral_question_2 ?? 0),
                                            (int) ($registration->oral_question_3 ?? 0),
                                        ])->avg();
                                    @endphp
                                    <div class="col-md-4 col-6">
                                        <label class="form-label fw-semibold">Bahasa Arab</label>
                                        <input type="number" name="arabic_score" class="form-control" min="0" max="100" step="0.01"
                                            value="{{ old('arabic_score', $registration->arabic_score) }}" placeholder="0 - 100">
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <label class="form-label fw-semibold">TPA</label>
                                        <input type="number" name="tpa_score" class="form-control" min="0" max="100" step="0.01"
                                            value="{{ old('tpa_score', $registration->tpa_score) }}" placeholder="0 - 100">
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <label class="form-label fw-semibold">Rata-rata Tes Lisan</label>
                                        <input type="text" class="form-control" value="{{ number_format((float) $oralAverage, 2, ',', '.') }}" readonly>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Wawancara</label>
                                        <select name="interview_recommendation" class="form-select" required>
                                            @foreach ([
                                                'sangat_direkomendasikan' => 'Sangat Direkomendasikan',
                                                'direkomendasikan' => 'Direkomendasikan',
                                                'tidak_direkomendasikan' => 'Tidak Direkomendasikan',
                                            ] as $key => $text)
                                                <option value="{{ $key }}" @selected(old('interview_recommendation', $registration->interview_recommendation ?? 'direkomendasikan') === $key)>{{ $text }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                        <div class="mb-3">
                            <div class="text-muted small">Nomor Pendaftaran</div>
                            <div class="fw-semibold">{{ $registration->registration_no }}</div>
                        </div>

                        {{-- RADIO STATUS --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status Kelulusan</label>
                            <div class="row g-2">
                                @foreach ([
            'pending' => 'Pending',
            'lulus' => 'Lulus',
            'cadangan' => 'Cadangan',
            'tidak_lulus' => 'Tidak Lulus',
        ] as $key => $text)
                                    <div class="col-md-3">
                                        <label class="border rounded-4 p-2 w-100 text-center" style="cursor:pointer">
                                            <input type="radio" name="graduation_status" value="{{ $key }}"
                                                class="form-check-input me-1" @checked($registration->graduation_status === $key)>
                                            {{ $text }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- CATATAN --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Catatan Admin</label>
                            <textarea name="admin_note" class="form-control" rows="4"
                                placeholder="Contoh: Silakan lakukan pembayaran tanda jadi maksimal 7 hari setelah pengumuman...">{{ old('admin_note', $registration->admin_note) }}</textarea>
                            <div class="text-muted small mt-1">
                                Catatan ini akan tampil di dashboard orang tua.
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Simpan Kelulusan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (new URLSearchParams(window.location.search).get('open') !== 'oral-exam') {
                return;
            }

            const modalElement = document.getElementById('modalKelulusanNote');
            if (modalElement && window.bootstrap) {
                bootstrap.Modal.getOrCreateInstance(modalElement).show();
            }
        });
    </script>
    <script>
        function selectDoc(e, el) {
            e.preventDefault();

            const label = el.getAttribute('data-doc-label');
            const url = el.getAttribute('data-doc-url');
            const ext = el.getAttribute('data-doc-ext');
            const isImg = el.getAttribute('data-doc-isimg') === '1';
            const isPdf = el.getAttribute('data-doc-ispdf') === '1';

            const title = document.getElementById('docTitle');
            const meta = document.getElementById('docMeta');
            const open = document.getElementById('docOpen');

            const empty = document.getElementById('docEmpty');
            const imgW = document.getElementById('docImgWrap');
            const pdfW = document.getElementById('docPdfWrap');
            const unkW = document.getElementById('docUnknown');
            const img = document.getElementById('docImg');
            const pdf = document.getElementById('docPdf');

            title.textContent = label;
            meta.textContent = url ? `Tipe: ${ext?.toUpperCase()} • ${url.split('/').pop()}` : 'Dokumen belum diunggah';

            // reset
            empty.classList.add('d-none');
            imgW.classList.add('d-none');
            pdfW.classList.add('d-none');
            unkW.classList.add('d-none');
            open.classList.add('d-none');

            if (!url) {
                empty.classList.remove('d-none');
                empty.textContent = 'Dokumen belum diunggah.';
                return;
            }

            open.href = url;
            open.classList.remove('d-none');

            if (isImg) {
                img.src = url;
                imgW.classList.remove('d-none');
                return;
            }

            if (isPdf) {
                // embed pdf
                pdf.src = url;
                pdfW.classList.remove('d-none');
                return;
            }

            // fallback
            unkW.classList.remove('d-none');
        }
    </script>
    <script>
        @if (session('success'))
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalSetKelulusan'));
            if (modal) {
                modal.hide();
            }
        @endif
    </script>
@endpush
