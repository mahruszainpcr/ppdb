@extends('layouts.app')
@section('title', 'Dashboard Wali')

@section('content')
    @if (session('success'))
        <div class="alert alert-success border-0 rounded-3">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 rounded-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card trezo-card mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h4 class="mb-1">Dashboard Pendaftaran</h4>
                    <div class="text-muted">Nomor pendaftaran aktif: <span
                            class="fw-semibold">{{ $registration->registration_no }}</span></div>
                    <div class="text-muted">Periode aktif: <span
                            class="fw-semibold">{{ $activePeriod?->academic_year ?? '-' }}</span></div>
                </div>
                <div class="text-end">
                    <div class="text-muted small mb-1">Progress Pengisian</div>
                    <div class="fw-bold fs-4">{{ $progressPercent }}%</div>
                    <a href="{{ route('psb.wizard', ['step' => $nextStep, 'registration' => $registration->id]) }}"
                        class="btn btn-primary btn-sm mt-2">
                        Lanjutkan Pengisian
                    </a>
                </div>
            </div>

            <div class="progress mt-3" style="height: 10px;">
                <div class="progress-bar" role="progressbar" style="width: {{ $progressPercent }}%;"
                    aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>

            <div class="row g-2 mt-2">
                <div class="col-md-4">
                    <div class="border rounded p-2">
                        <div class="fw-semibold">Step 1</div>
                        <div class="{{ $step1Complete ? 'text-success' : 'text-muted' }}">
                            {{ $step1Complete ? 'Selesai' : 'Belum lengkap' }}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-2">
                        <div class="fw-semibold">Step 2</div>
                        <div class="{{ $step2Complete ? 'text-success' : 'text-muted' }}">
                            {{ $step2Complete ? 'Selesai' : 'Belum lengkap' }}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-2">
                        <div class="fw-semibold">Step 3</div>
                        <div class="{{ $step3Complete ? 'text-success' : 'text-muted' }}">
                            {{ $step3Complete ? 'Selesai' : 'Belum lengkap' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card trezo-card mb-3">
        <div class="card-body">
            <div class="row g-4 align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge text-bg-primary">Bukti Pendaftaran</span>
                        <span class="text-muted small">Tunjukkan saat wawancara atau verifikasi.</span>
                    </div>
                    <h5 class="mb-2">QR Code Pendaftaran Aktif</h5>
                    <p class="text-muted mb-3">
                        QR ini terhubung ke detail formulir pendaftaran <span
                            class="fw-semibold">{{ $registration->registration_no }}</span>. Admin, guru, atau ustadz
                        yang sudah login bisa scan untuk melihat isi form lengkap.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ $activeQrPageUrl }}" class="btn btn-primary btn-sm">Lihat QR Penuh</a>
                        <span class="btn btn-outline-secondary btn-sm disabled">ID: {{ $registration->registration_no }}</span>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="border rounded-4 p-3 bg-light text-center">
                        <div class="d-inline-block bg-white p-3 rounded-4 shadow-sm">
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(190)->margin(1)->generate($activeScanUrl) !!}
                        </div>
                        <div class="small text-muted mt-3">Scan menuju detail pendaftaran petugas</div>
                        <div class="fw-semibold small mt-1">{{ $registration->registration_no }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (!empty($missingDocs))
        <div class="card trezo-card mb-3">
            <div class="card-body">
                <h6 class="mb-2">Checklist Dokumen Step 1</h6>
                <div class="text-muted mb-2">Pastikan dokumen wajib sudah diunggah.</div>
                <ul class="mb-0">
                    @foreach ($missingDocs as $doc)
                        <li>{{ str_replace('_', ' ', $doc) }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if ($waLink)
        <div class="card trezo-card mb-3">
            <div class="card-body">
                <h6 class="mb-1">Group Informasi</h6>
                <div class="text-muted mb-2">Pendaftaran sudah 100% lengkap. Silakan bergabung ke grup sesuai gender
                    (Ikhwan/Akhwat).</div>
                <a href="{{ $waLink }}" target="_blank" rel="noopener" class="btn btn-outline-success btn-sm">Gabung
                    Group WA</a>
            </div>
        </div>
    @endif

    <div class="card trezo-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Riwayat Pendaftaran Calon Santri</h5>
                <a href="{{ route('psb.new') }}" class="btn btn-success btn-sm">Tambah Pendaftaran Baru</a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Nomor Pendaftaran</th>
                            <th>Nama Calon Santri</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($registrationHistories as $item)
                            <tr>
                                <td class="fw-semibold">{{ $item['registration_no'] }}</td>
                                <td>{{ $item['student_name'] }}</td>
                                <td style="min-width:220px">
                                    <div class="small mb-1">{{ $item['progress'] }}%</div>
                                    <div class="progress" style="height:8px;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $item['progress'] }}%;"
                                            aria-valuenow="{{ $item['progress'] }}" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge text-bg-secondary">{{ ucfirst($item['status']) }}</span>
                                </td>
                                <td>{{ $item['created_at'] }}</td>
                                <td class="text-end">
                                    <a href="{{ route('app.dashboard', ['registration' => $item['id']]) }}"
                                        class="btn btn-outline-secondary btn-sm {{ $activeRegistrationId === $item['id'] ? 'disabled' : '' }}">
                                        Pilih
                                    </a>
                                    <a href="{{ route('psb.wizard', ['step' => $item['next_step'], 'registration' => $item['id']]) }}"
                                        class="btn btn-primary btn-sm">
                                        Lanjutkan
                                    </a>
                                    <a href="{{ $item['qr_page_url'] }}" class="btn btn-outline-dark btn-sm">
                                        QR
                                    </a>
                                    @if ($item['status'] === 'draft')
                                        <form action="{{ route('psb.delete', $item['id']) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Yakin hapus draft pendaftaran ini?')">
                                            @csrf
                                            <button class="btn btn-outline-danger btn-sm">Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data pendaftaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
