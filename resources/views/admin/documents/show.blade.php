@extends('layouts.app')
@section('title', 'Detail Dokumen')

@section('content')
    @if (session('success'))
        <div class="alert alert-success border-0 rounded-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Detail Dokumen</h4>
            <div class="text-muted">Preview PDF langsung dari browser untuk memudahkan admin dan ustadz mengecek isi dokumen.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-outline-light btn-sm" href="{{ route('admin.documents.index') }}">Kembali</a>
            <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.documents.edit', $document) }}">Edit</a>
            <a class="btn btn-primary btn-sm" target="_blank" href="{{ route('admin.documents.preview', $document) }}">Buka Preview PDF</a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-4">
            <div class="card trezo-card h-100">
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted small">Kategori</div>
                        <div class="fw-semibold">{{ $document->category_label }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Keterangan</div>
                        <div>{{ $document->description ?: '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Uploader</div>
                        <div>{{ $document->uploader?->name ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Nama File</div>
                        <div>{{ $document->original_name }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="text-muted small">Terakhir Diperbarui</div>
                        <div>{{ optional($document->updated_at)->format('d M Y H:i') }}</div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-outline-primary btn-sm" target="_blank"
                            href="{{ route('admin.documents.preview', $document) }}">Preview PDF</a>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.documents.download', $document) }}">Download</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card trezo-card">
                <div class="card-body">
                    <iframe src="{{ route('admin.documents.preview', $document) }}"
                        title="Preview {{ $document->original_name }}"
                        style="width: 100%; min-height: 78vh; border: 1px solid #dee2e6; border-radius: 12px;"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection
