@extends('layouts.app')
@section('title', 'Wizard PSB - Step 2')

@php
    $wizardMode = $wizardMode ?? 'parent';
    $wizardTitle = $wizardTitle ?? 'Wizard PSB';
    $step2Action = $step2Action ?? route('psb.step2');
    $step1Url = $step1Url ?? route('psb.wizard', ['step' => 1, 'registration' => $registration->id]);
    $listUrl = $listUrl ?? route('app.dashboard', ['registration' => $registration->id]);
    $detailUrl = $detailUrl ?? null;
    $deleteUrl = $deleteUrl ?? null;
    $showDeleteButton = $showDeleteButton ?? false;
    $wilayahOptionsUrl = $wilayahOptionsUrl ?? route('app.wilayah.options');
@endphp

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
                    <h5 class="mb-1">{{ $wizardTitle }}</h5>
                    <div class="text-muted small">Nomor Pendaftaran: <span
                            class="fw-semibold">{{ $registration->registration_no }}</span></div>
                </div>
                <div class="d-flex gap-2">
                    @if ($wizardMode === 'admin')
                        <a href="{{ $listUrl }}" class="btn btn-outline-light btn-sm">Kembali</a>
                        @if ($detailUrl)
                            <a href="{{ $detailUrl }}" class="btn btn-outline-light btn-sm">Detail</a>
                        @endif
                        @if ($showDeleteButton && $deleteUrl)
                            <form method="POST" action="{{ $deleteUrl }}"
                                onsubmit="return confirm('Yakin hapus data pendaftaran ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
                            </form>
                        @endif
                    @endif
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

    <form method="POST" action="{{ $step2Action }}">
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
                        <label class="form-label">NISN (Lihat rapor atau ijazah/SKL.)</label>
                        <input name="nisn" class="form-control" value="{{ old('nisn', $sp->nisn ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NIK (sesuai KK) <span class="text-danger">*</span></label>
                        <input name="nik" class="form-control" value="{{ old('nik', $sp->nik ?? '') }}" required>
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
                        <select name="gender" class="form-control form-select" required>
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

                    <div class="col-md-3">
                        <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                        <select name="province" id="provinceSelect" class="form-control form-select" required>
                            <option value="" disabled selected>Pilih provinsi...</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kabupaten/Kota <span class="text-danger">*</span></label>
                        <select name="city" id="regencySelect" class="form-control form-select" required disabled>
                            <option value="" disabled selected>Pilih provinsi dulu</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                        <select name="district" id="districtSelect" class="form-control form-select" required disabled>
                            <option value="" disabled selected>Pilih kabupaten/kota dulu</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Desa/Kelurahan <span class="text-danger">*</span></label>
                        <select name="village" id="villageSelect" class="form-control form-select" required disabled>
                            <option value="" disabled selected>Pilih kecamatan dulu</option>
                        </select>
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
                        <select name="orphan_status" class="form-control form-select" required>
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
                        <select name="motivation" class="form-control form-select" required>
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
                        <select name="quran_memorization_level" class="form-control form-select" required>
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
                        <select name="quran_reading_level" class="form-control form-select" required>
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
                        <select name="program_choice" class="form-control form-select" required>
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
                    <a href="{{ $step1Url }}" class="btn btn-outline-light">Kembali Step 1</a>
                    <button class="btn btn-primary px-4">Simpan & Lanjut Step 3</button>
                </div>
            </div>
        </div>

    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const provinceSelect = document.getElementById('provinceSelect');
            const regencySelect = document.getElementById('regencySelect');
            const districtSelect = document.getElementById('districtSelect');
            const villageSelect = document.getElementById('villageSelect');
            const optionsUrl = @json($wilayahOptionsUrl);

            const state = {
                provinceName: @json(old('province', $sp->province ?? '')),
                regencyName: @json(old('city', $sp->city ?? '')),
                districtName: @json(old('district', $sp->district ?? '')),
                villageName: @json(old('village', $sp->village ?? '')),
                provinceCode: '',
                regencyCode: '',
                districtCode: '',
            };

            const setSelectOptions = (selectEl, items, placeholder) => {
                selectEl.innerHTML = '';
                const ph = document.createElement('option');
                ph.value = '';
                ph.disabled = true;
                ph.selected = true;
                ph.textContent = placeholder;
                selectEl.appendChild(ph);

                items.forEach((item) => {
                    const opt = document.createElement('option');
                    opt.value = item.name;
                    opt.textContent = item.name;
                    opt.dataset.code = item.code;
                    selectEl.appendChild(opt);
                });
            };

            const setLoading = (selectEl, text) => {
                selectEl.innerHTML = '';
                const opt = document.createElement('option');
                opt.value = '';
                opt.disabled = true;
                opt.selected = true;
                opt.textContent = text;
                selectEl.appendChild(opt);
            };

            const fetchWilayah = async (level, parentCode = '') => {
                const params = new URLSearchParams({ level });
                if (parentCode) {
                    params.set('parent_code', parentCode);
                }

                const res = await fetch(`${optionsUrl}?${params.toString()}`);
                if (!res.ok) {
                    throw new Error('Gagal memuat data wilayah.');
                }

                return res.json();
            };

            const findCodeByName = (items, name) => {
                if (!name) {
                    return '';
                }

                const found = items.find((item) => item.name === name);
                return found ? found.code : '';
            };

            const selectByName = (selectEl, name) => {
                if (!name) {
                    return;
                }

                const option = Array.from(selectEl.options).find((opt) => opt.value === name);
                if (option) {
                    option.selected = true;
                }
            };

            const resetRegency = () => {
                setSelectOptions(regencySelect, [], 'Pilih provinsi dulu');
                regencySelect.disabled = true;
            };

            const resetDistrict = () => {
                setSelectOptions(districtSelect, [], 'Pilih kabupaten/kota dulu');
                districtSelect.disabled = true;
            };

            const resetVillage = () => {
                setSelectOptions(villageSelect, [], 'Pilih kecamatan dulu');
                villageSelect.disabled = true;
            };

            const loadProvinces = async () => {
                try {
                    setLoading(provinceSelect, 'Memuat provinsi...');
                    const items = await fetchWilayah('province');
                    setSelectOptions(provinceSelect, items, 'Pilih provinsi...');
                    provinceSelect.disabled = false;

                    selectByName(provinceSelect, state.provinceName);
                    state.provinceCode = findCodeByName(items, state.provinceName);

                    if (state.provinceCode) {
                        await loadRegencies(state.provinceCode);
                    } else {
                        resetRegency();
                        resetDistrict();
                        resetVillage();
                    }
                } catch (error) {
                    setLoading(provinceSelect, 'Gagal memuat provinsi');
                    provinceSelect.disabled = true;
                }
            };

            const loadRegencies = async (provinceCode) => {
                if (!provinceCode) {
                    resetRegency();
                    resetDistrict();
                    resetVillage();
                    return;
                }

                try {
                    setLoading(regencySelect, 'Memuat kabupaten/kota...');
                    regencySelect.disabled = true;
                    const items = await fetchWilayah('regency', provinceCode);
                    setSelectOptions(regencySelect, items, 'Pilih kabupaten/kota...');
                    regencySelect.disabled = false;

                    selectByName(regencySelect, state.regencyName);
                    state.regencyCode = findCodeByName(items, state.regencyName);

                    if (state.regencyCode) {
                        await loadDistricts(state.regencyCode);
                    } else {
                        resetDistrict();
                        resetVillage();
                    }
                } catch (error) {
                    setLoading(regencySelect, 'Gagal memuat kabupaten/kota');
                    regencySelect.disabled = true;
                    resetDistrict();
                    resetVillage();
                }
            };

            const loadDistricts = async (regencyCode) => {
                if (!regencyCode) {
                    resetDistrict();
                    resetVillage();
                    return;
                }

                try {
                    setLoading(districtSelect, 'Memuat kecamatan...');
                    districtSelect.disabled = true;
                    const items = await fetchWilayah('district', regencyCode);
                    setSelectOptions(districtSelect, items, 'Pilih kecamatan...');
                    districtSelect.disabled = false;

                    selectByName(districtSelect, state.districtName);
                    state.districtCode = findCodeByName(items, state.districtName);

                    if (state.districtCode) {
                        await loadVillages(state.districtCode);
                    } else {
                        resetVillage();
                    }
                } catch (error) {
                    setLoading(districtSelect, 'Gagal memuat kecamatan');
                    districtSelect.disabled = true;
                    resetVillage();
                }
            };

            const loadVillages = async (districtCode) => {
                if (!districtCode) {
                    resetVillage();
                    return;
                }

                try {
                    setLoading(villageSelect, 'Memuat desa/kelurahan...');
                    villageSelect.disabled = true;
                    const items = await fetchWilayah('village', districtCode);
                    setSelectOptions(villageSelect, items, 'Pilih desa/kelurahan...');
                    villageSelect.disabled = false;
                    selectByName(villageSelect, state.villageName);
                } catch (error) {
                    setLoading(villageSelect, 'Gagal memuat desa/kelurahan');
                    villageSelect.disabled = true;
                }
            };

            provinceSelect.addEventListener('change', async (event) => {
                const selected = event.target.selectedOptions[0];
                state.provinceName = selected ? selected.value : '';
                state.provinceCode = selected ? selected.dataset.code : '';
                state.regencyName = '';
                state.regencyCode = '';
                state.districtName = '';
                state.districtCode = '';
                state.villageName = '';
                await loadRegencies(state.provinceCode);
            });

            regencySelect.addEventListener('change', async (event) => {
                const selected = event.target.selectedOptions[0];
                state.regencyName = selected ? selected.value : '';
                state.regencyCode = selected ? selected.dataset.code : '';
                state.districtName = '';
                state.districtCode = '';
                state.villageName = '';
                await loadDistricts(state.regencyCode);
            });

            districtSelect.addEventListener('change', async (event) => {
                const selected = event.target.selectedOptions[0];
                state.districtName = selected ? selected.value : '';
                state.districtCode = selected ? selected.dataset.code : '';
                state.villageName = '';
                await loadVillages(state.districtCode);
            });

            villageSelect.addEventListener('change', (event) => {
                const selected = event.target.selectedOptions[0];
                state.villageName = selected ? selected.value : '';
            });

            loadProvinces();
        });
    </script>
@endsection
