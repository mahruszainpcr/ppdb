@extends('layouts.app')
@section('title', 'Dokumen Internal')

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
            <h4 class="mb-0">Dokumen Internal</h4>
            <div class="text-muted">Kelola dokumen PDF untuk admin dan ustadz lengkap dengan kategori, keterangan, dan nama pengunggah.</div>
        </div>
        <div>
            <a class="btn btn-primary btn-sm" href="{{ route('admin.documents.create') }}">Tambah Dokumen</a>
        </div>
    </div>

    <div class="card trezo-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="adminDocumentsTable">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th>Nama File</th>
                            <th>Uploader</th>
                            <th>Diperbarui</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($documents as $document)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $document->category_label }}</span>
                                </td>
                                <td class="text-muted" style="min-width: 240px;">
                                    {{ \Illuminate\Support\Str::limit($document->description ?: '-', 120) }}
                                </td>
                                <td>{{ $document->original_name }}</td>
                                <td>{{ $document->uploader?->name ?? '-' }}</td>
                                <td>{{ optional($document->updated_at)->format('d M Y H:i') }}</td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end flex-wrap gap-2">
                                        <a class="btn btn-outline-light btn-sm" href="{{ route('admin.documents.show', $document) }}">Buka</a>
                                        <a class="btn btn-outline-primary btn-sm" target="_blank"
                                            href="{{ route('admin.documents.preview', $document) }}">Preview PDF</a>
                                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.documents.edit', $document) }}">Edit</a>
                                        <form method="POST" action="{{ route('admin.documents.destroy', $document) }}"
                                            onsubmit="return confirm('Hapus dokumen ini? file PDF juga akan ikut terhapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    Belum ada dokumen internal. Tambahkan dokumen PDF pertama untuk admin dan ustadz.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
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
            if (window.jQuery && $.fn.DataTable) {
                $('#adminDocumentsTable').DataTable({
                    searching: true,
                    lengthChange: true,
                    pageLength: 10,
                    ordering: true,
                    order: [[4, 'desc']],
                    columnDefs: [
                        { targets: 5, orderable: false, searchable: false }
                    ],
                    language: {
                        search: 'Cari:',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ dokumen',
                        infoEmpty: 'Belum ada dokumen',
                        zeroRecords: 'Dokumen tidak ditemukan',
                        paginate: {
                            first: 'Awal',
                            last: 'Akhir',
                            next: 'Berikutnya',
                            previous: 'Sebelumnya'
                        }
                    }
                });
            }
        });
    </script>
@endpush
