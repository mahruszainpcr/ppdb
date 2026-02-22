@extends('layouts.app')
@section('title', $schedule->exists ? 'Edit Jadwal Akademik' : 'Tambah Jadwal Akademik')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">{{ $schedule->exists ? 'Edit Jadwal Akademik' : 'Tambah Jadwal Akademik' }}</h4>
            <div class="text-muted">Atur mapel/kitab, kelas, semester, tahun ajaran, dan jam.</div>
        </div>
        <div>
            <a class="btn btn-outline-light btn-sm" href="{{ route('admin.academic-schedules.index') }}">Kembali</a>
        </div>
    </div>

    <div class="card trezo-card">
        <div class="card-body">
            <form method="POST"
                action="{{ $schedule->exists ? route('admin.academic-schedules.update', $schedule) : route('admin.academic-schedules.store') }}">
                @csrf
                @if ($schedule->exists)
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tahun Ajaran</label>
                        <select name="academic_year_id" class="form-select" required>
                            <option value="">Pilih Tahun Ajaran</option>
                            @foreach ($academicYears as $year)
                                <option value="{{ $year->id }}" @selected(old('academic_year_id', $schedule->academic_year_id) == $year->id)>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Semester</label>
                        <select name="semester_id" class="form-select" required>
                            <option value="">Pilih Semester</option>
                            @foreach ($semesters as $semester)
                                <option value="{{ $semester->id }}" @selected(old('semester_id', $schedule->semester_id) == $semester->id)>
                                    {{ $semester->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('semester_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Kelas/Tingkatan</label>
                        <select name="class_level_id" class="form-select" required>
                            <option value="">Pilih Kelas</option>
                            @foreach ($classLevels as $level)
                                <option value="{{ $level->id }}" @selected(old('class_level_id', $schedule->class_level_id) == $level->id)>
                                    {{ $level->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_level_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mapel/Kitab</label>
                        <select name="subject_id" class="form-select" required>
                            <option value="">Pilih Mapel/Kitab</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected(old('subject_id', $schedule->subject_id) == $subject->id)>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ustadz</label>
                        <select name="teacher_id" class="form-select" required>
                            <option value="">Pilih Ustadz</option>
                            @foreach ($ustadzList as $u)
                                <option value="{{ $u->id }}" @selected(old('teacher_id', $schedule->teacher_id) == $u->id)>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('teacher_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Hari</label>
                        <select name="day_of_week" class="form-select" required>
                            <option value="">Pilih Hari</option>
                            @foreach ($dayMap as $value => $label)
                                <option value="{{ $value }}" @selected(old('day_of_week', $schedule->day_of_week) == $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('day_of_week')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jam Mulai</label>
                        <input type="time" name="start_time" class="form-control" required
                            value="{{ old('start_time', $schedule->start_time) }}">
                        @error('start_time')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jam Selesai</label>
                        <input type="time" name="end_time" class="form-control" required
                            value="{{ old('end_time', $schedule->end_time) }}">
                        @error('end_time')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Ruangan (opsional)</label>
                        <input type="text" name="room" class="form-control"
                            value="{{ old('room', $schedule->room) }}" placeholder="Contoh: R-101">
                        @error('room')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Catatan (opsional)</label>
                        <input type="text" name="note" class="form-control"
                            value="{{ old('note', $schedule->note) }}" placeholder="Contoh: Praktikum / Tahfidz">
                        @error('note')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive"
                        @checked(old('is_active', $schedule->exists ? $schedule->is_active : true))>
                    <label class="form-check-label" for="isActive">Aktif</label>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary">{{ $schedule->exists ? 'Simpan Perubahan' : 'Simpan' }}</button>
                    <a class="btn btn-outline-light" href="{{ route('admin.academic-schedules.index') }}">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
