@extends('layouts.app')
@section('title', 'Dashboard PSB')

@section('content')
    @if (session('success'))
        <div class="alert alert-success border-0 rounded-3">{{ session('success') }}</div>
    @endif

    @php
        $statusMap = [
            'draft' => ['Draft', 'secondary'],
            'submitted' => ['Terkirim', 'primary'],
            'verified' => ['Diverifikasi', 'success'],
            'revision_requested' => ['Perlu Revisi', 'warning'],
        ];
        [$statusLabel, $statusColor] = $statusMap[$registration->status] ?? ['Status', 'secondary'];

        $gradMap = [
            'pending' => ['Menunggu', 'secondary'],
            'lulus' => ['LULUS', 'success'],
            'tidak_lulus' => ['TIDAK LULUS', 'danger'],
            'cadangan' => ['CADANGAN', 'warning'],
        ];
        [$gradLabel, $gradColor] = $gradMap[$registration->graduation_status] ?? ['-', 'secondary'];

        $period = $registration->period ?? $activePeriod;
    @endphp

    <div class="row g-3">

        {{-- Header summary --}}
        <div class="col-12">
            <div class="card trezo-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <h5 class="mb-1">Dashboard Pendaftaran Santri Baru</h5>
                            <div class="text-muted small">
                                Nomor Pendaftaran:
                                <span class="fw-semibold">{{ $registration->registration_no }}</span>
                            </div>
                            <div class="text-muted small">
                                Periode:
                                <span class="fw-semibold">{{ $period?->name ?? 'Belum diset (admin)' }}</span>
                            </div>
                        </div>

                        <div class="text-end">
                            <div class="d-flex gap-2 justify-content-end flex-wrap">
                                <span class="badge rounded-pill text-bg-{{ $statusColor }}">{{ $statusLabel }}</span>
                                <span class="badge rounded-pill text-bg-{{ $gradColor }}">Kelulusan:
                                    {{ $gradLabel }}</span>
                            </div>

                            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                                @csrf
                                <button class="btn btn-sm btn-outline-light">Logout</button>
                            </form>
                        </div>
                    </div>

                    <hr class="border-opacity-25">

                    {{-- Progress --}}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fw-semibold">Kelengkapan Form</div>
                        <div class="text-muted small">{{ $progressPercent }}%</div>
                    </div>

                    <div class="progress" role="progressbar" aria-valuenow="{{ $progressPercent }}" aria-valuemin="0"
                        aria-valuemax="100">
                        <div class="progress-bar" style="width: {{ $progressPercent }}%"></div>
                    </div>

                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        <a href="{{ route('psb.wizard', ['step' => $nextStep]) }}" class="btn btn-primary">
                            {{ $progressPercent < 100 ? 'Lanjut Lengkapi Form' : 'Lihat / Review Form' }}
                        </a>
                        <a href="{{ route('psb.wizard', ['step' => 1]) }}" class="btn btn-outline-light">Edit Program &
                            Dokumen</a>
                        <a href="{{ route('psb.wizard', ['step' => 2]) }}" class="btn btn-outline-light">Edit Data
                            Santri</a>
                        <a href="{{ route('psb.wizard', ['step' => 3]) }}" class="btn btn-outline-light">Edit Orang Tua &
                            Pernyataan</a>
                    </div>

                </div>
            </div>
        </div>

        {{-- Alerts --}}
        <div class="col-12 col-lg-8">
            @if (!empty($missingDocs))
                <div class="alert alert-warning border-0 rounded-3">
                    <div class="fw-semibold mb-1">Dokumen wajib belum lengkap</div>
                    <div class="small text-muted mb-2">Lengkapi di Step 1 agar bisa submit final.</div>
                    <ul class="mb-0 small">
                        @foreach ($missingDocs as $m)
                            <li>{{ str_replace('_', ' ', $m) }}</li>
                        @endforeach
                    </ul>
                    <div class="mt-2">
                        <a class="btn btn-sm btn-dark" href="{{ route('psb.wizard', ['step' => 1]) }}">Lengkapi Dokumen</a>
                    </div>
                </div>
            @endif

            @if ($registration->status === 'revision_requested')
                <div class="alert alert-warning border-0 rounded-3">
                    <div class="fw-semibold mb-1">Perlu Revisi dari Admin</div>
                    <div class="small text-muted">
                        {{ $registration->admin_note ?? 'Silakan periksa kembali data/dokumen Anda.' }}
                    </div>
                </div>
            @endif

            {{-- Step cards --}}
            <div class="card trezo-card mb-3">
                <div class="card-body">
                    <h6 class="mb-3">Checklist Tahapan</h6>

                    <div class="list-group list-group-flush">
                        <div
                            class="list-group-item bg-transparent text-light d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">Step 1 — Program & Dokumen</div>
                                <div class="text-muted small">Pilih pembiayaan/jenjang & upload dokumen wajib.</div>
                            </div>
                            @if ($step1Complete)
                                <span class="badge text-bg-success">Selesai</span>
                            @else
                                <span class="badge text-bg-warning">Belum</span>
                            @endif
                        </div>

                        <div
                            class="list-group-item bg-transparent text-light d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">Step 2 — Data Calon Santri</div>
                                <div class="text-muted small">Identitas, alamat, tahfidz, program pilihan.</div>
                            </div>
                            @if ($step2Complete)
                                <span class="badge text-bg-success">Selesai</span>
                            @else
                                <span class="badge text-bg-warning">Belum</span>
                            @endif
                        </div>

                        <div
                            class="list-group-item bg-transparent text-light d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">Step 3 — Orang Tua & Pernyataan</div>
                                <div class="text-muted small">Data ayah/ibu, ustadz favorit, kesanggupan & submit final.
                                </div>
                            </div>
                            @if ($step3Complete)
                                <span class="badge text-bg-success">Selesai</span>
                            @else
                                <span class="badge text-bg-warning">Belum</span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-3 d-flex gap-2 flex-wrap">
                        @if ($registration->status !== 'submitted' && $step1Complete && $step2Complete && $step3Complete)
                            <div class="alert alert-info border-0 rounded-3 w-100 mb-0">
                                <div class="fw-semibold mb-1">Siap Submit Final</div>
                                <div class="small text-muted">Silakan buka Step 3 dan klik tombol <b>Submit Final</b>.</div>
                                <a href="{{ route('psb.wizard', ['step' => 3]) }}" class="btn btn-sm btn-primary mt-2">Ke Step
                                    3</a>
                            </div>
                        @endif

                        @if ($registration->status === 'submitted')
                            <div class="alert alert-success border-0 rounded-3 w-100 mb-0">
                                <div class="fw-semibold mb-1">Formulir sudah terkirim</div>
                                <div class="small text-muted">Silakan pantau status verifikasi dan pengumuman kelulusan di
                                    halaman ini.</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Hasil kelulusan --}}
            <div class="card trezo-card">
                <div class="card-body">
                    <h6 class="mb-2">Status Kelulusan</h6>
                    <div class="text-muted small mb-3">Admin akan mengumumkan hasil seleksi melalui sistem.</div>

                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <div class="fw-semibold">Kelulusan:</div>
                            <div class="fs-5 fw-bold text-{{ $gradColor }}">{{ $gradLabel }}</div>
                        </div>
                        <div class="text-end">
                            <div class="text-muted small">Catatan Admin</div>
                            <div class="small">{{ $registration->admin_note ?? '-' }}</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Sidebar info --}}
        <div class="col-12 col-lg-4">
            {{-- Jadwal penting --}}
            <div class="card trezo-card mb-3">
                <div class="card-body">
                    <h6 class="mb-2">Jadwal Penting</h6>
                    @if ($period)
                        <ul class="small text-muted mb-0">
                            <li>Ujian: <b>{{ optional($period->exam_date)->format('d M Y') ?? '-' }}</b></li>
                            <li>Pengumuman: <b>{{ optional($period->announce_date)->format('d M Y') ?? '-' }}</b></li>
                            <li>Batas Tanda Jadi:
                                <b>{{ optional($period->down_payment_deadline)->format('d M Y') ?? '-' }}</b></li>
                        </ul>
                    @else
                        <div class="text-muted small">Periode belum diaktifkan oleh admin.</div>
                    @endif
                </div>
            </div>

            {{-- WA Group --}}
            <div class="card trezo-card mb-3">
                <div class="card-body">
                    <h6 class="mb-2">Grup Calon Peserta Ujian</h6>
                    <div class="text-muted small mb-2">Setelah mendaftar, silakan bergabung ke grup sesuai jenis kelamin.
                    </div>

                    @if ($waLink)
                        <a href="{{ $waLink }}" target="_blank" class="btn btn-success w-100">Gabung Grup
                            WhatsApp</a>
                    @else
                        <div class="alert alert-dark border-0 rounded-3 mb-0">
                            <div class="small text-muted">
                                Link grup akan tampil otomatis setelah Anda memilih jenis kelamin (Step 2).
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Kontak admin --}}
            <div class="card trezo-card">
                <div class="card-body">
                    <h6 class="mb-2">Konfirmasi Admin (Chat)</h6>
                    <div class="text-muted small mb-2">Untuk informasi lebih lanjut:</div>

                    <ul class="small text-muted mb-0">
                        <li>Abu Ja'far: <b>{{ $period?->admin_contact_1 ?? '0821-7267-6721' }}</b></li>
                        <li>Admin: <b>{{ $period?->admin_contact_2 ?? '0821-1792-7452' }}</b></li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
@endsection
