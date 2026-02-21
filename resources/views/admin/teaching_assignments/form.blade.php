@extends('layouts.app')
@section('title', $assignment->exists ? 'Edit Ustadz Pengampu' : 'Tambah Ustadz Pengampu')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">{{ $assignment->exists ? 'Edit Ustadz Pengampu' : 'Tambah Ustadz Pengampu' }}</h4>
            <div class="text-muted">Pilih ustadz, mapel/kitab, kelas, semester, dan tahun ajaran.</div>
        </div>
        <div>
            <a class="btn btn-outline-light btn-sm" href="{{ route('admin.teaching-assignments.index') }}">Kembali</a>
        </div>
    </div>

    <div class="card trezo-card">
        <div class="card-body">
            <form method="POST"
                action="{{ $assignment->exists ? route('admin.teaching-assignments.update', $assignment) : route('admin.teaching-assignments.store') }}">
                @csrf
                @if ($assignment->exists)
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Ustadz</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Pilih Ustadz</option>
                            @foreach ($ustadzList as $u)
                                <option value="{{ $u->id }}" @selected(old('user_id', $assignment->user_id) == $u->id)>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mapel/Kitab</label>
                        <select name="subject_id" class="form-select" required>
                            <option value="">Pilih Mapel/Kitab</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected(old('subject_id', $assignment->subject_id) == $subject->id)>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kelas/Tingkatan</label>
                        <select name="class_level_id" class="form-select" required>
                            <option value="">Pilih Kelas</option>
                            @foreach ($classLevels as $level)
                                <option value="{{ $level->id }}" @selected(old('class_level_id', $assignment->class_level_id) == $level->id)>
                                    {{ $level->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_level_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Semester</label>
                        <select name="semester_id" class="form-select" required>
                            <option value="">Pilih Semester</option>
                            @foreach ($semesters as $semester)
                                <option value="{{ $semester->id }}" @selected(old('semester_id', $assignment->semester_id) == $semester->id)>
                                    {{ $semester->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('semester_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tahun Ajaran</label>
                        <select name="academic_year_id" class="form-select" required>
                            <option value="">Pilih Tahun Ajaran</option>
                            @foreach ($academicYears as $year)
                                <option value="{{ $year->id }}" @selected(old('academic_year_id', $assignment->academic_year_id) == $year->id)>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive"
                        @checked(old('is_active', $assignment->exists ? $assignment->is_active : true))>
                    <label class="form-check-label" for="isActive">Aktif</label>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary">{{ $assignment->exists ? 'Simpan Perubahan' : 'Simpan' }}</button>
                    <a class="btn btn-outline-light" href="{{ route('admin.teaching-assignments.index') }}">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
