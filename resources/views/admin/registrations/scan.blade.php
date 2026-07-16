@extends('layouts.app')
@section('title', 'Scan QR Pendaftaran')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xxl-8 col-xl-9">
            <div class="card trezo-card overflow-hidden">
                <div class="card-body p-0">
                    <div class="p-4 p-md-5 text-white"
                        style="background: linear-gradient(135deg, #0f172a 0%, #14532d 55%, #16a34a 100%);">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                            <div>
                                <span class="badge bg-light text-success mb-3">Admin Only</span>
                                <h3 class="mb-2">Scan QR Pendaftaran</h3>
                                <p class="mb-0 text-white-50">
                                    Gunakan kamera HP atau laptop untuk scan QR bukti pendaftaran. Setelah terbaca,
                                    sistem akan langsung membuka detail formulir calon santri.
                                </p>
                            </div>
                            <a href="{{ route('admin.registrations.index') }}" class="btn btn-light btn-sm text-success">
                                Kembali ke Data Pendaftar
                            </a>
                        </div>
                    </div>

                    <div class="p-4 p-md-5">
                        <div class="row g-4">
                            <div class="col-lg-7">
                                <div class="border rounded-4 p-3 bg-light">
                                    <div id="qr-scanner"
                                        class="rounded-4 overflow-hidden bg-dark d-flex align-items-center justify-content-center"
                                        style="min-height: 360px;">
                                        <div class="text-center text-white px-4" id="scanner-placeholder">
                                            <div class="fs-5 fw-semibold mb-2">Kamera siap digunakan</div>
                                            <div class="small text-white-50">
                                                Izinkan akses kamera pada browser untuk mulai scan QR pendaftaran.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-5">
                                <div class="border rounded-4 p-4 h-100">
                                    <h5 class="mb-3">Panduan Cepat</h5>
                                    <ol class="text-muted ps-3 mb-4">
                                        <li>Buka halaman ini dari akun admin.</li>
                                        <li>Arahkan kamera ke QR bukti pendaftaran wali.</li>
                                        <li>Setelah kode terbaca, halaman detail pendaftar akan terbuka otomatis.</li>
                                    </ol>

                                    <label for="manual-scan-url" class="form-label fw-semibold">Atau tempel link hasil
                                        scan</label>
                                    <input type="text" id="manual-scan-url" class="form-control mb-3"
                                        placeholder="https://domain/admin/registrations/scan/DS-2026-XXXXXXX">

                                    <button type="button" id="open-scan-url" class="btn btn-primary w-100 mb-3">
                                        Buka Detail Pendaftaran
                                    </button>

                                    <div class="small text-muted">
                                        Halaman ini hanya tersedia untuk akun admin yang sudah login.
                                    </div>
                                    <div id="scan-feedback" class="alert alert-warning d-none mt-3 mb-0"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            const scannerRoot = document.getElementById('qr-scanner');
            const feedback = document.getElementById('scan-feedback');
            const manualInput = document.getElementById('manual-scan-url');
            const openButton = document.getElementById('open-scan-url');
            const allowedPrefix = @json(url('/admin/registrations/scan/'));

            function showFeedback(message) {
                feedback.textContent = message;
                feedback.classList.remove('d-none');
            }

            function openScanUrl(rawValue) {
                const value = (rawValue || '').trim();

                if (!value) {
                    showFeedback('Link scan belum diisi.');
                    return;
                }

                if (!value.startsWith(allowedPrefix)) {
                    showFeedback('QR/link tidak dikenali sebagai link scan pendaftaran admin.');
                    return;
                }

                window.location.href = value;
            }

            openButton.addEventListener('click', function() {
                openScanUrl(manualInput.value);
            });

            manualInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    openScanUrl(manualInput.value);
                }
            });

            if (!('BarcodeDetector' in window) || !navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                showFeedback('Browser ini belum mendukung scan kamera langsung. Gunakan kamera HP biasa lalu buka link hasil scan di sini.');
                return;
            }

            const detector = new BarcodeDetector({
                formats: ['qr_code']
            });

            let activeStream = null;
            const video = document.createElement('video');
            video.setAttribute('playsinline', 'true');
            video.autoplay = true;
            video.muted = true;
            video.style.width = '100%';
            video.style.height = '100%';
            video.style.objectFit = 'cover';

            scannerRoot.innerHTML = '';
            scannerRoot.appendChild(video);

            function stopStream() {
                if (activeStream) {
                    activeStream.getTracks().forEach(track => track.stop());
                    activeStream = null;
                }
            }

            async function scanLoop() {
                if (!video.readyState || video.readyState < 2) {
                    requestAnimationFrame(scanLoop);
                    return;
                }

                try {
                    const barcodes = await detector.detect(video);

                    if (barcodes.length > 0 && barcodes[0].rawValue) {
                        stopStream();
                        openScanUrl(barcodes[0].rawValue);
                        return;
                    }
                } catch (error) {
                    showFeedback('Kamera aktif, tetapi pembacaan QR gagal. Coba arahkan ulang kamera.');
                }

                requestAnimationFrame(scanLoop);
            }

            navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'environment'
                }
            }).then(function(stream) {
                activeStream = stream;
                video.srcObject = stream;
                video.onloadedmetadata = function() {
                    video.play();
                    requestAnimationFrame(scanLoop);
                };
            }).catch(function() {
                showFeedback('Akses kamera ditolak atau tidak tersedia. Anda masih bisa paste link hasil scan secara manual.');
            });

            window.addEventListener('beforeunload', stopStream);
        })();
    </script>
@endpush
