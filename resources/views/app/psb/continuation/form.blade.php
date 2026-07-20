@extends('layouts.app')

@section('title', $pageTitle ?? 'Formulir Lanjutan Santri')

@php
    $continuation = $continuation ?? null;
    $isAdminMode = $isAdminMode ?? false;
    $existingSignatureUrl = $continuation?->signature_path ? asset('storage/' . $continuation->signature_path) : null;
    $paymentProofUrl = $continuation?->payment_proof_path ? asset('storage/' . $continuation->payment_proof_path) : null;
@endphp

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            @if (session('success'))
                <div class="alert alert-success border-0 rounded-3">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger border-0 rounded-3">
                    <div class="fw-semibold mb-1">Periksa kembali formulir berikut:</div>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card trezo-card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <div class="badge text-bg-success mb-2">Form Khusus</div>
                            <h4 class="mb-1">{{ $pageTitle }}</h4>
                            <div class="text-muted">{{ $pageSubtitle ?? '' }}</div>
                            <div class="text-muted small mt-2">
                                Nomor pendaftaran:
                                <span class="fw-semibold">{{ $registration->registration_no }}</span>
                                <span class="mx-2">•</span>
                                Tahun ajaran:
                                <span class="fw-semibold">{{ $registration->period?->academic_year ?? '2026/2027' }}</span>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ $backUrl }}" class="btn btn-outline-light btn-sm">Kembali</a>
                            @if ($downloadUrl)
                                <a href="{{ $downloadUrl }}" class="btn btn-outline-success btn-sm">Download PDF Terakhir</a>
                            @endif
                            @if ($isAdminMode)
                                <a href="{{ route('admin.registrations.proof.pdf', $registration) }}"
                                    class="btn btn-outline-primary btn-sm">PDF</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ $formAction }}" id="continuationForm" enctype="multipart/form-data">
                @csrf

                <div class="card trezo-card mb-3">
                    <div class="card-body">
                        <h5 class="mb-3">A. Data Santri</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control"
                                    value="{{ old('full_name', $continuation?->full_name) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select" required>
                                    <option value="" disabled {{ old('gender', $registration->gender) ? '' : 'selected' }}>
                                        Pilih...
                                    </option>
                                    <option value="male" {{ old('gender', $registration->gender) === 'male' ? 'selected' : '' }}>
                                        Ikhwan
                                    </option>
                                    <option value="female" {{ old('gender', $registration->gender) === 'female' ? 'selected' : '' }}>
                                        Akhwat
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kelas Terakhir</label>
                                <input type="text" name="last_class" class="form-control"
                                    value="{{ old('last_class', $continuation?->last_class ?? 'IX Wustho') }}" readonly>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Asrama <span class="text-danger">*</span></label>
                                <input type="text" name="dormitory" class="form-control"
                                    value="{{ old('dormitory', $continuation?->dormitory) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card trezo-card mb-3">
                    <div class="card-body">
                        <h5 class="mb-3">B. Data Orang Tua / Wali</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Ayah/Wali <span class="text-danger">*</span></label>
                                <input type="text" name="father_name" class="form-control"
                                    value="{{ old('father_name', $continuation?->father_name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nomor HP/WhatsApp Ayah <span class="text-danger">*</span></label>
                                <input type="text" name="father_phone" class="form-control"
                                    value="{{ old('father_phone', $continuation?->father_phone) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Ibu/Wali <span class="text-danger">*</span></label>
                                <input type="text" name="mother_name" class="form-control"
                                    value="{{ old('mother_name', $continuation?->mother_name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nomor HP/WhatsApp Ibu <span class="text-danger">*</span></label>
                                <input type="text" name="mother_phone" class="form-control"
                                    value="{{ old('mother_phone', $continuation?->mother_phone) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card trezo-card mb-3">
                    <div class="card-body">
                        <h5 class="mb-3">C. Pilihan Melanjutkan</h5>
                        <label class="border rounded-3 p-3 d-flex align-items-start gap-2">
                            <input class="form-check-input mt-1" type="checkbox" name="continue_to_ulya" value="1"
                                @checked(old('continue_to_ulya', $continuation?->continue_to_ulya ?? true))>
                            <span>Bersedia melanjutkan pendidikan di Ulya Mahad Darussalam Al Islami.</span>
                        </label>
                    </div>
                </div>

                <div class="card trezo-card mb-3">
                    <div class="card-body">
                        <h5 class="mb-3">D. Komitmen</h5>
                        <div class="d-grid gap-3">
                            <label class="border rounded-3 p-3 d-flex align-items-start gap-2">
                                <input class="form-check-input mt-1" type="checkbox" name="agree_rules" value="1"
                                    @checked(old('agree_rules', $continuation?->agree_rules ?? true))>
                                <span>Bersedia menaati seluruh peraturan Mahad Darussalam Al Islami.</span>
                            </label>

                            <label class="border rounded-3 p-3 d-flex align-items-start gap-2">
                                <input class="form-check-input mt-1" type="checkbox" name="agree_programs" value="1"
                                    @checked(old('agree_programs', $continuation?->agree_programs ?? true))>
                                <span>Bersedia mengikuti seluruh program pembelajaran, tahfidz, bahasa Arab, diniyah, dan
                                    pembinaan yang berlaku di jenjang Ulya.</span>
                            </label>

                            <label class="border rounded-3 p-3 d-flex align-items-start gap-2">
                                <input class="form-check-input mt-1" type="checkbox" name="agree_administration"
                                    value="1" @checked(old('agree_administration', $continuation?->agree_administration ?? true))>
                                <span>Bersedia menyelesaikan administrasi sesuai ketentuan Mahad. Uang masuk sarpras
                                    Rp1.500.000, seragam dan buku Rp1.750.000, SPP jalur mandiri Rp1.300.000, dan jalur
                                    beasiswa Rp650.000.</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card trezo-card mb-3">
                    <div class="card-body">
                        <h5 class="mb-3">E. Konfirmasi Data</h5>
                        <div class="d-grid gap-3">
                            <label class="border rounded-3 p-3 d-flex align-items-start gap-2">
                                <input class="form-check-input mt-1" type="radio" name="bedding_option" value="buy"
                                    @checked(old('bedding_option', $continuation?->bedding_option) === 'buy')>
                                <span>Saya juga akan membeli kasur, seprei, bantal, dan lemari seharga Rp1.100.000.</span>
                            </label>
                            <label class="border rounded-3 p-3 d-flex align-items-start gap-2">
                                <input class="form-check-input mt-1" type="radio" name="bedding_option" value="not_buy"
                                    @checked(old('bedding_option', $continuation?->bedding_option) === 'not_buy')>
                                <span>Saya tidak membeli kasur, seprei, bantal, dan lemari.</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card trezo-card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                            <div>
                                <h5 class="mb-1">F. Bukti Transfer Pendaftaran</h5>
                                <div class="text-muted small">
                                    Upload bukti transfer pembayaran pendaftaran Rp150.000.
                                    No Rek. BSI 7145-1777-28, Kode Bank 451, a.n. Al Marwa SPP.
                                </div>
                            </div>
                            @if ($paymentProofUrl)
                                <a href="{{ $paymentProofUrl }}" target="_blank" class="btn btn-outline-success btn-sm">Lihat File</a>
                            @endif
                        </div>

                        <label class="form-label">Bukti Transfer <span class="text-danger">*</span></label>
                        <input type="file" name="payment_proof" class="form-control" accept=".jpg,.jpeg,.png,.pdf"
                            {{ !$paymentProofUrl && !$isAdminMode ? 'required' : '' }}>
                        <div class="text-muted small mt-2">
                            Format file: JPG, PNG, atau PDF. Maksimal 10 MB.
                            {{ $paymentProofUrl ? 'Jika tidak diganti, file lama tetap dipakai.' : '' }}
                        </div>
                    </div>
                </div>

                <div class="card trezo-card mb-3">
                    <div class="card-body">
                        <h5 class="mb-3">Pernyataan</h5>
                        <div class="border rounded-3 p-3 bg-light-subtle">
                            Dengan ini saya menyatakan bahwa data yang saya berikan adalah benar dan saya mengajukan
                            permohonan untuk melanjutkan pendidikan dari jenjang Wustho ke Ulya di Mahad Darussalam Al
                            Islami. Saya tidak akan mengundurkan diri setelah menandatangani form ini.
                        </div>

                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                <label class="form-label mb-0">Tanda Tangan Orang Tua / Wali <span
                                        class="text-danger">*</span></label>
                                <button type="button" class="btn btn-outline-warning btn-sm" id="clearSignatureBtn">Hapus
                                    Tanda Tangan</button>
                            </div>

                            <div class="border rounded-3 bg-white p-2">
                                <canvas id="signaturePad" width="900" height="260"
                                    style="width:100%; height:260px; display:block; border-radius:12px; background:#fff;"></canvas>
                            </div>
                            <input type="hidden" name="signature_data" id="signatureData"
                                value="{{ old('signature_data') }}">
                            <div class="text-muted small mt-2">
                                Gunakan mouse atau sentuhan layar untuk tanda tangan orang tua / wali.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mb-4">
                    <a href="{{ $backUrl }}" class="btn btn-outline-light">Kembali</a>
                    <button type="submit" class="btn btn-primary px-4">{{ $submitLabel ?? 'Simpan' }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('signaturePad');
            const hiddenInput = document.getElementById('signatureData');
            const clearButton = document.getElementById('clearSignatureBtn');
            const form = document.getElementById('continuationForm');
            const ctx = canvas.getContext('2d');
            const existingSignatureUrl = @json($existingSignatureUrl);
            let drawing = false;
            let hasDrawn = false;

            const resizeCanvas = () => {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const width = canvas.offsetWidth;
                const height = canvas.offsetHeight;

                canvas.width = width * ratio;
                canvas.height = height * ratio;
                ctx.setTransform(1, 0, 0, 1, 0, 0);
                ctx.scale(ratio, ratio);
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
                ctx.lineWidth = 2.2;
                ctx.strokeStyle = '#173324';

                if (!hiddenInput.value) {
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, width, height);
                }
            };

            const setSignatureValue = () => {
                hiddenInput.value = canvas.toDataURL('image/png');
            };

            const fillWhiteBackground = () => {
                ctx.save();
                ctx.globalCompositeOperation = 'destination-over';
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.offsetWidth, canvas.offsetHeight);
                ctx.restore();
            };

            const getPoint = (event) => {
                const rect = canvas.getBoundingClientRect();
                const source = event.touches ? event.touches[0] : event;
                return {
                    x: source.clientX - rect.left,
                    y: source.clientY - rect.top,
                };
            };

            const startDrawing = (event) => {
                event.preventDefault();
                drawing = true;
                const point = getPoint(event);
                ctx.beginPath();
                ctx.moveTo(point.x, point.y);
            };

            const draw = (event) => {
                if (!drawing) {
                    return;
                }

                event.preventDefault();
                const point = getPoint(event);
                ctx.lineTo(point.x, point.y);
                ctx.stroke();
                hasDrawn = true;
            };

            const endDrawing = () => {
                if (!drawing) {
                    return;
                }

                drawing = false;
                fillWhiteBackground();
                setSignatureValue();
            };

            const clearSignature = () => {
                ctx.clearRect(0, 0, canvas.offsetWidth, canvas.offsetHeight);
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.offsetWidth, canvas.offsetHeight);
                hiddenInput.value = '';
                hasDrawn = false;
            };

            resizeCanvas();
            clearSignature();

            if (existingSignatureUrl && !hiddenInput.value) {
                const image = new Image();
                image.onload = function () {
                    clearSignature();
                    ctx.drawImage(image, 0, 0, canvas.offsetWidth, canvas.offsetHeight);
                    fillWhiteBackground();
                    setSignatureValue();
                    hasDrawn = true;
                };
                image.src = existingSignatureUrl;
            }

            if (hiddenInput.value) {
                const image = new Image();
                image.onload = function () {
                    clearSignature();
                    ctx.drawImage(image, 0, 0, canvas.offsetWidth, canvas.offsetHeight);
                    fillWhiteBackground();
                    setSignatureValue();
                    hasDrawn = true;
                };
                image.src = hiddenInput.value;
            }

            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', endDrawing);
            canvas.addEventListener('mouseleave', endDrawing);

            canvas.addEventListener('touchstart', startDrawing, {
                passive: false
            });
            canvas.addEventListener('touchmove', draw, {
                passive: false
            });
            canvas.addEventListener('touchend', endDrawing);

            clearButton.addEventListener('click', clearSignature);

            form.addEventListener('submit', function (event) {
                if (!hiddenInput.value || !hasDrawn) {
                    event.preventDefault();
                    alert('Tanda tangan santri wajib diisi terlebih dahulu.');
                }
            });

            window.addEventListener('resize', () => {
                const snapshot = hiddenInput.value;
                resizeCanvas();
                clearSignature();

                if (snapshot) {
                    const image = new Image();
                    image.onload = function () {
                        ctx.drawImage(image, 0, 0, canvas.offsetWidth, canvas.offsetHeight);
                        fillWhiteBackground();
                        setSignatureValue();
                        hasDrawn = true;
                    };
                    image.src = snapshot;
                }
            });
        });
    </script>
@endpush
