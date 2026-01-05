@extends('layouts.app')
@section('title', 'Detail Pendaftar')

@php
    use Illuminate\Support\Str;

    $sp = $registration->studentProfile;
    $pp = $registration->parentProfile;
    $st = $registration->statement;

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
@endphp

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Detail Pendaftar</h4>
            <div class="text-muted">
                No: <span class="fw-semibold">{{ $registration->registration_no }}</span>
                <span class="mx-2">•</span>
                Status: <span class="badge bg-secondary">{{ $registration->status }}</span>
                <span class="mx-2">•</span>
                @php
                    $gs = $registration->graduation_status;
                    $label =
                        [
                            'pending' => 'Pending',
                            'lulus' => 'Lulus',
                            'tidak_lulus' => 'Tidak Lulus',
                            'cadangan' => 'Cadangan',
                        ][$gs] ?? $gs;

                    $badgeClass = match ($gs) {
                        'lulus' => 'bg-success',
                        'tidak_lulus' => 'bg-danger',
                        'cadangan' => 'bg-warning text-dark',
                        default => 'bg-info',
                    };
                @endphp

                Kelulusan:
                <button type="button" class="badge {{ $badgeClass }} border-0" style="cursor:pointer"
                    data-bs-toggle="modal" data-bs-target="#modalKelulusanNote">
                    {{ $label }}<span class="ms-1">🛈</span>
                </button>

            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.registrations.index') }}" class="btn btn-outline-light btn-sm">Kembali</a>

            {{-- optional: tombol logout --}}
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="btn btn-outline-light btn-sm">Logout</button>
            </form>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-4">
            <div class="card trezo-card">
                <div class="card-body">
                    <div class="text-muted small mb-1">Nama Santri</div>
                    <div class="fs-6 fw-semibold">{{ $sp?->full_name ?? '-' }}</div>
                    <hr class="opacity-25">
                    <div class="d-flex justify-content-between">
                        <div class="text-muted small">Jenjang</div>
                        <div class="fw-semibold">{{ $registration->education_level }}</div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <div class="text-muted small">Pembiayaan</div>
                        <div class="fw-semibold text-capitalize">{{ $registration->funding_type }}</div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <div class="text-muted small">Gender</div>
                        <div class="fw-semibold">{{ $registration->gender }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card trezo-card">
                <div class="card-body">
                    <div class="text-muted small mb-1">Akun Wali</div>
                    <div class="fw-semibold">{{ $registration->user->name ?? '-' }}</div>
                    <div class="text-muted">{{ $registration->user->phone ?? '-' }}</div>
                    <hr class="opacity-25">
                    <div class="text-muted small mb-1">Periode/Gelombang</div>
                    <div class="fw-semibold">{{ $registration->period?->name ?? '-' }}</div>
                    <div class="text-muted small">Gelombang: {{ $registration->period?->wave ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card trezo-card">
                <div class="card-body">
                    <div class="text-muted small mb-1">Kelengkapan Dokumen</div>
                    @php
                        $total = $registration->documents->count();
                        $uploaded = $registration->documents->whereNotNull('file_path')->count();
                        $pct = $total ? intval(($uploaded / $total) * 100) : 0;
                    @endphp

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fw-semibold">{{ $uploaded }} / {{ $total }} terunggah</div>
                        <div class="text-muted small">{{ $pct }}%</div>
                    </div>
                    <div class="progress mt-2" style="height:10px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $pct }}%"></div>
                    </div>

                    <hr class="opacity-25">

                    <div class="text-muted small mb-1">Pernyataan</div>
                    <div class="d-flex justify-content-between">
                        <div class="text-muted small">Bersedia Mengabdi</div>
                        <div class="fw-semibold">{{ $st?->willing_to_serve ? 'IYA' : 'TIDAK / BELUM' }}</div>
                    </div>
                </div>
            </div>
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
                                            <div class="fw-semibold">{{ $registration->gender ?? '-' }}</div>
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
                                                {{ $pp?->father_city ?? '-' }} • {{ $pp?->father_district ?? '-' }} •
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

@endsection

@push('scripts')
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
