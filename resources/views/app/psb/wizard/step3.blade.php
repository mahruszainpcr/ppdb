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
                        <select name="father_education" class="form-select" required>
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
                        <select name="father_job" class="form-select" required>
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
                        <select name="father_income" class="form-select" required>
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

                    <div class="col-md-4">
                        <label class="form-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                        <input name="father_city" class="form-control"
                            value="{{ old('father_city', $pp->father_city ?? '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                        <input name="father_district" class="form-control"
                            value="{{ old('father_district', $pp->father_district ?? '') }}" required>
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
                        <select name="mother_education" class="form-select" required>
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
                        <select name="mother_job" class="form-select" required>
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
                        <select name="mother_income" class="form-select" required>
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
                    @php $wts = old('willing_to_serve', 'yes'); @endphp
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

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="agree_morality" value="1"
                        id="agree_morality" {{ old('agree_morality') ? 'checked' : '' }} required>
                    <label class="form-check-label" for="agree_morality">
                        Saya tidak terlibat penyalahgunaan narkoba, merokok, LGBT, pergaulan bebas, dan pelanggaran moral
                        lain. Jika melanggar bersedia dikeluarkan dan infak tidak bisa diminta kembali.
                    </label>
                </div>

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="agree_rules" value="1" id="agree_rules"
                        {{ old('agree_rules') ? 'checked' : '' }} required>
                    <label class="form-check-label" for="agree_rules">
                        Saya telah memahami visi misi & tata tertib Mahad Darussalam Yayasan Al-Marwa dan bersedia mematuhi
                        ketentuan.
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox"  required>
                    <label class="form-check-label" for="agree_rules">
                        Bila diterima bersedia untuk menjaga nama baik Ma'had, menyelesaikan segala permasalahan Ma'had
                        dengan kekeluargaan dan tidak melakukan penuntutan dan atau pengaduan kefihak diluar mahad, serta
                        mendukung segala peraturan dan kegiatan mahad. Kritik dan saran disampaikan melalui saluran yang
                        disediakan mahad.
                    </label>
                </div>



                {{-- Accordion kesanggupan pembayaran --}}
                <div class="accordion my-3" id="paymentAccordion">
                    <div class="accordion-item bg-transparent border border-opacity-25">
                        <h2 class="accordion-header" id="headingPay">
                            <button class="accordion-button collapsed bg-transparent text-light" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapsePay">
                                Pernyataan Kesanggupan Pembayaran (baca dulu)
                            </button>
                        </h2>
                        <div id="collapsePay" class="accordion-collapse collapse" data-bs-parent="#paymentAccordion">
                            <div class="accordion-body text-muted small">
                                {{-- Tempel teks panjang kesanggupan pembayaran kamu di sini (ringkas / full) --}}
                                <div class="mb-2">Saya bersedia memenuhi kewajiban pembayaran biaya pendidikan sesuai
                                    waktu yang ditentukan, termasuk ketentuan tanda jadi, infak, dan aturan pengembalian
                                    dana.</div>
                                <div class="mb-2">Dengan mencentang, berarti saya telah membaca dan menyetujui seluruh
                                    ketentuan.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="agree_payment" value="1"
                        id="agree_payment" {{ old('agree_payment') ? 'checked' : '' }} required>
                    <label class="form-check-label" for="agree_payment">
                        Saya menyetujui Pernyataan Kesanggupan Pembayaran dan seluruh ketentuan tambahan.
                    </label>
                </div>

                <hr class="border-opacity-25">

                <div class="d-flex justify-content-between">
                    <a href="{{ route('psb.wizard', ['step' => 2]) }}" class="btn btn-outline-light">Kembali Step 2</a>
                    <button class="btn btn-success px-4">Submit Final</button>
                </div>
            </div>
        </div>

    </form>
@endsection
