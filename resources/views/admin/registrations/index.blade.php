@extends('layouts.app')
@section('title', 'Data Pendaftar')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Data Pendaftar</h4>
            <div class="text-muted">Cari, filter, dan buka detail pendaftar.</div>
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
                    <select name="status" class="form-select">
                        <option value="">Status (Semua)</option>
                        @foreach (['draft', 'submitted', 'verified', 'revision_requested'] as $st)
                            <option value="{{ $st }}" @selected(request('status') === $st)>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="graduation_status" class="form-select">
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

    <div class="card trezo-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle" id="registrationsTable">
                    <thead>
                        <tr>
                            <th>No Daftar</th>
                            <th>Nama Santri</th>
                            <th>No WA Wali</th>
                            <th>Jenjang</th>
                            <th>Status</th>
                            <th>Kelulusan</th>
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
                    { data: 'registration_no' },
                    { data: 'student_name', orderable: false },
                    { data: 'phone', orderable: false },
                    { data: 'education_level' },
                    { data: 'status', orderable: false, searchable: false },
                    { data: 'graduation_status', orderable: false, searchable: false },
                    { data: 'actions', orderable: false, searchable: false, className: 'text-end' }
                ]
            });

            document.getElementById('registrationsFilterForm').addEventListener('submit', function (e) {
                e.preventDefault();
                table.ajax.reload();
            });
        });
    </script>
@endpush
