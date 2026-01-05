@extends('layouts.app')
@section('title', 'Manajemen Akun User')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Manajemen Akun Wali</h4>
            <div class="text-muted">Edit data & reset password (password = no HP).</div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('reset_password_success'))
        <div class="alert alert-success">
            Password berhasil di-reset.<br>
            <b>Password baru:</b>
            <span class="bg-dark text-white px-2 py-1 rounded">{{ session('reset_password_value') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card trezo-card mb-3">
        <div class="card-body">
            <form class="row g-2">
                <div class="col-md-4">
                    <input name="search" class="form-control" placeholder="Cari nama / no HP"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Cari</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-light w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card trezo-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>No WhatsApp</th>
                            <th>Dibuat</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                            <tr>
                                <td>{{ $u->name }}</td>
                                <td>{{ $u->phone }}</td>
                                <td class="text-muted">{{ $u->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#editUser{{ $u->id }}">
                                        Edit
                                    </button>

                                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                                        data-bs-target="#resetUser{{ $u->id }}">
                                        Reset Password
                                    </button>
                                </td>
                            </tr>

                            {{-- MODAL EDIT --}}
                            <div class="modal fade" id="editUser{{ $u->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content trezo-card">
                                        <form method="POST" action="{{ route('admin.users.update', $u) }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Akun Wali</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <label class="form-label">Nama</label>
                                                <input name="name" class="form-control mb-2" value="{{ $u->name }}"
                                                    required>

                                                <label class="form-label">No WhatsApp</label>
                                                <input name="phone" class="form-control" value="{{ $u->phone }}"
                                                    required>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-outline-light" data-bs-dismiss="modal">Batal</button>
                                                <button class="btn btn-primary">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- MODAL RESET --}}
                            <div class="modal fade" id="resetUser{{ $u->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content trezo-card">
                                        <form method="POST" action="{{ route('admin.users.resetPassword', $u) }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Reset Password</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Password akan di-set sama dengan nomor HP:</p>
                                                <div class="fw-semibold">{{ $u->phone }}</div>
                                                <div class="alert alert-warning mt-2 mb-0">
                                                    Password lama akan diganti.
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-outline-light" data-bs-dismiss="modal">Batal</button>
                                                <button class="btn btn-warning">Reset</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted p-4">
                                    Belum ada akun wali.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $users->links() }}
    </div>
@endsection
