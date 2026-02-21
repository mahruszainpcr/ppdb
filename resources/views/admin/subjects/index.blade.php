@extends('layouts.app')
@section('title', 'Master Mapel/Kitab')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Master Mapel/Kitab</h4>
            <div class="text-muted">Kelola daftar mapel dan kitab.</div>
        </div>
        <div>
            <a class="btn btn-primary btn-sm" href="{{ route('admin.subjects.create') }}">Tambah Mapel/Kitab</a>
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
                            <th>Jenis</th>
                            <th>Kode</th>
                            <th>Urutan</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subjects as $subject)
                            <tr>
                                <td class="fw-semibold">{{ $subject->name }}</td>
                                <td>{{ $subject->type === 'kitab' ? 'Kitab' : 'Mapel' }}</td>
                                <td>{{ $subject->code ?: '-' }}</td>
                                <td>{{ $subject->sort_order }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge {{ $subject->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $subject->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                        <form method="POST" action="{{ route('admin.subjects.toggle', $subject) }}">
                                            @csrf
                                            @method('PATCH')
                                            <div class="form-check form-switch m-0">
                                                <input class="form-check-input js-toggle-active" type="checkbox"
                                                    role="switch" @checked($subject->is_active)>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                                <td>{{ optional($subject->created_at)->format('Y-m-d') }}</td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-light me-1"
                                        href="{{ route('admin.subjects.edit', $subject) }}">Edit</a>
                                    <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}"
                                        class="d-inline" onsubmit="return confirm('Hapus mapel/kitab ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Belum ada data mapel/kitab.</td>
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
