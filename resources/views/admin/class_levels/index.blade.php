@extends('layouts.app')
@section('title', 'Master Kelas/Tingkatan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Master Kelas/Tingkatan</h4>
            <div class="text-muted">Kelola daftar kelas atau tingkatan.</div>
        </div>
        <div>
            <a class="btn btn-primary btn-sm" href="{{ route('admin.class-levels.create') }}">Tambah Kelas</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card trezo-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Kode</th>
                            <th>Urutan</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($classLevels as $level)
                            <tr>
                                <td class="fw-semibold">{{ $level->name }}</td>
                                <td>{{ $level->code ?: '-' }}</td>
                                <td>{{ $level->sort_order }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge {{ $level->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $level->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                        <form method="POST" action="{{ route('admin.class-levels.toggle', $level) }}">
                                            @csrf
                                            @method('PATCH')
                                            <div class="form-check form-switch m-0">
                                                <input class="form-check-input js-toggle-active" type="checkbox"
                                                    role="switch" @checked($level->is_active)>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                                <td>{{ optional($level->created_at)->format('Y-m-d') }}</td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-light me-1"
                                        href="{{ route('admin.class-levels.edit', $level) }}">Edit</a>
                                    <form method="POST" action="{{ route('admin.class-levels.destroy', $level) }}"
                                        class="d-inline" onsubmit="return confirm('Hapus kelas/tingkatan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data kelas/tingkatan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.js-toggle-active').forEach((toggle) => {
                toggle.addEventListener('change', function () {
                    const form = this.closest('form');
                    if (form) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
