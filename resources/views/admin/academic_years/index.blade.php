@extends('layouts.app')
@section('title', 'Tahun Ajaran')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Tahun Ajaran</h4>
            <div class="text-muted">Kelola master tahun ajaran.</div>
        </div>
        <div>
            <a class="btn btn-primary btn-sm" href="{{ route('admin.academic-years.create') }}">Tambah Tahun Ajaran</a>
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
                            <th>Periode</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($academicYears as $year)
                            <tr>
                                <td class="fw-semibold">{{ $year->name }}</td>
                                <td>
                                    @php
                                        $start = optional($year->start_date)->format('d M Y');
                                        $end = optional($year->end_date)->format('d M Y');
                                    @endphp
                                    @if ($start || $end)
                                        {{ $start ?: '-' }} - {{ $end ?: '-' }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span
                                            class="badge {{ $year->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $year->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                        <form method="POST" action="{{ route('admin.academic-years.toggle', $year) }}">
                                            @csrf
                                            @method('PATCH')
                                            <div class="form-check form-switch m-0">
                                                <input class="form-check-input js-toggle-active" type="checkbox"
                                                    role="switch" @checked($year->is_active)>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                                <td>{{ optional($year->created_at)->format('Y-m-d') }}</td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-light me-1"
                                        href="{{ route('admin.academic-years.edit', $year) }}">Edit</a>
                                    <form method="POST" action="{{ route('admin.academic-years.destroy', $year) }}"
                                        class="d-inline" onsubmit="return confirm('Hapus tahun ajaran ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada data tahun ajaran.</td>
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
