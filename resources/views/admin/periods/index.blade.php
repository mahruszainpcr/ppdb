@extends('layouts.app')
@section('title', 'Manajemen Periode PPDB')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Manajemen Periode PPDB</h4>
            <div class="text-muted">Tambah periode baru, aktifkan periode, dan atur informasi dinamis yang tampil di form pendaftaran.</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.periods.index', ['create' => 1]) }}" class="btn btn-primary btn-sm">Tambah Periode Baru</a>
            <a href="{{ route('admin.periods.index') }}" class="btn btn-outline-light btn-sm">Reset</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="row g-3">
        <div class="col-xl-4">
            <div class="card trezo-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Daftar Periode</h6>
                        <span class="badge bg-light text-dark">{{ $periods->count() }} periode</span>
                    </div>

                    <div class="d-grid gap-2">
                        @forelse ($periods as $item)
                            <div class="border rounded-3 p-3 {{ !$createMode && $period?->id === $item->id ? 'border-primary bg-light' : '' }}">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="fw-semibold">{{ $item->name }}</div>
                                        <div class="small text-muted">Gelombang {{ $item->wave }}</div>
                                        <div class="small text-muted mt-1">
                                            {{ optional($item->registration_open_date)->format('d M Y') ?? '-' }}
                                            s/d
                                            {{ optional($item->registration_close_date)->format('d M Y') ?? '-' }}
                                        </div>
                                    </div>
                                    <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>

                                <div class="d-flex flex-wrap gap-2 mt-3">
                                    <a href="{{ route('admin.periods.index', ['period_id' => $item->id]) }}" class="btn btn-outline-light btn-sm">Edit</a>
                                    @if (!$item->is_active)
                                        <form method="POST" action="{{ route('admin.periods.activate', $item) }}">
                                            @csrf
                                            <button class="btn btn-outline-success btn-sm">Aktifkan</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.periods.destroy', $item) }}"
                                        onsubmit="return confirm('Hapus periode ini? data pendaftaran yang sudah ada akan kehilangan tautan periode.')">
                                        @csrf
                                        <button class="btn btn-outline-danger btn-sm">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted">Belum ada periode. Tambahkan periode pertama.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card trezo-card">
                <div class="card-body">
                    <h6 class="mb-3">{{ $createMode || !$period?->exists ? 'Tambah Periode Baru' : 'Edit Periode' }}</h6>

                    <form method="POST" action="{{ route('admin.periods.save') }}" class="row g-3">
                        @csrf
                        <input type="hidden" name="period_id" value="{{ $createMode ? '' : ($period?->id ?? '') }}">

                        <div class="col-md-8">
                            <label class="form-label">Nama Periode</label>
                            <input name="name" class="form-control" required
                                value="{{ old('name', $period?->name ?? '') }}"
                                placeholder="Contoh: PPDB Mahad Darussalam 2027/2028">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Gelombang</label>
                            <input type="number" min="1" name="wave" class="form-control" required
                                value="{{ old('wave', $period?->wave ?? 1) }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                                    @checked(old('is_active', $period?->is_active ?? $createMode))>
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
                            <label class="form-label">Judul Bukti Pembayaran di Step Dokumen</label>
                            <input name="payment_proof_label" class="form-control"
                                value="{{ old('payment_proof_label', $period?->payment_proof_label ?? 'Bukti Pembayaran Uang Pendaftaran (Rp. 150.000)') }}"
                                placeholder="Contoh: Bukti Pembayaran Uang Pendaftaran (Rp. 150.000)">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Informasi Pembayaran di Step Dokumen</label>
                            <textarea name="payment_proof_note" class="form-control" rows="3"
                                placeholder="Contoh: No Rek. (BSI 7145-1777-28) Kode Bank 451 An. Al Marwa SPP">{{ old('payment_proof_note', $period?->payment_proof_note ?? 'No Rek. (BSI 7145-1777-28) Kode Bank 451 An. Al Marwa SPP') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Persetujuan Pembayaran di Step 2</label>
                            <textarea name="payment_agreement_note" class="form-control" rows="5"
                                placeholder="Teks ini akan tampil dinamis pada form persetujuan pembayaran.">{{ old('payment_agreement_note', $period?->payment_agreement_note ?? "Saya bersedia memenuhi kewajiban pembayaran biaya pendidikan sesuai waktu yang ditentukan, termasuk ketentuan tanda jadi, infak, dan aturan pengembalian dana.\nDengan mencentang, berarti saya telah membaca dan menyetujui seluruh ketentuan pembayaran lainnya yang ditetapkan ma'had.") }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Informasi Tambahan Umum</label>
                            <textarea name="information_note" class="form-control" rows="4"
                                placeholder="Info dinamis tambahan untuk wali/orang tua.">{{ old('information_note', $period?->information_note ?? '') }}</textarea>
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <button class="btn btn-primary">Simpan Periode</button>
                            <a href="{{ route('admin.periods.index') }}" class="btn btn-outline-light">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
