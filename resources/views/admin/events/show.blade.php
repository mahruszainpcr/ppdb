@extends('layouts.app')
@section('title', $event->name)

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

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">{{ $event->name }}</h4>
            <div class="text-muted">
                {{ $event->event_date?->format('d M Y') ?? 'Tanggal belum diatur' }}
                @if ($event->location)
                    • {{ $event->location }}
                @endif
            </div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-light btn-sm" href="{{ route('admin.events.index') }}">Kembali</a>
            <a class="btn btn-primary btn-sm" href="{{ route('admin.events.edit', $event) }}">Edit Event</a>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card trezo-card h-100">
                <div class="card-body">
                    <div class="text-muted small">Status Event</div>
                    <div class="fs-5 fw-semibold">{{ $event->is_active ? 'Aktif' : 'Nonaktif' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card trezo-card h-100">
                <div class="card-body">
                    <div class="text-muted small">Total Sudah Hadir</div>
                    <div class="fs-5 fw-semibold" id="attendanceCount">{{ $event->attendances_count }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card trezo-card h-100">
                <div class="card-body">
                    <div class="text-muted small">Catatan Event</div>
                    <div class="small">{{ $event->description ?: 'Belum ada catatan tambahan.' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card trezo-card mb-3">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="mb-1">Scanner Absensi Event</h5>
                            <div class="text-muted small">Satu peserta hanya bisa melakukan absensi satu kali di event ini.</div>
                        </div>
                        @if (!$event->is_active)
                            <span class="badge bg-warning text-dark">Event Nonaktif</span>
                        @endif
                    </div>

                    <div id="event-scanner"
                        class="rounded-4 overflow-hidden bg-dark d-flex align-items-center justify-content-center"
                        style="min-height: 340px;">
                        <div class="text-center text-white px-4" id="scanner-placeholder">
                            <div class="fs-5 fw-semibold mb-2">Scanner siap digunakan</div>
                            <div class="small text-white-50">Izinkan kamera browser untuk mulai scan QR peserta.</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="border rounded-4 p-4 bg-light h-100">
                        <h6 class="mb-3">Input Manual / Hasil Scan</h6>
                        <label class="form-label">Nomor pendaftaran, nomor HP orang tua, atau link QR</label>
                        <input type="text" id="scanPayload" class="form-control mb-3"
                            placeholder="Contoh: DS-2026-ABCDEFG, 0812xxxxxxx, atau link scan QR">

                        <button type="button" id="submitScan" class="btn btn-primary w-100 mb-3"
                            @disabled(!$event->is_active)>
                            Simpan Absensi
                        </button>

                        <div id="scanResult" class="alert d-none mb-0"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card trezo-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3 gap-2 flex-wrap">
                <h5 class="mb-0">Histori Kehadiran</h5>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a class="btn btn-sm btn-outline-success" href="{{ route('admin.events.attendances.export', $event) }}">Export Excel</a>
                    <div class="text-muted small">Terakhir scan tersimpan paling atas.</div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No. Pendaftaran</th>
                            <th>Nama Santri</th>
                            <th>Nama Wali</th>
                            <th>Waktu Scan</th>
                            <th>Petugas</th>
                        </tr>
                    </thead>
                    <tbody id="attendanceHistoryBody">
                        @forelse ($recentAttendances as $attendance)
                            <tr>
                                <td class="fw-semibold">{{ $attendance->registration->registration_no }}</td>
                                <td>{{ $attendance->registration->studentProfile?->full_name ?? $attendance->registration->santriContinuation?->full_name ?? '-' }}</td>
                                <td>{{ $attendance->registration->user?->name ?? '-' }}</td>
                                <td>{{ optional($attendance->scanned_at)->format('d M Y H:i:s') ?? '-' }}</td>
                                <td class="d-flex align-items-center justify-content-between gap-2">
                                    <span>{{ $attendance->scanner?->name ?? '-' }}</span>
                                    <form method="POST" action="{{ route('admin.events.attendance.destroy', [$event, $attendance]) }}" onsubmit="return confirm('Yakin hapus histori absensi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link btn-sm text-danger p-0">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr id="attendanceEmptyRow">
                                <td colspan="5" class="text-center text-muted py-4">Belum ada histori absensi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scanUrl = @json(route('admin.events.scan-attendance', $event));
            const csrfToken = @json(csrf_token());
            const isEventActive = @json($event->is_active);

            const payloadInput = document.getElementById('scanPayload');
            const submitButton = document.getElementById('submitScan');
            const resultBox = document.getElementById('scanResult');
            const scannerRoot = document.getElementById('event-scanner');
            const historyBody = document.getElementById('attendanceHistoryBody');
            const attendanceCount = document.getElementById('attendanceCount');
            let isProcessingScan = false;
            let lastScannedPayload = '';
            let lastScannedAt = 0;

            function setResult(type, message) {
                resultBox.className = 'alert mb-0';
                resultBox.classList.add(type === 'success' ? 'alert-success' : (type === 'warning' ? 'alert-warning' : 'alert-danger'));
                resultBox.textContent = message;
                resultBox.classList.remove('d-none');
            }

            function prependAttendanceRow(attendance) {
                const emptyRow = document.getElementById('attendanceEmptyRow');
                if (emptyRow) {
                    emptyRow.remove();
                }

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="fw-semibold">${attendance.registration_no}</td>
                    <td>${attendance.student_name}</td>
                    <td>${attendance.parent_name}</td>
                    <td>${attendance.scanned_at}</td>
                    <td>${attendance.scanned_by}</td>
                `;

                historyBody.prepend(row);
                attendanceCount.textContent = String(Number(attendanceCount.textContent || '0') + 1);
            }

            async function submitAttendance(payload) {
                if (!isEventActive) {
                    setResult('warning', 'Event sedang nonaktif. Aktifkan event terlebih dahulu sebelum scan.');
                    return;
                }

                const cleanPayload = (payload || '').trim();
                if (!cleanPayload) {
                    setResult('danger', 'Payload scan masih kosong.');
                    return;
                }

                 if (isProcessingScan) {
                    return;
                }

                const now = Date.now();
                if (cleanPayload === lastScannedPayload && (now - lastScannedAt) < 2500) {
                    return;
                }

                isProcessingScan = true;
                lastScannedPayload = cleanPayload;
                lastScannedAt = now;

                submitButton.disabled = true;

                try {
                    const response = await fetch(scanUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            payload: cleanPayload
                        }),
                    });

                    const data = await response.json();

                    if (response.ok && data.ok) {
                        setResult('success', `${data.message} ${data.attendance.student_name} (${data.attendance.registration_no})`);
                        prependAttendanceRow(data.attendance);
                        payloadInput.value = '';
                        return;
                    }

                    if (data.status === 'already_scanned') {
                        setResult('warning', `${data.message} ${data.attendance.student_name} pada ${data.attendance.scanned_at}.`);
                        return;
                    }

                    setResult('danger', data.message || 'Gagal memproses scan absensi.');
                } catch (error) {
                    setResult('danger', 'Terjadi kendala saat menyimpan absensi. Silakan coba lagi.');
                } finally {
                    submitButton.disabled = false;
                    isProcessingScan = false;
                }
            }

            submitButton.addEventListener('click', function() {
                submitAttendance(payloadInput.value);
            });

            payloadInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    submitAttendance(payloadInput.value);
                }
            });

            if (!('BarcodeDetector' in window) || !navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                setResult('warning', 'Browser ini belum mendukung scan kamera. Silakan gunakan input manual: nomor pendaftaran, nomor HP orang tua, atau link QR.');
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
                        payloadInput.value = barcodes[0].rawValue;
                        await submitAttendance(barcodes[0].rawValue);
                    }
                } catch (error) {
                    // keep loop alive
                }

                requestAnimationFrame(scanLoop);
            }

            const cameraConstraints = {
                video: {
                    facingMode: { ideal: 'environment' },
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            };

            navigator.mediaDevices.getUserMedia(cameraConstraints).then(function(stream) {
                activeStream = stream;
                video.srcObject = stream;
                video.onloadedmetadata = function() {
                    video.play();
                    requestAnimationFrame(scanLoop);
                };
            }).catch(function() {
                navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user',
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    }
                }).then(function(stream) {
                    activeStream = stream;
                    video.srcObject = stream;
                    video.onloadedmetadata = function() {
                        video.play();
                        requestAnimationFrame(scanLoop);
                    };
                }).catch(function() {
                    setResult('warning', 'Akses kamera ditolak. Mohon izinkan akses kamera di browser lalu gunakan input manual jika tetap tidak tersedia.');
                });
            });

            window.addEventListener('beforeunload', stopStream);
        });
    </script>
@endpush
