@extends('layouts.app')

@section('title', 'Pilih Jenis Pendaftaran')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card trezo-card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <div class="badge text-bg-primary mb-2">Mulai Pendaftaran</div>
                            <h4 class="mb-1">Pilih Jenis Formulir</h4>
                            <div class="text-muted">Silakan pilih alur yang sesuai agar form yang muncul langsung tepat.</div>
                        </div>
                        <a href="{{ route('app.dashboard') }}" class="btn btn-outline-light btn-sm">Kembali ke Dashboard</a>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card trezo-card h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="badge text-bg-success mb-3">Form Reguler</div>
                            <h5 class="mb-2">Pendaftaran Santri Baru / Pindahan</h5>
                            <div class="text-muted mb-3">
                                Untuk SMP baru, SMA baru, atau santri pindahan dengan alur wizard lengkap dan upload dokumen.
                            </div>
                            <ul class="text-muted small mb-4">
                                <li>Step program dan dokumen</li>
                                <li>Step data santri</li>
                                <li>Step data orang tua dan pernyataan</li>
                            </ul>
                            <div class="mt-auto">
                                <a href="{{ route('psb.new') }}" class="btn btn-primary">Buka Form Reguler</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card trezo-card h-100 border-success-subtle">
                        <div class="card-body d-flex flex-column">
                            <div class="badge text-bg-warning mb-3">Form Terpisah</div>
                            <h5 class="mb-2">SMA - Santri Lama (SMP di Darussalam)</h5>
                            <div class="text-muted mb-3">
                                Formulir lanjutan khusus santri Wustho yang akan melanjutkan ke Ulya. Tidak memakai wizard reguler.
                            </div>
                            <ul class="text-muted small mb-4">
                                <li>Satu form langsung selesai</li>
                                <li>Tanda tangan canvas di dalam form</li>
                                <li>Setelah simpan langsung download surat PDF</li>
                            </ul>
                            <div class="mt-auto">
                                <form method="POST" action="{{ route('psb.continuation.new') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Buka Form Santri Lama</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
