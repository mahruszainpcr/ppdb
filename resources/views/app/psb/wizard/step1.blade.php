@extends('layouts.app')

@section('title', 'Wizard PSB - Step 1')

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
                            <h5 class="mb-1">Pendaftaran Santri Baru</h5>
                            <div class="text-muted small">
                                Nomor Pendaftaran: <span class="fw-semibold">{{ $registration->registration_no }}</span>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <span class="badge rounded-pill text-bg-secondary">Step 1</span>
                            <span class="badge rounded-pill text-bg-dark">Draft</span>
                        </div>
                    </div>

                    <hr class="border-opacity-25">

                    {{-- Stepper mini --}}
                    <div class="d-flex align-items-center gap-2 small text-muted">
                        <span class="badge text-bg-primary rounded-pill">1</span> Program & Dokumen
                        <span class="mx-1">›</span>
                        <span class="badge text-bg-secondary rounded-pill">2</span> Data Santri
                        <span class="mx-1">›</span>
                        <span class="badge text-bg-secondary rounded-pill">3</span> Orang Tua & Pernyataan
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('psb.step1') }}" enctype="multipart/form-data">
                @csrf

                {{-- Card: Program --}}
                <div class="card trezo-card mb-3">
                    <div class="card-body">
                        <h6 class="mb-3">A. Program</h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Jenis Pembiayaan <span class="text-danger">*</span></label>
                                <select name="funding_type" class="form-select" required>
                                    <option value="" disabled
                                        {{ old('funding_type', $registration->funding_type) ? '' : 'selected' }}>Pilih...
                                    </option>
                                    <option value="mandiri"
                                        {{ old('funding_type', $registration->funding_type) === 'mandiri' ? 'selected' : '' }}>
                                        MANDIRI (Membayar Full)</option>
                                    <option value="beasiswa"
                                        {{ old('funding_type', $registration->funding_type) === 'beasiswa' ? 'selected' : '' }}>
                                        BEASISWA</option>
                                </select>
                                <div class="form-text">Beasiswa memerlukan Surat Kurang Mampu (bisa menyusul).</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Jenjang Pendidikan <span class="text-danger">*</span></label>
                                <select name="education_level" class="form-select" required>
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
                                    value="{{ old('period_wave', $activePeriod?->wave ?? 1) }}" required>
                                <div class="form-text">Nanti bisa dibuat dinamis dari admin periode.</div>
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
                                    return '<span class="badge text-bg-secondary">N/A</span>';
                                }
                                if ($doc->file_path) {
                                    return '<span class="badge text-bg-success">Uploaded</span>';
                                }
                                return $doc->is_required
                                    ? '<span class="badge text-bg-warning">Required</span>'
                                    : '<span class="badge text-bg-secondary">Optional</span>';
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
                                        Bukti Pembayaran Uang Pendaftaran (Rp. 150.000)
                                        <span class="text-danger">*</span>
                                        <div class="text-muted small">
                                            No Rek. (BSI 7145-1777-28) Kode Bank 451 An. Al Marwa SPP
                                        </div>
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
                            <div class="col-12">
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
                            <div class="col-12">
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

                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                Simpan & Lanjut Step 2
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card trezo-card mb-3">
                <div class="card-body">
                    <h6 class="mb-2">Checklist Step 1</h6>
                    <div class="text-muted small mb-3">Pastikan dokumen wajib sudah diunggah.</div>

                    @php
                        $requiredTypes = ['PAYMENT_PROOF', 'KK', 'BIRTH_CERT', 'KTP_FATHER', 'KTP_MOTHER'];
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
                                    {{ str_replace('_', ' ', $t) }}
                                </span>
                                @if ($ok)
                                    <span class="badge text-bg-success">OK</span>
                                @else
                                    <span class="badge text-bg-warning">Missing</span>
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
@endsection
