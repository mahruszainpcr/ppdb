@extends('layouts.app')
@section('title', 'Event & Absensi')

@section('content')
    @if (session('success'))
        <div class="alert alert-success border-0 rounded-3">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 rounded-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Event & Absensi</h4>
            <div class="text-muted">Kelola event seperti seleksi PPDB dan pantau histori scan kehadiran peserta.</div>
        </div>
        <div>
            <a class="btn btn-primary btn-sm" href="{{ route('admin.events.create') }}">Tambah Event</a>
        </div>
    </div>

    <div class="row g-3">
        @forelse ($events as $event)
            <div class="col-xl-4 col-md-6">
                <div class="card trezo-card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <div class="fw-semibold fs-5">{{ $event->name }}</div>
                                <div class="text-muted small">
                                    {{ $event->event_date?->format('d M Y') ?? 'Tanggal belum diatur' }}
                                </div>
                            </div>
                            <span class="badge {{ $event->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $event->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>

                        <div class="small text-muted mb-2">
                            {{ $event->location ?: 'Lokasi belum diatur' }}
                        </div>

                        <div class="small text-muted mb-3" style="min-height:42px;">
                            {{ \Illuminate\Support\Str::limit($event->description ?: 'Belum ada deskripsi event.', 110) }}
                        </div>

                        <div class="border rounded p-3 bg-light mb-3">
                            <div class="text-muted small">Total Sudah Absen</div>
                            <div class="fs-4 fw-bold">{{ $event->attendances_count }}</div>
                        </div>

                        <div class="mt-auto d-flex flex-wrap gap-2">
                            <a class="btn btn-outline-light btn-sm" href="{{ route('admin.events.show', $event) }}">Buka</a>
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.events.edit', $event) }}">Edit</a>
                            @if (auth()->user()?->role === 'admin')
                                <form method="POST" action="{{ route('admin.events.destroy', $event) }}"
                                    onsubmit="return confirm('Hapus event ini? histori absensi juga akan terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm">Hapus</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card trezo-card">
                    <div class="card-body text-center py-5 text-muted">
                        Belum ada event. Tambahkan event pertama seperti <strong>Seleksi PPDB</strong> untuk mulai scan
                        absensi peserta.
                    </div>
                </div>
            </div>
        @endforelse
    </div>
@endsection
