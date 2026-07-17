@extends('layouts.app')
@section('title', 'Data Pendaftar')

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
            <h4 class="mb-0">Data Pendaftar</h4>
            <div class="text-muted">Cari, filter, dan buka detail pendaftar.</div>
        </div>
        <div class="d-flex gap-2">
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('admin.registrations.proofs.download') }}" class="btn btn-primary btn-sm">
                    Download Bukti 100%
                </a>
            @endif
            <button class="btn btn-outline-light btn-sm" id="registrationsExportBtn">
                Download Excel
            </button>
        </div>
    </div>

    <div class="card trezo-card mb-3">
        <div class="card-body">
            <form class="row g-2" id="registrationsFilterForm">
                <div class="col-md-4">
                    <input name="search" class="form-control" placeholder="Cari: nama/no pendaftaran/no WA"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-control form-select">
                        <option value="">Status (Semua)</option>
                        @foreach (['draft', 'submitted', 'verified', 'revision_requested'] as $st)
                            <option value="{{ $st }}" @selected(request('status') === $st)>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="graduation_status" class="form-control form-select">
                        <option value="">Kelulusan (Semua)</option>
                        @foreach (['pending', 'lulus', 'tidak_lulus', 'cadangan'] as $gs)
                            <option value="{{ $gs }}" @selected(request('graduation_status') === $gs)>{{ $gs }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Terapkan</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.registrations.index') }}" class="btn btn-outline-light w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-3" id="registrationStatsCards">
        <div class="col-md-3 col-6">
            <div class="card trezo-card">
                <div class="card-body py-3">
                    <div class="text-muted small">Total Pendaftar</div>
                    <div class="fs-4 fw-semibold" id="statTotal">0</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card trezo-card">
                <div class="card-body py-3">
                    <div class="text-muted small">Lengkap</div>
                    <div class="fs-4 fw-semibold text-success" id="statLengkap">0</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card trezo-card">
                <div class="card-body py-3">
                    <div class="text-muted small">Kurang</div>
                    <div class="fs-4 fw-semibold text-warning" id="statKurang">0</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card trezo-card">
                <div class="card-body py-3">
                    <div class="text-muted small">Belum Isi</div>
                    <div class="fs-4 fw-semibold text-secondary" id="statBelumIsi">0</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card trezo-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle" id="registrationsTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Pendaftaran</th>
                            <th>Nama Santri</th>
                            <th>Nama Wali</th>
                            <th>Ikhwan/Akhwat</th>
                            <th>Asal SD</th>
                            <th>Status Pendaftaran</th>
                            <th>Tanggal Daftar</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const table = $('#registrationsTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                lengthChange: true,
                pageLength: 15,
                ajax: {
                    url: @json(route('admin.registrations.data')),
                    data: function (d) {
                        const form = document.getElementById('registrationsFilterForm');
                        d.search = form.querySelector('input[name="search"]').value;
                        d.status = form.querySelector('select[name="status"]').value;
                        d.graduation_status = form.querySelector('select[name="graduation_status"]').value;
                    }
                },
                columns: [
                    { data: 'row_no', orderable: false, searchable: false },
                    { data: 'registration_no' },
                    { data: 'student_name', orderable: false },
                    { data: 'parent_name', orderable: false },
                    { data: 'gender_group' },
                    { data: 'school_origin', orderable: false },
                    { data: 'completion_status', orderable: false, searchable: false },
                    { data: 'registered_at' },
                    { data: 'actions', orderable: false, searchable: false, className: 'text-end' }
                ],
                drawCallback: function () {
                    const json = table.ajax.json();
                    const stats = json?.stats ?? {};
                    document.getElementById('statTotal').textContent = stats.total ?? 0;
                    document.getElementById('statLengkap').textContent = stats.lengkap ?? 0;
                    document.getElementById('statKurang').textContent = stats.kurang ?? 0;
                    document.getElementById('statBelumIsi').textContent = stats.belum_isi ?? 0;
                }
            });

            document.getElementById('registrationsFilterForm').addEventListener('submit', function (e) {
                e.preventDefault();
                table.ajax.reload();
            });

            document.getElementById('registrationsExportBtn').addEventListener('click', function () {
                const form = document.getElementById('registrationsFilterForm');
                const params = new URLSearchParams();
                const searchValue = form.querySelector('input[name="search"]').value;
                const statusValue = form.querySelector('select[name="status"]').value;
                const graduationValue = form.querySelector('select[name="graduation_status"]').value;

                if (searchValue) params.set('search', searchValue);
                if (statusValue) params.set('status', statusValue);
                if (graduationValue) params.set('graduation_status', graduationValue);

                const baseUrl = @json(route('admin.registrations.export'));
                const url = params.toString() ? `${baseUrl}?${params.toString()}` : baseUrl;
                window.location.href = url;
            });
        });
    </script>
@endpush
