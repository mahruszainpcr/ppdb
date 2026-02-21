@extends('layouts.app')
@section('title', $semester->exists ? 'Edit Semester' : 'Tambah Semester')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">{{ $semester->exists ? 'Edit Semester' : 'Tambah Semester' }}</h4>
            <div class="text-muted">Lengkapi informasi semester.</div>
        </div>
        <div>
            <a class="btn btn-outline-light btn-sm" href="{{ route('admin.semesters.index') }}">Kembali</a>
        </div>
    </div>

    <div class="card trezo-card">
        <div class="card-body">
            <form method="POST"
                action="{{ $semester->exists ? route('admin.semesters.update', $semester) : route('admin.semesters.store') }}">
                @csrf
                @if ($semester->exists)
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label class="form-label">Nama Semester</label>
                    <input type="text" name="name" class="form-control" required
                        value="{{ old('name', $semester->name) }}" placeholder="Contoh: Semester Ganjil">
                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Kode (opsional)</label>
                        <input type="text" name="code" class="form-control"
                            value="{{ old('code', $semester->code) }}" placeholder="Contoh: GANJIL">
                        @error('code')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Urutan</label>
                        <input type="number" name="sort_order" class="form-control" min="0" max="999"
                            value="{{ old('sort_order', $semester->sort_order ?? 0) }}">
                        @error('sort_order')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive"
                        @checked(old('is_active', $semester->exists ? $semester->is_active : true))>
                    <label class="form-check-label" for="isActive">Aktif</label>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary">{{ $semester->exists ? 'Simpan Perubahan' : 'Simpan' }}</button>
                    <a class="btn btn-outline-light" href="{{ route('admin.semesters.index') }}">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
