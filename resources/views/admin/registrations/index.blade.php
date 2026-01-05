@extends('layouts.app')
@section('title', 'Data Pendaftar')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Data Pendaftar</h4>
            <div class="text-muted">Cari, filter, dan buka detail pendaftar.</div>
        </div>
    </div>

    <div class="card trezo-card mb-3">
        <div class="card-body">
            <form class="row g-2">
                <div class="col-md-4">
                    <input name="search" class="form-control" placeholder="Cari: nama/no pendaftaran/no WA"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Status (Semua)</option>
                        @foreach (['draft', 'submitted', 'verified', 'revision_requested'] as $st)
                            <option value="{{ $st }}" @selected(request('status') === $st)>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="graduation_status" class="form-select">
                        <option value="">Kelulusan (Semua)</option>
                        @foreach (['pending', 'lulus', 'tidak_lulus', 'cadangan'] as $gs)
                            <option value="{{ $gs }}" @selected(request('graduation_status') === $gs)>{{ $gs }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Terapkan</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.registrations.index') }}" class="btn btn-outline-light w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card trezo-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>No Daftar</th>
                            <th>Nama Santri</th>
                            <th>No WA Wali</th>
                            <th>Jenjang</th>
                            <th>Status</th>
                            <th>Kelulusan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registrations as $r)
                            <tr>
                                <td class="fw-semibold">{{ $r->registration_no }}</td>
                                <td>{{ optional($r->studentProfile)->full_name ?? '-' }}</td>
                                <td>{{ $r->user->phone ?? '-' }}</td>
                                <td>{{ $r->education_level }}</td>
                                <td><span class="badge bg-secondary">{{ $r->status }}</span></td>
                                <td><span class="badge bg-info">{{ $r->graduation_status }}</span></td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-light"
                                        href="{{ route('admin.registrations.show', $r) }}">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted p-4">
                                    Belum ada data pendaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $registrations->links() }}
    </div>
@endsection
