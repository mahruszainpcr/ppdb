@extends('layouts.app')
@section('title', 'Pengumuman Kelulusan')

@section('content')
    @if (session('success'))
        <div class="alert alert-success border-0 rounded-3">{{ session('success') }}</div>
    @endif

    @php
        $gradMap = [
            'pending' => ['Menunggu', 'secondary'],
            'lulus' => ['LULUS', 'success'],
            'tidak_lulus' => ['TIDAK LULUS', 'danger'],
            'cadangan' => ['CADANGAN', 'warning'],
        ];
        [$gradLabel, $gradColor] = $gradMap[$registration->graduation_status] ?? ['-', 'secondary'];
    @endphp

    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="card trezo-card mb-3">
                <div class="card-body">
                    <h5 class="mb-1">Pengumuman Hasil Seleksi</h5>
                    <div class="text-muted small mb-3">
                        Nomor Pendaftaran:
                        <span class="fw-semibold">{{ $registration->registration_no }}</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <div class="fw-semibold">Kelulusan</div>
                            <div class="fs-4 fw-bold text-{{ $gradColor }}">{{ $gradLabel }}</div>
                        </div>
                        <div class="text-end">
                            <div class="text-muted small">Catatan Admin</div>
                            <div class="small">{{ $registration->admin_note ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card trezo-card">
                <div class="card-body">
                    <h6 class="mb-2">Status Formulir</h6>
                    <div class="text-muted small mb-2">
                        Pastikan seluruh data & dokumen sudah lengkap untuk memperlancar verifikasi.
                    </div>
                    <a href="{{ route('psb.wizard', ['step' => 1]) }}" class="btn btn-outline-light btn-sm">Cek
                        Dokumen</a>
                    <a href="{{ route('psb.wizard', ['step' => 2]) }}" class="btn btn-outline-light btn-sm">Cek Data
                        Santri</a>
                    <a href="{{ route('psb.wizard', ['step' => 3]) }}" class="btn btn-outline-light btn-sm">Cek Orang Tua</a>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
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
