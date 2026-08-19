@extends('layouts.app')

@section('title', 'Wizard PSB - Step 3')

@php
    $periodInfo = $registration->period ?? $activePeriod ?? null;
    $wizardMode = $wizardMode ?? 'parent';
    $wizardTitle = $wizardTitle ?? 'Pendaftaran Santri Baru';
    $step1Action = $step1Action ?? route('psb.step1');
    $step2Url = $step2Url ?? route('psb.wizard', ['step' => 2, 'registration' => $registration->id]);
    $listUrl = $listUrl ?? route('app.dashboard', ['registration' => $registration->id]);
    $detailUrl = $detailUrl ?? null;
    $deleteUrl = $deleteUrl ?? null;
    $showDeleteButton = $showDeleteButton ?? false;
    $edu = old('education_level', $registration->education_level) ?? '';
    $wizardStepNumber = $wizardStepNumber ?? 3;
    $step1Status = $step1Status ?? 'done';
    $step2Status = $step2Status ?? 'done';
    $step3Status = $step3Status ?? 'active';
    $statusClass = fn(string $status) => match ($status) {
        'done' => 'text-bg-success',
        'active' => 'text-bg-primary',
        default => 'text-bg-secondary',
    };
    $isScholarshipAllowed = (int) ($periodInfo?->wave ?? $activePeriod?->wave ?? 1) === 1;
    $currentFunding = old('funding_type', $registration->funding_type) ?? 'mandiri';
    if (!$isScholarshipAllowed && $currentFunding === 'beasiswa') {
        $currentFunding = 'mandiri';
    }
    $submitLabel = $edu === 'SMA_OLD' ? 'Simpan & Lanjut Form Santri Lama' : 'Submit Final';
@endphp

@section('content')
    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success border-0 rounded-3">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger border-0 rounded-3">
                    <div class="fw-semibold mb-1">Periksa kembali isian Anda:</div>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="col-12 col-lg-8">
            <div class="card trezo-card mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h5 class="mb-1">{{ $wizardTitle }}</h5>
                            <div class="text-muted small">
                                Nomor Pendaftaran: <span class="fw-semibold">{{ $registration->registration_no }}</span>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            @if ($wizardMode === 'admin')
                                <a href="{{ $listUrl }}" class="btn btn-outline-light btn-sm">Kembali</a>
                                @if ($detailUrl)
                                    <a href="{{ $detailUrl }}" class="btn btn-outline-light btn-sm">Detail</a>
                                @endif
                                @if ($showDeleteButton && $deleteUrl)
                                    <form method="POST" action="{{ $deleteUrl }}"
                                        onsubmit="return confirm('Yakin hapus data pendaftaran ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
                                    </form>
                                @endif
                            @endif
                            <span class="badge rounded-pill {{ $statusClass($step3Status) }}">Step {{ $wizardStepNumber }}</span>
                            <span class="badge rounded-pill text-bg-dark">Draft</span>
                        </div>
                    </div>

                    <hr class="border-opacity-25">

                    {{-- Stepper mini --}}
                    <div class="d-flex align-items-center gap-2 small text-muted">
                        <span class="badge {{ $statusClass($step1Status) }} rounded-pill">1</span> Data Santri
                        <span class="mx-1">›</span>
                        <span class="badge {{ $statusClass($step2Status) }} rounded-pill">2</span> Orang Tua & Pernyataan
                        <span class="mx-1">›</span>
                        <span class="badge {{ $statusClass($step3Status) }} rounded-pill">3</span> Program & Dokumen
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ $step1Action }}" enctype="multipart/form-data">
                @csrf

                {{-- Card: Program --}}
                <div class="card trezo-card mb-3 {{ $edu === 'SMA_OLD' ? 'd-none' : '' }}" id="documentUploadCard">
                    <div class="card-body">
                        <h6 class="mb-3">A. Program</h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Jenis Pembiayaan <span class="text-danger">*</span></label>
                                <select name="funding_type" class="form-control form-select" required>
                                    <option value="" disabled
                                        {{ $currentFunding ? '' : 'selected' }}>Pilih...
                                    </option>
                                    <option value="mandiri"
                                        {{ $currentFunding === 'mandiri' ? 'selected' : '' }}>
                                        MANDIRI (Membayar Full)</option>
                                    @if ($isScholarshipAllowed)
                                        <option value="beasiswa"
                                            {{ $currentFunding === 'beasiswa' ? 'selected' : '' }}>
                                            BEASISWA</option>
                                    @endif
                                </select>
                                @if ($isScholarshipAllowed)
                                    <div class="form-text">Surat kurang mampu untuk beasiswa bersifat opsional dan bisa menyusul.</div>
                                @else
                                    <div class="form-text">Jalur beasiswa hanya tersedia pada periode 1.</div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Jenjang Pendidikan <span class="text-danger">*</span></label>
                                <select name="education_level" class="form-control form-select" required>
                                    <option value="" disabled
                                        {{ old('education_level', $registration->education_level) ? '' : 'selected' }}>
                                        Pilih...</option>
                                    <option value="SMP_NEW"
                                        {{ old('education_level', $registration->education_level) === 'SMP_NEW' ? 'selected' : '' }}>
                                        SMP - Pindahan/Santri Baru</option>
                                    <option value="SMA_NEW"
                                        {{ old('education_level', $registration->education_level) === 'SMA_NEW' ? 'selected' : '' }}>
                                        SMA - Pindahan/Santri Baru</option>
                                    <option value="SMA_OLD"
                                        {{ old('education_level', $registration->education_level) === 'SMA_OLD' ? 'selected' : '' }}>
                                        SMA - Santri Lama (SMP di Darussalam)</option>
                                </select>
                                <div class="form-text">Jika SMA dari luar darussalam, surat berkelakuan baik bisa menyusul.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Gelombang <span class="text-danger">*</span></label>
                                <input type="number" min="1" name="period_wave" class="form-control"
                                    value="{{ old('period_wave', $activePeriod?->wave ?? 1) }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Upload Dokumen --}}
                <div class="card trezo-card mb-3">
                    <div class="card-body">
                        <h6 class="mb-2">B. Upload Dokumen</h6>
                        <div class="text-muted small mb-3">
                            Format: JPG/PNG/PDF. Disarankan ukuran ≤ 10MB per file.
                        </div>

                        @php
                            $docs = $registration->documents->keyBy('type');
                            $statusBadge = function ($doc) {
                                if (!$doc) {
                                    return '<span class="badge text-bg-secondary">Tidak Ada</span>';
                                }
                                if ($doc->file_path) {
                                    return '<span class="badge text-bg-success">Sudah Diunggah</span>';
                                }
                                return $doc->is_required
                                    ? '<span class="badge text-bg-warning">Wajib</span>'
                                    : '<span class="badge text-bg-secondary">Opsional</span>';
                            };
                            $previewBtn = function ($doc) {
                                if (!$doc || !$doc->file_path) {
                                    return '';
                                }
                                $url = asset('storage/' . $doc->file_path);
                                return '<a class="btn btn-sm btn-outline-light" target="_blank" href="' .
                                    $url .
                                    '">Lihat</a>';
                            };
                        @endphp

                        <div class="row g-3">
                            {{-- Payment Proof --}}
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <label class="form-label mb-0">
                                        {{ $periodInfo?->payment_proof_label ?: 'Bukti Pembayaran Uang Pendaftaran (Rp. 150.000)' }}
                                        <span class="text-danger">*</span>
                                        @if (!empty($periodInfo?->payment_proof_note))
                                            <div class="text-muted small" style="white-space: pre-line;">
                                                {{ $periodInfo->payment_proof_note }}
                                            </div>
                                        @endif
                                    </label>
                                    <div class="d-flex gap-2">
                                        {!! $statusBadge($docs['PAYMENT_PROOF'] ?? null) !!}
                                        {!! $previewBtn($docs['PAYMENT_PROOF'] ?? null) !!}
                                    </div>
                                </div>
                                <input class="form-control mt-2" type="file" name="payment_proof"
                                    accept=".jpg,.jpeg,.png,.pdf">
                            </div>

                            {{-- KK --}}
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <label class="form-label mb-0">Foto Kartu Keluarga (KK) <span
                                            class="text-danger">*</span></label>
                                    <div class="d-flex gap-2">
                                        {!! $statusBadge($docs['KK'] ?? null) !!}
                                        {!! $previewBtn($docs['KK'] ?? null) !!}
                                    </div>
                                </div>
                                <input class="form-control mt-2" type="file" name="kk"
                                    accept=".jpg,.jpeg,.png,.pdf">
                            </div>

                            {{-- Akta --}}
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <label class="form-label mb-0">Foto Akta Kelahiran (Ananda) <span
                                            class="text-danger">*</span></label>
                                    <div class="d-flex gap-2">
                                        {!! $statusBadge($docs['BIRTH_CERT'] ?? null) !!}
                                        {!! $previewBtn($docs['BIRTH_CERT'] ?? null) !!}
                                    </div>
                                </div>
                                <input class="form-control mt-2" type="file" name="birth_cert"
                                    accept=".jpg,.jpeg,.png,.pdf">
                            </div>

                            {{-- KTP Ayah --}}
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <label class="form-label mb-0">Foto KTP Ayah <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-2">
                                        {!! $statusBadge($docs['KTP_FATHER'] ?? null) !!}
                                        {!! $previewBtn($docs['KTP_FATHER'] ?? null) !!}
                                    </div>
                                </div>
                                <input class="form-control mt-2" type="file" name="ktp_father"
                                    accept=".jpg,.jpeg,.png,.pdf">
                            </div>

                            {{-- KTP Ibu --}}
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <label class="form-label mb-0">Foto KTP Ibu <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-2">
                                        {!! $statusBadge($docs['KTP_MOTHER'] ?? null) !!}
                                        {!! $previewBtn($docs['KTP_MOTHER'] ?? null) !!}
                                    </div>
                                </div>
                                <input class="form-control mt-2" type="file" name="ktp_mother"
                                    accept=".jpg,.jpeg,.png,.pdf">
                            </div>

                            {{-- SKTM --}}
                            <div class="col-12 {{ $currentFunding === 'beasiswa' ? '' : 'd-none' }}"
                                id="sktmField">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <label class="form-label mb-0">
                                        Surat Kurang Mampu RT/Lurah/Camat (khusus Beasiswa, bisa menyusul)
                                    </label>
                                    <div class="d-flex gap-2">
                                        {!! $statusBadge($docs['SKTM'] ?? null) !!}
                                        {!! $previewBtn($docs['SKTM'] ?? null) !!}
                                    </div>
                                </div>
                                <input class="form-control mt-2" type="file" name="sktm"
                                    accept=".jpg,.jpeg,.png,.pdf">
                            </div>

                            {{-- Good Behavior --}}
                            <div class="col-12 {{ str_starts_with($edu, 'SMA') ? '' : 'd-none' }}" id="goodBehaviorField">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <label class="form-label mb-0">
                                        Surat Keterangan Berkelakuan Baik (dari sekolah SMP)
                                        <span class="text-muted small d-block">Khusus yang mendaftar SMA kelas 1 dari luar
                                            darussalam, bisa menyusul</span>
                                    </label>
                                    <div class="d-flex gap-2">
                                        {!! $statusBadge($docs['GOOD_BEHAVIOR'] ?? null) !!}
                                        {!! $previewBtn($docs['GOOD_BEHAVIOR'] ?? null) !!}
                                    </div>
                                </div>
                                <input class="form-control mt-2" type="file" name="good_behavior"
                                    accept=".jpg,.jpeg,.png,.pdf">
                            </div>
                        </div>

                        <hr class="border-opacity-25 mt-4">

                        <div class="d-flex justify-content-between gap-2">
                            <a href="{{ $step2Url }}" class="btn btn-outline-light">Kembali Step 2</a>
                            <button type="submit" class="btn btn-primary px-4">
                                {{ $submitLabel }}
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card trezo-card mb-3 {{ $edu === 'SMA_OLD' ? 'd-none' : '' }}" id="documentChecklistCard">
                <div class="card-body">
                    <h6 class="mb-2">Checklist Tahap 1</h6>
                    <div class="text-muted small mb-3">Pastikan dokumen wajib sudah diunggah.</div>

                    @php
                        $requiredTypes = ['PAYMENT_PROOF', 'KK', 'BIRTH_CERT', 'KTP_FATHER', 'KTP_MOTHER'];
                        $documentLabels = [
                            'PAYMENT_PROOF' => 'Bukti Pembayaran',
                            'KK' => 'Kartu Keluarga',
                            'BIRTH_CERT' => 'Akta Kelahiran',
                            'KTP_FATHER' => 'KTP Ayah',
                            'KTP_MOTHER' => 'KTP Ibu',
                        ];
                        $missing = [];
                        foreach ($requiredTypes as $t) {
                            if (empty($docs[$t]?->file_path)) {
                                $missing[] = $t;
                            }
                        }
                    @endphp

                    <ul class="list-group list-group-flush">
                        @foreach ($requiredTypes as $t)
                            @php $ok = !empty($docs[$t]?->file_path); @endphp
                            <li
                                class="list-group-item bg-transparent text-light d-flex justify-content-between align-items-center">
                                <span>
                                    {{ $documentLabels[$t] ?? $t }}
                                </span>
                                @if ($ok)
                                    <span class="badge text-bg-success">Sudah</span>
                                @else
                                    <span class="badge text-bg-warning">Belum Diunggah</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    <div class="alert alert-dark border-0 rounded-3 mt-3 mb-0">
                        <div class="small text-muted">
                            * Dokumen tambahan (SKTM / Berkelakuan Baik) akan menyesuaikan pilihan program.
                        </div>
                    </div>
                </div>
            </div>

            <div class="card trezo-card">
                <div class="card-body">
                    <h6 class="mb-2">Catatan Penting</h6>
                    <ul class="small text-muted mb-0">
                        <li>Isi data sesuai KK (huruf besar/kecil mengikuti KK).</li>
                        <li>Nomor HP wajib aktif dan WhatsApp.</li>
                        <li>Jangan ada kolom kosong saat nanti submit final.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fundingSelect = document.querySelector('select[name="funding_type"]');
            const educationSelect = document.querySelector('select[name="education_level"]');
            const sktmField = document.getElementById('sktmField');
            const goodBehaviorField = document.getElementById('goodBehaviorField');
            const documentUploadCard = document.getElementById('documentUploadCard');
            const documentChecklistCard = document.getElementById('documentChecklistCard');
            const submitButton = document.querySelector('button[type="submit"]');

            if (!fundingSelect || !educationSelect || !sktmField || !goodBehaviorField || !submitButton) {
                return;
            }

            const updateDocVisibility = () => {
                const funding = fundingSelect.value;
                const education = educationSelect.value || '';
                const isSantriLama = education === 'SMA_OLD';
                const scholarshipAllowed = {{ $isScholarshipAllowed ? 'true' : 'false' }};

                if (!scholarshipAllowed && funding === 'beasiswa') {
                    fundingSelect.value = 'mandiri';
                }

                sktmField.classList.toggle('d-none', funding !== 'beasiswa');
                goodBehaviorField.classList.toggle('d-none', !education.startsWith('SMA') || isSantriLama);

                if (documentUploadCard) {
                    documentUploadCard.classList.toggle('d-none', isSantriLama);
                }

                if (documentChecklistCard) {
                    documentChecklistCard.classList.toggle('d-none', isSantriLama);
                }

                submitButton.textContent = isSantriLama
                    ? 'Simpan & Lanjut Form Santri Lama'
                    : 'Submit Final';
            };

            fundingSelect.addEventListener('change', updateDocVisibility);
            educationSelect.addEventListener('change', updateDocVisibility);
            updateDocVisibility();
        });
    </script>
@endsection
