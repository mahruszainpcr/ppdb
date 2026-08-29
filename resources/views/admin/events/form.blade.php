@extends('layouts.app')
@section('title', $event->exists ? 'Edit Event' : 'Tambah Event')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">{{ $event->exists ? 'Edit Event' : 'Tambah Event' }}</h4>
            <div class="text-muted">Siapkan event untuk absensi scan barcode peserta.</div>
        </div>
        <div>
            <a class="btn btn-outline-light btn-sm" href="{{ route('admin.events.index') }}">Kembali</a>
        </div>
    </div>

    <div class="card trezo-card">
        <div class="card-body">
            <form method="POST" action="{{ $event->exists ? route('admin.events.update', $event) : route('admin.events.store') }}">
                @csrf
                @if ($event->exists)
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Nama Event</label>
                        <input type="text" name="name" class="form-control" required
                            placeholder="Contoh: Seleksi PPDB Gelombang 1"
                            value="{{ old('name', $event->name) }}">
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Event</label>
                        <input type="date" name="event_date" class="form-control"
                            value="{{ old('event_date', optional($event->event_date)->format('Y-m-d')) }}">
                        @error('event_date')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-8">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="location" class="form-control"
                            placeholder="Contoh: Aula Ma'had Darussalam Palas"
                            value="{{ old('location', $event->location) }}">
                        @error('location')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label d-block">Status</label>
                        <div class="form-check mt-2">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive"
                                @checked(old('is_active', $event->exists ? $event->is_active : true))>
                            <label class="form-check-label" for="isActive">Event aktif dan siap dipakai scan</label>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Deskripsi / Catatan</label>
                    <textarea name="description" class="form-control" rows="5"
                        placeholder="Catatan teknis event, keterangan peserta, jam hadir, dan lain-lain.">{{ old('description', $event->description) }}</textarea>
                    @error('description')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-primary">{{ $event->exists ? 'Simpan Perubahan' : 'Simpan Event' }}</button>
                    <a class="btn btn-outline-light" href="{{ route('admin.events.index') }}">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
