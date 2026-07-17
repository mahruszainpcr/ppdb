@extends('layouts.app')
@section('title', 'QR Bukti Pendaftaran')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xxl-8 col-xl-9">
            <div class="card trezo-card overflow-hidden">
                <div class="card-body p-0">
                    <div class="p-4 p-md-5 text-white"
                        style="background: linear-gradient(135deg, #0f5132 0%, #198754 55%, #9ad0b1 100%);">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                            <div>
                                <span class="badge bg-light text-success mb-3">Bukti Pendaftaran Resmi</span>
                                <h3 class="mb-2">QR Code Pendaftaran Santri</h3>
                                <p class="mb-0 text-white-50">
                                    Tunjukkan halaman ini kepada admin, guru, atau ustadz saat wawancara agar detail
                                    pendaftaran bisa langsung dibuka melalui hasil scan.
                                </p>
                            </div>
                            <a href="{{ route('app.dashboard', ['registration' => $registration->id]) }}"
                                class="btn btn-light btn-sm text-success">
                                Kembali ke Dashboard
                            </a>
                        </div>
                    </div>

                    <div class="p-4 p-md-5">
                        <div class="row g-4 align-items-center">
                            <div class="col-lg-6">
                                <div class="bg-light border rounded-4 p-4 text-center h-100">
                                    <div class="d-inline-block bg-white rounded-4 shadow-sm p-3">
                                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(280)->margin(1)->generate($scanUrl) !!}
                                    </div>
                                    <div class="small text-muted mt-3">Scan untuk melihat detail formulir pendaftaran</div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="h-100 d-flex flex-column justify-content-center">
                                    <div class="mb-3">
                                        <div class="text-muted small">Nomor Pendaftaran</div>
                                        <div class="fs-4 fw-bold">{{ $registration->registration_no }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="text-muted small">Nama Calon Santri</div>
                                        <div class="fw-semibold">{{ $registration->studentProfile?->full_name ?? '-' }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="text-muted small">Asal Sekolah</div>
                                        <div class="fw-semibold">{{ $registration->studentProfile?->school_origin ?? '-' }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="text-muted small">Jenis Pendaftar</div>
                                        <div class="fw-semibold">{{ $registration->funding_type_label }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="text-muted small">Jenis Kelamin</div>
                                        <div class="fw-semibold">{{ $registration->gender_label }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="text-muted small">Jenjang</div>
                                        <div class="fw-semibold">{{ $registration->education_level_label }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="text-muted small">Status Pendaftaran</div>
                                        <span class="badge text-bg-secondary">{{ ucfirst($registration->status) }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <div class="text-muted small">Periode</div>
                                        <div class="fw-semibold">{{ $registration->period?->academic_year ?? '-' }}</div>
                                    </div>
                                    {{-- <div class="mb-4">
                                        <div class="text-muted small">Link Scan Petugas</div>
                                        <div class="small fw-semibold text-break">{{ $scanUrl }}</div>
                                    </div> --}}
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="{{ route('psb.proof.pdf', $registration) }}" class="btn btn-primary btn-sm">
                                            Download PDF
                                        </a>
                                        <a href="{{ route('psb.wizard', ['step' => 3, 'registration' => $registration->id]) }}"
                                            class="btn btn-outline-secondary btn-sm">
                                            Edit Pendaftaran
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
