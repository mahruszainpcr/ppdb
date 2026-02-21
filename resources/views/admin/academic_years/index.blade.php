@extends('layouts.app')
@section('title', 'Tahun Ajaran')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Tahun Ajaran</h4>
            <div class="text-muted">Kelola master tahun ajaran.</div>
        </div>
        <div>
            <a class="btn btn-primary btn-sm" href="{{ route('admin.academic-years.create') }}">Tambah Tahun Ajaran</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card trezo-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle" id="academicYearsTable">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const table = $('#academicYearsTable').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                lengthChange: true,
                pageLength: 15,
                ajax: {
                    url: @json(route('admin.academic-years.data'))
                },
                columns: [
                    { data: 'name' },
                    { data: 'range', orderable: false, searchable: false },
                    { data: 'status', orderable: false, searchable: false },
                    { data: 'created_at' },
                    { data: 'actions', orderable: false, searchable: false, className: 'text-end' }
                ]
            });

            document.addEventListener('change', function (e) {
                const toggle = e.target.closest('.js-toggle-active');
                if (!toggle) return;
                const form = toggle.closest('form');
                if (form) {
                    form.submit();
                }
            });

            table.on('draw', function () {
                // keep event delegation active for toggles after redraw
            });
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endpush
