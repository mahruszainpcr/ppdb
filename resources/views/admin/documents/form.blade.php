@extends('layouts.app')
@section('title', $document->exists ? 'Edit Dokumen' : 'Tambah Dokumen')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">{{ $document->exists ? 'Edit Dokumen' : 'Tambah Dokumen' }}</h4>
            <div class="text-muted">Upload file PDF dan atur kategori dokumen untuk kebutuhan admin maupun ustadz.</div>
        </div>
        <div>
            <a class="btn btn-outline-light btn-sm" href="{{ route('admin.documents.index') }}">Kembali</a>
        </div>
    </div>

    <div class="card trezo-card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data"
                action="{{ $document->exists ? route('admin.documents.update', $document) : route('admin.documents.store') }}">
                @csrf
                @if ($document->exists)
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Kategori Dokumen</label>
                        @php
                            $selectedCategory = old('category', $document->category === 'lainnya' && $document->custom_category ? $document->custom_category : $document->category);
                        @endphp
                        <select name="category" id="categoryField" class="form-select" required>
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $value => $label)
                                <option value="{{ $value }}" @selected($selectedCategory === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6" id="customCategoryWrap" style="display: none;">
                        <label class="form-label">Kategori Lainnya</label>
                        <input type="text" name="custom_category" id="customCategoryField" class="form-control"
                            placeholder="Tulis kategori dokumen lainnya"
                            value="{{ old('custom_category', $document->custom_category) }}">
                        @error('custom_category')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="description" class="form-control" rows="5"
                        placeholder="Tulis keterangan singkat dokumen, misalnya tujuan, isi ringkas, atau catatan penting.">{{ old('description', $document->description) }}</textarea>
                    @error('description')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-8">
                        <label class="form-label">File PDF</label>
                        <input type="file" name="file" class="form-control" accept="application/pdf" {{ $document->exists ? '' : 'required' }}>
                        <div class="form-text">
                            Format wajib PDF, maksimal 10 MB.
                            @if ($document->exists && $document->original_name)
                                File saat ini: <strong>{{ $document->original_name }}</strong>
                            @endif
                        </div>
                        @error('file')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Siapa Upload Dokumen</label>
                        <input type="text" class="form-control"
                            value="{{ $document->uploader?->name ?? auth()->user()?->name ?? '-' }}" disabled>
                        <div class="form-text">
                            @if ($document->exists)
                                Akan berubah jika file PDF diganti oleh akun lain.
                            @else
                                Otomatis diambil dari akun login saat upload.
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-primary">{{ $document->exists ? 'Simpan Perubahan' : 'Simpan Dokumen' }}</button>
                    <a class="btn btn-outline-light" href="{{ route('admin.documents.index') }}">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const categoryField = document.getElementById('categoryField');
            const customCategoryWrap = document.getElementById('customCategoryWrap');
            const customCategoryField = document.getElementById('customCategoryField');

            function syncCustomCategory() {
                const isOther = categoryField.value === 'lainnya';
                customCategoryWrap.style.display = isOther ? '' : 'none';
                customCategoryField.required = isOther;
                if (!isOther) {
                    customCategoryField.value = '';
                }
            }

            categoryField.addEventListener('change', syncCustomCategory);
            syncCustomCategory();
        });
    </script>
@endpush
