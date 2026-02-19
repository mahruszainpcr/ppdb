@extends('layouts.app')
@section('title', 'Postingan Berita')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Postingan Berita</h4>
            <div class="text-muted">Kelola konten berita di landing page.</div>
        </div>
        <div>
            <a class="btn btn-primary btn-sm" href="{{ route('admin.news-posts.create') }}">Tambah Berita</a>
        </div>
    </div>

    <div class="card trezo-card mb-3">
        <div class="card-body">
            <form class="row g-2" id="newsPostsFilterForm">
                <div class="col-md-4">
                    <input name="search" class="form-control" placeholder="Cari judul / kategori">
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select">
                        <option value="">Kategori (Semua)</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Status (Semua)</option>
                        <option value="published">Publish</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Terapkan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card trezo-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle" id="newsPostsTable">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Publish</th>
                            <th>Author</th>
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
            const table = $('#newsPostsTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                lengthChange: true,
                pageLength: 15,
                ajax: {
                    url: @json(route('admin.news-posts.data')),
                    data: function (d) {
                        const form = document.getElementById('newsPostsFilterForm');
                        d.search = form.querySelector('input[name="search"]').value;
                        d.category_id = form.querySelector('select[name="category_id"]').value;
                        d.status = form.querySelector('select[name="status"]').value;
                    }
                },
                columns: [
                    { data: 'title' },
                    { data: 'category', orderable: false, searchable: false },
                    { data: 'status', orderable: false, searchable: false },
                    { data: 'published_at' },
                    { data: 'author', orderable: false, searchable: false },
                    { data: 'actions', orderable: false, searchable: false, className: 'text-end' }
                ]
            });

            document.getElementById('newsPostsFilterForm').addEventListener('submit', function (e) {
                e.preventDefault();
                table.ajax.reload();
            });
        });
    </script>
@endpush
