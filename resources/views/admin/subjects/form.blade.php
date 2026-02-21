@extends('layouts.app')
@section('title', $subject->exists ? 'Edit Mapel/Kitab' : 'Tambah Mapel/Kitab')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">{{ $subject->exists ? 'Edit Mapel/Kitab' : 'Tambah Mapel/Kitab' }}</h4>
            <div class="text-muted">Lengkapi informasi mapel/kitab.</div>
        </div>
        <div>
            <a class="btn btn-outline-light btn-sm" href="{{ route('admin.subjects.index') }}">Kembali</a>
        </div>
    </div>

    <div class="card trezo-card">
        <div class="card-body">
            <form method="POST"
                action="{{ $subject->exists ? route('admin.subjects.update', $subject) : route('admin.subjects.store') }}">
                @csrf
                @if ($subject->exists)
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label class="form-label">Nama Mapel/Kitab</label>
                    <input type="text" name="name" class="form-control" required
                        value="{{ old('name', $subject->name) }}" placeholder="Contoh: Fiqih / Nahwu">
                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Jenis</label>
                        <select name="type" class="form-select" required>
                            <option value="mapel" @selected(old('type', $subject->type ?? 'mapel') === 'mapel')>Mapel</option>
                            <option value="kitab" @selected(old('type', $subject->type ?? 'mapel') === 'kitab')>Kitab</option>
                        </select>
                        @error('type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kode (opsional)</label>
                        <input type="text" name="code" class="form-control"
                            value="{{ old('code', $subject->code) }}" placeholder="Contoh: FQH">
                        @error('code')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Urutan</label>
                        <input type="number" name="sort_order" class="form-control" min="0" max="999"
                            value="{{ old('sort_order', $subject->sort_order ?? 0) }}">
                        @error('sort_order')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive"
                        @checked(old('is_active', $subject->exists ? $subject->is_active : true))>
                    <label class="form-check-label" for="isActive">Aktif</label>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary">{{ $subject->exists ? 'Simpan Perubahan' : 'Simpan' }}</button>
                    <a class="btn btn-outline-light" href="{{ route('admin.subjects.index') }}">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
