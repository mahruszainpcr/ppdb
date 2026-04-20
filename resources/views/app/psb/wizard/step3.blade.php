@extends('layouts.app')
@section('title', 'Wizard PSB - Step 3')

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

    @php
        $pp = $registration->parentProfile;
        $st = $registration->statement;
    @endphp

    <div class="card trezo-card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-1">Wizard PSB</h5>
                    <div class="text-muted small">Nomor Pendaftaran: <span
                            class="fw-semibold">{{ $registration->registration_no }}</span></div>
                </div>
                <span class="badge rounded-pill text-bg-secondary">Step 3</span>
            </div>
            <hr class="border-opacity-25">
            <div class="d-flex align-items-center gap-2 small text-muted">
                <span class="badge text-bg-success rounded-pill">1</span> Program & Dokumen
                <span class="mx-1">›</span>
                <span class="badge text-bg-success rounded-pill">2</span> Data Santri
                <span class="mx-1">›</span>
                <span class="badge text-bg-primary rounded-pill">3</span> Orang Tua & Pernyataan
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('psb.step3.submit') }}">
        @csrf

        <div class="card trezo-card mb-3">
            <div class="card-body">
                <h6 class="mb-3">A. Identitas Orang Tua / Wali</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nomor Kartu Keluarga <span class="text-danger">*</span></label>
                        <input name="kk_number" class="form-control" value="{{ old('kk_number', $pp->kk_number ?? '') }}"
                            required>
                    </div>
                </div>

                <hr class="border-opacity-25 my-4">

                <h6 class="mb-3">B. Data Ayah / Wali</h6>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Nama Ayah/Wali (sesuai KK) <span class="text-danger">*</span></label>
                        <input name="father_name" class="form-control"
                            value="{{ old('father_name', $pp->father_name ?? '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">NIK Ayah <span class="text-danger">*</span></label>
                        <input name="father_nik" class="form-control" value="{{ old('father_nik', $pp->father_nik ?? '') }}"
                            required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                        <input name="father_birth_place" class="form-control"
                            value="{{ old('father_birth_place', $pp->father_birth_place ?? '') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" name="father_birth_date" class="form-control"
                            value="{{ old('father_birth_date', optional($pp?->father_birth_date)->format('Y-m-d')) }}"
                            required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Agama <span class="text-danger">*</span></label>
                        <input name="father_religion" class="form-control"
                            value="{{ old('father_religion', $pp->father_religion ?? 'ISLAM') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Pendidikan Terakhir <span class="text-danger">*</span></label>
                        <select name="father_education" class="form-control form-select" required>
                            @php $fe = old('father_education', $pp->father_education ?? 'SMA Sederajat'); @endphp
                            @foreach (['SMP Sederajat', 'SMA Sederajat', 'S1', 'S2', 'S3', 'Other'] as $v)
                                <option value="{{ $v }}" {{ $fe === $v ? 'selected' : '' }}>
                                    {{ $v }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Pekerjaan <span class="text-danger">*</span></label>
                        <select name="father_job" class="form-control form-select" required>
                            @php $fj = old('father_job', $pp->father_job ?? 'Karyawan Swasta'); @endphp
                            @foreach (['Pegawai Negeri Sipil', 'Perusahaan Nasional (BUMN, Perusahaan Besar)', 'Karyawan Swasta', 'WIRAUSAHA', 'Other'] as $v)
                                <option value="{{ $v }}" {{ $fj === $v ? 'selected' : '' }}>
                                    {{ $v }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Penghasilan/Bulan <span class="text-danger">*</span></label>
                        <select name="father_income" class="form-control form-select" required>
                            @php $fi = old('father_income', $pp->father_income ?? '2 -5 Juta'); @endphp
                            @foreach (['Dibawah 2 Juta', '2 -5 Juta', 'Diatas 5 Juta', 'Other'] as $v)
                                <option value="{{ $v }}" {{ $fi === $v ? 'selected' : '' }}>
                                    {{ $v }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Alamat Ayah/Wali <span class="text-danger">*</span></label>
                        <textarea name="father_address" class="form-control" rows="2" required>{{ old('father_address', $pp->father_address ?? '') }}</textarea>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                        <select name="father_province" id="fatherProvinceSelect" class="form-control form-select" required>
                            <option value="" disabled selected>Pilih provinsi...</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                        <select name="father_city" id="fatherRegencySelect" class="form-control form-select" required disabled>
                            <option value="" disabled selected>Pilih provinsi dulu</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                        <select name="father_district" id="fatherDistrictSelect" class="form-control form-select" required disabled>
                            <option value="" disabled selected>Pilih kota/kabupaten dulu</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Desa/Kelurahan <span class="text-danger">*</span></label>
                        <select name="father_village" id="fatherVillageSelect" class="form-control form-select" required disabled>
                            <option value="" disabled selected>Pilih kecamatan dulu</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kode Pos</label>
                        <input name="father_postal_code" class="form-control"
                            value="{{ old('father_postal_code', $pp->father_postal_code ?? '') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">No. Telepon Ayah/Wali (WhatsApp) <span
                                class="text-danger">*</span></label>
                        <input name="father_phone" class="form-control"
                            value="{{ old('father_phone', $pp->father_phone ?? '') }}" required>
                    </div>
                </div>

                <hr class="border-opacity-25 my-4">

                <h6 class="mb-3">C. Data Ibu</h6>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Nama Ibu (sesuai KK) <span class="text-danger">*</span></label>
                        <input name="mother_name" class="form-control"
                            value="{{ old('mother_name', $pp->mother_name ?? '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">NIK Ibu <span class="text-danger">*</span></label>
                        <input name="mother_nik" class="form-control"
                            value="{{ old('mother_nik', $pp->mother_nik ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                        <input name="mother_birth_place" class="form-control"
                            value="{{ old('mother_birth_place', $pp->mother_birth_place ?? '') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" name="mother_birth_date" class="form-control"
                            value="{{ old('mother_birth_date', optional($pp?->mother_birth_date)->format('Y-m-d')) }}"
                            required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Agama <span class="text-danger">*</span></label>
                        <input name="mother_religion" class="form-control"
                            value="{{ old('mother_religion', $pp->mother_religion ?? 'ISLAM') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Pendidikan Terakhir <span class="text-danger">*</span></label>
                        <select name="mother_education" class="form-control form-select" required>
                            @php $me = old('mother_education', $pp->mother_education ?? 'SMA Sederajat'); @endphp
                            @foreach (['SMP Sederajat', 'SMA Sederajat', 'S1', 'S2', 'S3', 'Other'] as $v)
                                <option value="{{ $v }}" {{ $me === $v ? 'selected' : '' }}>
                                    {{ $v }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Pekerjaan <span class="text-danger">*</span></label>
                        <select name="mother_job" class="form-control form-select" required>
                            @php $mj = old('mother_job', $pp->mother_job ?? 'Ibu Rumah Tangga'); @endphp
                            @foreach (['Pegawai Negeri Sipil', 'Perusahaan Nasional (BUMN, Perusahaan Besar)', 'Karyawan Swasta', 'WIRASWASTA', 'Ibu Rumah Tangga', 'Other'] as $v)
                                <option value="{{ $v }}" {{ $mj === $v ? 'selected' : '' }}>
                                    {{ $v }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Penghasilan/Bulan <span class="text-danger">*</span></label>
                        <select name="mother_income" class="form-control form-select" required>
                            @php $mi = old('mother_income', $pp->mother_income ?? 'Tidak Bepenghasilan'); @endphp
                            @foreach (['Dibawah 2 Juta', '2 -5 Juta', 'Diatas 5 Juta', 'Tidak Bepenghasilan', 'Other'] as $v)
                                <option value="{{ $v }}" {{ $mi === $v ? 'selected' : '' }}>
                                    {{ $v }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">No. Telepon Ibu (WhatsApp) <span class="text-danger">*</span></label>
                        <input name="mother_phone" class="form-control"
                            value="{{ old('mother_phone', $pp->mother_phone ?? '') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Ustadz Favorit <span class="text-danger">*</span></label>
                        <input name="favorite_ustadz" class="form-control"
                            value="{{ old('favorite_ustadz', $pp->favorite_ustadz ?? '') }}" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="card trezo-card mb-3">
            <div class="card-body">
                <h6 class="mb-3">D. Form Pernyataan (Wajib)</h6>

                <div class="mb-3">
                    <label class="form-label d-block">
                        Apakah calon santri/santriwati bila lulus bersedia mengabdi?
                        <span class="text-danger">*</span>
                    </label>
                    @php $wts = old('willing_to_serve', isset($st) ? ($st->willing_to_serve ? 'yes' : 'no') : 'yes'); @endphp
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="willing_to_serve" value="yes"
                            id="wts_yes" {{ $wts === 'yes' ? 'checked' : '' }}>
                        <label class="form-check-label" for="wts_yes">Iya</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="willing_to_serve" value="no"
                            id="wts_no" {{ $wts === 'no' ? 'checked' : '' }}>
                        <label class="form-check-label" for="wts_no">Tidak - pendaftaran tidak akan diproses</label>
                    </div>
                    @error('willing_to_serve')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label d-block">
                        Saya tidak terlibat penyalahgunaan narkoba, merokok, LGBT, pergaulan bebas, dan pelanggaran moral
                        lain. Jika melanggar bersedia dikeluarkan dan infak tidak bisa diminta kembali.
                        <span class="text-danger">*</span>
                    </label>
                    @php $am = old('agree_morality', isset($st) ? ($st->agree_morality ? 'yes' : 'no') : 'yes'); @endphp
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="agree_morality" value="yes"
                            id="agree_morality_yes" {{ $am === 'yes' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="agree_morality_yes">Iya</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="agree_morality" value="no"
                            id="agree_morality_no" {{ $am === 'no' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="agree_morality_no">Tidak</label>
                    </div>
                    @error('agree_morality')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label d-block">
                        Saya telah memahami visi misi & tata tertib Mahad Darussalam Yayasan Al-Marwa dan bersedia memenuhi
                        peraturan yang ada di mahad.
                        <span class="text-danger">*</span>
                    </label>
                    @php $ar = old('agree_rules', isset($st) ? ($st->agree_rules ? 'yes' : 'no') : 'yes'); @endphp
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="agree_rules" value="yes"
                            id="agree_rules_yes" {{ $ar === 'yes' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="agree_rules_yes">Iya</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="agree_rules" value="no"
                            id="agree_rules_no" {{ $ar === 'no' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="agree_rules_no">Tidak</label>
                    </div>
                    @error('agree_rules')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label d-block">
                        Bila diterima bersedia untuk menjaga nama baik Ma'had, menyelesaikan segala permasalahan Ma'had
                        dengan kekeluargaan dan tidak melakukan penuntutan dan atau pengaduan kefihak diluar mahad, serta
                        mendukung segala peraturan dan kegiatan mahad. Kritik dan saran disampaikan melalui saluran yang
                        disediakan mahad.
                        <span class="text-danger">*</span>
                    </label>
                    @php $ai = old('agree_integrity', isset($st) && isset($st->agree_integrity) ? ($st->agree_integrity ? 'yes' : 'no') : 'yes'); @endphp
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="agree_integrity" value="yes"
                            id="agree_integrity_yes" {{ $ai === 'yes' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="agree_integrity_yes">Iya</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="agree_integrity" value="no"
                            id="agree_integrity_no" {{ $ai === 'no' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="agree_integrity_no">Tidak</label>
                    </div>
                    @error('agree_integrity')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>



                <div class="mb-3">
                    <label class="form-label d-block">
                        Saya bersedia memenuhi kewajiban pembayaran biaya pendidikan sesuai waktu yang ditentukan, termasuk ketentuan tanda jadi, infak, dan aturan pengembalian dana.
Dengan mencentang, berarti saya telah membaca dan menyetujui seluruh ketentuan. <br>
Saya menyetujui Pernyataan Kesanggupan Pembayaran dengan ketentuan SPP di bayarkan sebelum tanggal 10 setiap bulannya, uang masuk dibayarkan 50% setelah dinyatakan diterima dan dilunasi paling lambat bulan April 2027 serta buku dan seragam dilunasi bulan januari 2027 dan juga bersedia memenuhi seluruh ketentuan tambahan yang belum tercantum dalam form ini.
                        <span class="text-danger">*</span>
                    </label>
                    @php $ap = old('agree_payment', isset($st) ? ($st->agree_payment ? 'yes' : 'no') : 'yes'); @endphp
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="agree_payment" value="yes"
                            id="agree_payment_yes" {{ $ap === 'yes' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="agree_payment_yes">Iya</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="agree_payment" value="no"
                            id="agree_payment_no" {{ $ap === 'no' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="agree_payment_no">Tidak</label>
                    </div>
                    @error('agree_payment')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="border-opacity-25">

                <div class="d-flex justify-content-between">
                    <a href="{{ route('psb.wizard', ['step' => 2]) }}" class="btn btn-outline-light">Kembali Step 2</a>
                    <button class="btn btn-success px-4">Submit Final</button>
                </div>
            </div>
        </div>

    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const provinceSelect = document.getElementById('fatherProvinceSelect');
            const regencySelect = document.getElementById('fatherRegencySelect');
            const districtSelect = document.getElementById('fatherDistrictSelect');
            const villageSelect = document.getElementById('fatherVillageSelect');
            const optionsUrl = @json(route('app.wilayah.options'));

            const state = {
                provinceName: @json(old('father_province', $pp->father_province ?? '')),
                regencyName: @json(old('father_city', $pp->father_city ?? '')),
                districtName: @json(old('father_district', $pp->father_district ?? '')),
                villageName: @json(old('father_village', $pp->father_village ?? '')),
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
                if (!name) return '';
                const found = items.find((item) => item.name === name);
                return found ? found.code : '';
            };

            const selectByName = (selectEl, name) => {
                if (!name) return;
                const opt = Array.from(selectEl.options).find((o) => o.value === name);
                if (opt) {
                    opt.selected = true;
                }
            };

            const resetRegency = () => {
                setSelectOptions(regencySelect, [], 'Pilih provinsi dulu');
                regencySelect.disabled = true;
            };

            const resetDistrict = () => {
                setSelectOptions(districtSelect, [], 'Pilih kota/kabupaten dulu');
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
                } catch (err) {
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
                    setLoading(regencySelect, 'Memuat kota/kabupaten...');
                    regencySelect.disabled = true;
                    const items = await fetchWilayah('regency', provinceCode);
                    setSelectOptions(regencySelect, items, 'Pilih kota/kabupaten...');
                    regencySelect.disabled = false;
                    selectByName(regencySelect, state.regencyName);
                    state.regencyCode = findCodeByName(items, state.regencyName);
                    if (state.regencyCode) {
                        await loadDistricts(state.regencyCode);
                    } else {
                        resetDistrict();
                        resetVillage();
                    }
                } catch (err) {
                    setLoading(regencySelect, 'Gagal memuat kota/kabupaten');
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
                } catch (err) {
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
                } catch (err) {
                    setLoading(villageSelect, 'Gagal memuat desa/kelurahan');
                    villageSelect.disabled = true;
                }
            };

            provinceSelect.addEventListener('change', async (e) => {
                const selected = e.target.selectedOptions[0];
                state.provinceName = selected ? selected.value : '';
                state.provinceCode = selected ? selected.dataset.code : '';
                state.regencyName = '';
                state.regencyCode = '';
                state.districtName = '';
                state.districtCode = '';
                state.villageName = '';
                await loadRegencies(state.provinceCode);
            });

            regencySelect.addEventListener('change', async (e) => {
                const selected = e.target.selectedOptions[0];
                state.regencyName = selected ? selected.value : '';
                state.regencyCode = selected ? selected.dataset.code : '';
                state.districtName = '';
                state.districtCode = '';
                state.villageName = '';
                await loadDistricts(state.regencyCode);
            });

            districtSelect.addEventListener('change', async (e) => {
                const selected = e.target.selectedOptions[0];
                state.districtName = selected ? selected.value : '';
                state.districtCode = selected ? selected.dataset.code : '';
                state.villageName = '';
                await loadVillages(state.districtCode);
            });

            villageSelect.addEventListener('change', (e) => {
                const selected = e.target.selectedOptions[0];
                state.villageName = selected ? selected.value : '';
            });

            loadProvinces();
        });
    </script>
@endsection
