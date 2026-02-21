@extends('layouts.app')
@section('title', 'Master Semester')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Master Semester</h4>
            <div class="text-muted">Kelola daftar semester.</div>
        </div>
        <div>
            <a class="btn btn-primary btn-sm" href="{{ route('admin.semesters.create') }}">Tambah Semester</a>
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
                <table class="table table-hover mb-0 align-middle" id="semestersTable">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Kode</th>
                            <th>Urutan</th>
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
            const table = $('#semestersTable').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                lengthChange: true,
                pageLength: 15,
                ajax: {
                    url: @json(route('admin.semesters.data'))
                },
                columns: [
                    { data: 'name' },
                    { data: 'code' },
                    { data: 'sort_order' },
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
