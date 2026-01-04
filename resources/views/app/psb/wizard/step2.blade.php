@extends('layouts.app')
@section('title', 'Wizard PSB - Step 2')

@section('content')
    @if (session('success'))
        <div class="alert alert-success border-0 rounded-3">
            {{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger border-0 rounded-3">
            <div class="fw-semibold mb-1">Periksa kembali isian Anda:</div>
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php $sp = $registration->studentProfile; @endphp

    <div class="card trezo-card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-1">Wizard PSB</h5>
                    <div class="text-muted small">Nomor Pendaftaran: <span
                            class="fw-semibold">{{ $registration->registration_no }}</span></div>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge rounded-pill text-bg-secondary">Step 2</span>
                </div>
            </div>
            <hr class="border-opacity-25">
            <div class="d-flex align-items-center gap-2 small text-muted">
                <span class="badge text-bg-success rounded-pill">1</span> Program & Dokumen
                <span class="mx-1">›</span>
                <span class="badge text-bg-primary rounded-pill">2</span> Data Santri
                <span class="mx-1">›</span>
                <span class="badge text-bg-secondary rounded-pill">3</span> Orang Tua & Pernyataan
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('psb.step2') }}">
        @csrf

        <div class="card trezo-card mb-3">
            <div class="card-body">
                <h6 class="mb-3">A. Identitas Calon Santri</h6>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Nama Lengkap (sesuai KK) <span class="text-danger">*</span></label>
                        <input name="full_name" class="form-control" value="{{ old('full_name', $sp->full_name ?? '') }}"
                            required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">NISN</label>
                        <input name="nisn" class="form-control" value="{{ old('nisn', $sp->nisn ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NIK (sesuai KK)</label>
                        <input name="nik" class="form-control" value="{{ old('nik', $sp->nik ?? '') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                        <input name="birth_place" class="form-control"
                            value="{{ old('birth_place', $sp->birth_place ?? '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" name="birth_date" class="form-control"
                            value="{{ old('birth_date', optional($sp?->birth_date)->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="gender" class="form-select" required>
                            <option value="" disabled {{ old('gender', $registration->gender) ? '' : 'selected' }}>
                                Pilih...</option>
                            <option value="male" {{ old('gender', $registration->gender) === 'male' ? 'selected' : '' }}>
                                Laki-Laki</option>
                            <option value="female"
                                {{ old('gender', $registration->gender) === 'female' ? 'selected' : '' }}>Perempuan
                            </option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Alamat Rumah (sesuai KK) <span class="text-danger">*</span></label>
                        <textarea name="address" class="form-control" rows="2" required>{{ old('address', $sp->address ?? '') }}</textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                        <input name="province" class="form-control" value="{{ old('province', $sp->province ?? '') }}"
                            required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kabupaten/Kota <span class="text-danger">*</span></label>
                        <input name="city" class="form-control" value="{{ old('city', $sp->city ?? '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                        <input name="district" class="form-control" value="{{ old('district', $sp->district ?? '') }}"
                            required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kode Pos</label>
                        <input name="postal_code" class="form-control"
                            value="{{ old('postal_code', $sp->postal_code ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card trezo-card mb-3">
            <div class="card-body">
                <h6 class="mb-3">B. Pendidikan & Data Tambahan</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Asal Sekolah <span class="text-danger">*</span></label>
                        <input name="school_origin" class="form-control"
                            value="{{ old('school_origin', $sp->school_origin ?? '') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Jumlah Saudara Kandung</label>
                        <input type="number" min="0" name="siblings_count" class="form-control"
                            value="{{ old('siblings_count', $sp->siblings_count ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Anak ke</label>
                        <input type="number" min="1" name="child_number" class="form-control"
                            value="{{ old('child_number', $sp->child_number ?? '') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Hobi <span class="text-danger">*</span></label>
                        <input name="hobby" class="form-control" value="{{ old('hobby', $sp->hobby ?? '') }}"
                            required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cita-cita <span class="text-danger">*</span></label>
                        <input name="ambition" class="form-control" value="{{ old('ambition', $sp->ambition ?? '') }}"
                            required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status Calon Santri <span class="text-danger">*</span></label>
                        <select name="orphan_status" class="form-select" required>
                            @php $os = old('orphan_status', $sp->orphan_status ?? 'both'); @endphp
                            <option value="both" {{ $os === 'both' ? 'selected' : '' }}>Masih memiliki kedua orangtua
                            </option>
                            <option value="yatim" {{ $os === 'yatim' ? 'selected' : '' }}>Anak Yatim</option>
                            <option value="piatu" {{ $os === 'piatu' ? 'selected' : '' }}>Anak Piatu</option>
                            <option value="yatim_piatu" {{ $os === 'yatim_piatu' ? 'selected' : '' }}>Yatim Piatu</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Golongan Darah</label>
                        <input name="blood_type" class="form-control"
                            value="{{ old('blood_type', $sp->blood_type ?? '') }}" placeholder="A / B / AB / O">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Agama <span class="text-danger">*</span></label>
                        <input name="religion" class="form-control"
                            value="{{ old('religion', $sp->religion ?? 'ISLAM') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kewarganegaraan <span class="text-danger">*</span></label>
                        <input name="nationality" class="form-control"
                            value="{{ old('nationality', $sp->nationality ?? 'INDONESIA') }}" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Penyakit berat yang pernah diderita (isi “Tidak” bila tidak ada) <span
                                class="text-danger">*</span></label>
                        <input name="medical_history" class="form-control"
                            value="{{ old('medical_history', $sp->medical_history ?? 'Tidak') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Motivasi Masuk Pondok <span class="text-danger">*</span></label>
                        @php $mot = old('motivation', $sp->motivation ?? 'self'); @endphp
                        <select name="motivation" class="form-select" required>
                            <option value="self" {{ $mot === 'self' ? 'selected' : '' }}>Keinginan Sendiri</option>
                            <option value="parents" {{ $mot === 'parents' ? 'selected' : '' }}>Keinginan Orangtua</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card trezo-card mb-3">
            <div class="card-body">
                <h6 class="mb-3">C. Tahfidz & Program</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Hafalan Al-Qur'an (Juz) <span class="text-danger">*</span></label>
                        @php $hz = old('quran_memorization_level', $sp->quran_memorization_level ?? 'lt_half'); @endphp
                        <select name="quran_memorization_level" class="form-select" required>
                            <option value="lt_half" {{ $hz === 'lt_half' ? 'selected' : '' }}>Kurang dari Setengah Juz
                            </option>
                            <option value="lt_one" {{ $hz === 'lt_one' ? 'selected' : '' }}>Kurang dari Satu Juz</option>
                            <option value="ge_one" {{ $hz === 'ge_one' ? 'selected' : '' }}>1 Juz atau Lebih</option>
                            <option value="ge_three" {{ $hz === 'ge_three' ? 'selected' : '' }}>3 Juz atau Lebih</option>
                            <option value="ge_five" {{ $hz === 'ge_five' ? 'selected' : '' }}>5 Juz atau Lebih</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Kemampuan Membaca Al-Qur'an <span class="text-danger">*</span></label>
                        @php $qr = old('quran_reading_level', $sp->quran_reading_level ?? 'none'); @endphp
                        <select name="quran_reading_level" class="form-select" required>
                            <option value="none" {{ $qr === 'none' ? 'selected' : '' }}>Belum Bisa Baca</option>
                            <option value="iqro" {{ $qr === 'iqro' ? 'selected' : '' }}>Iqro'</option>
                            <option value="fluent" {{ $qr === 'fluent' ? 'selected' : '' }}>Lancar</option>
                            <option value="fluent_tahsin" {{ $qr === 'fluent_tahsin' ? 'selected' : '' }}>Lancar dengan
                                Tahsin
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Program Pilihan <span class="text-danger">*</span></label>
                        @php $pc = old('program_choice', $sp->program_choice ?? 'mahad'); @endphp
                        <select name="program_choice" class="form-select" required>
                            <option value="mahad" {{ $pc === 'mahad' ? 'selected' : '' }}>Ma'had (target hafalan
                                persemester 1
                                Juz)</option>
                            <option value="takhosus" {{ $pc === 'takhosus' ? 'selected' : '' }}>Takhosus (target hafalan
                                persemester 2.5 Juz)</option>
                        </select>
                    </div>
                </div>

                <hr class="border-opacity-25">

                <div class="d-flex justify-content-between">
                    <a href="{{ route('psb.wizard', ['step' => 1]) }}" class="btn btn-outline-light">Kembali Step 1</a>
                    <button class="btn btn-primary px-4">Simpan & Lanjut Step 3</button>
                </div>
            </div>
        </div>

    </form>
@endsection
