@extends('layouts.app')
@section('title', $academicYear->exists ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">{{ $academicYear->exists ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran' }}</h4>
            <div class="text-muted">Lengkapi informasi tahun ajaran.</div>
        </div>
        <div>
            <a class="btn btn-outline-light btn-sm" href="{{ route('admin.academic-years.index') }}">Kembali</a>
        </div>
    </div>

    <div class="card trezo-card">
        <div class="card-body">
            <form method="POST"
                action="{{ $academicYear->exists ? route('admin.academic-years.update', $academicYear) : route('admin.academic-years.store') }}">
                @csrf
                @if ($academicYear->exists)
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label class="form-label">Nama Tahun Ajaran</label>
                    <input type="text" name="name" class="form-control" required
                        value="{{ old('name', $academicYear->name) }}" placeholder="Contoh: 2025/2026">
                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control"
                            value="{{ old('start_date', optional($academicYear->start_date)->format('Y-m-d')) }}">
                        @error('start_date')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="end_date" class="form-control"
                            value="{{ old('end_date', optional($academicYear->end_date)->format('Y-m-d')) }}">
                        @error('end_date')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive"
                        @checked(old('is_active', $academicYear->exists ? $academicYear->is_active : true))>
                    <label class="form-check-label" for="isActive">Aktif</label>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary">{{ $academicYear->exists ? 'Simpan Perubahan' : 'Simpan' }}</button>
                    <a class="btn btn-outline-light" href="{{ route('admin.academic-years.index') }}">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
