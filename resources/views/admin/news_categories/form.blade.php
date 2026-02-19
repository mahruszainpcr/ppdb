@extends('layouts.app')
@section('title', $category->exists ? 'Edit Kategori' : 'Tambah Kategori')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">{{ $category->exists ? 'Edit Kategori' : 'Tambah Kategori' }}</h4>
            <div class="text-muted">Lengkapi informasi kategori berita.</div>
        </div>
        <div>
            <a class="btn btn-outline-light btn-sm" href="{{ route('admin.news-categories.index') }}">Kembali</a>
        </div>
    </div>

    <div class="card trezo-card">
        <div class="card-body">
            <form method="POST"
                action="{{ $category->exists ? route('admin.news-categories.update', $category) : route('admin.news-categories.store') }}">
                @csrf
                @if ($category->exists)
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="name" class="form-control" required
                        value="{{ old('name', $category->name) }}">
                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive"
                        @checked(old('is_active', $category->exists ? $category->is_active : true))>
                    <label class="form-check-label" for="isActive">Aktif</label>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary">{{ $category->exists ? 'Simpan Perubahan' : 'Simpan' }}</button>
                    <a class="btn btn-outline-light" href="{{ route('admin.news-categories.index') }}">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
