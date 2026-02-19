@extends('layouts.app')
@section('title', 'Kategori Berita')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Kategori Berita</h4>
            <div class="text-muted">Kelola kategori untuk postingan berita.</div>
        </div>
        <div>
            <a class="btn btn-primary btn-sm" href="{{ route('admin.news-categories.create') }}">Tambah Kategori</a>
        </div>
    </div>

    <div class="card trezo-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle" id="newsCategoriesTable">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Slug</th>
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

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#newsCategoriesTable').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                lengthChange: true,
                pageLength: 15,
                ajax: {
                    url: @json(route('admin.news-categories.data'))
                },
                columns: [
                    { data: 'name' },
                    { data: 'slug' },
                    { data: 'status', orderable: false, searchable: false },
                    { data: 'created_at' },
                    { data: 'actions', orderable: false, searchable: false, className: 'text-end' }
                ]
            });
        });
    </script>
@endpush
