@extends('layouts.app')
@section('title', 'Pengaturan Dinamis PPDB')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Pengaturan Dinamis PPDB</h4>
            <div class="text-muted">Atur jadwal, kontak, grup WA, dan informasi yang tampil ke wali.</div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card trezo-card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.periods.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label">Pilih Periode</label>
                    <select name="period_id" class="form-control form-select">
                        <option value="">Periode aktif / terbaru</option>
                        @foreach ($periods as $p)
                            <option value="{{ $p->id }}" @selected(($period?->id ?? null) === $p->id)>
                                {{ $p->name }} (Gel. {{ $p->wave }}){{ $p->is_active ? ' - Aktif' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-outline-light">Pilih</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card trezo-card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.periods.save') }}" class="row g-3">
                @csrf
                <input type="hidden" name="period_id" value="{{ $period?->id }}">

                <div class="col-md-8">
                    <label class="form-label">Nama Periode</label>
                    <input name="name" class="form-control" required
                        value="{{ old('name', $period?->name ?? '') }}"
                        placeholder="Contoh: PPDB Mahad Darussalam 2026 Gelombang 1">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Gelombang</label>
                    <input type="number" min="1" name="wave" class="form-control" required
                        value="{{ old('wave', $period?->wave ?? 1) }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                            @checked(old('is_active', $period?->is_active ?? true))>
                        <label class="form-check-label" for="is_active">Set Aktif</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tanggal Buka Pendaftaran</label>
                    <input type="date" name="registration_open_date" class="form-control"
                        value="{{ old('registration_open_date', optional($period?->registration_open_date)->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Tutup Pendaftaran</label>
                    <input type="date" name="registration_close_date" class="form-control"
                        value="{{ old('registration_close_date', optional($period?->registration_close_date)->format('Y-m-d')) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tanggal Ujian</label>
                    <input type="date" name="exam_date" class="form-control"
                        value="{{ old('exam_date', optional($period?->exam_date)->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Pengumuman</label>
                    <input type="date" name="announce_date" class="form-control"
                        value="{{ old('announce_date', optional($period?->announce_date)->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Batas Tanda Jadi</label>
                    <input type="date" name="down_payment_deadline" class="form-control"
                        value="{{ old('down_payment_deadline', optional($period?->down_payment_deadline)->format('Y-m-d')) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Link Grup WA Ikhwan</label>
                    <input name="wa_group_ikhwan" class="form-control" type="url"
                        value="{{ old('wa_group_ikhwan', $period?->wa_group_ikhwan ?? '') }}"
                        placeholder="https://chat.whatsapp.com/...">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Link Grup WA Akhwat</label>
                    <input name="wa_group_akhwat" class="form-control" type="url"
                        value="{{ old('wa_group_akhwat', $period?->wa_group_akhwat ?? '') }}"
                        placeholder="https://chat.whatsapp.com/...">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Kontak Admin 1</label>
                    <input name="admin_contact_1" class="form-control"
                        value="{{ old('admin_contact_1', $period?->admin_contact_1 ?? '') }}"
                        placeholder="Contoh: Abu Ja'far: 0821-7267-6721">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kontak Admin 2</label>
                    <input name="admin_contact_2" class="form-control"
                        value="{{ old('admin_contact_2', $period?->admin_contact_2 ?? '') }}"
                        placeholder="Contoh: Admin: 0821-1792-7452">
                </div>

                <div class="col-12">
                    <label class="form-label">Informasi Tambahan (Dinamis)</label>
                    <textarea name="information_note" class="form-control" rows="4"
                        placeholder="Teks ini bisa dipakai untuk info dinamis yang ingin ditampilkan ke wali/orang tua.">{{ old('information_note', $period?->information_note ?? '') }}</textarea>
                </div>

                <div class="col-12">
                    <button class="btn btn-primary">Simpan Pengaturan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

