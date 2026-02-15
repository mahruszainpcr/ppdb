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
            <form class="row g-2" id="usersFilterForm">
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
                <table class="table table-hover align-middle mb-0" id="usersTable">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>No WhatsApp</th>
                            <th>Dibuat</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content trezo-card">
                <form method="POST" id="editUserForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Akun Wali</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Nama</label>
                        <input name="name" class="form-control mb-2" id="editUserName" required>

                        <label class="form-label">No WhatsApp</label>
                        <input name="phone" class="form-control" id="editUserPhone" required>
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
    <div class="modal fade" id="resetUserModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content trezo-card">
                <form method="POST" id="resetUserForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Reset Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Password akan di-set sama dengan nomor HP:</p>
                        <div class="fw-semibold" id="resetUserPhone"></div>
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
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const updateUrlTemplate = @json(route('admin.users.update', ['user' => '__ID__']));
            const resetUrlTemplate = @json(route('admin.users.resetPassword', ['user' => '__ID__']));

            const table = $('#usersTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                lengthChange: true,
                pageLength: 15,
                ajax: {
                    url: @json(route('admin.users.data')),
                    data: function (d) {
                        d.search = document.querySelector('#usersFilterForm input[name="search"]').value;
                    }
                },
                columns: [
                    { data: 'name' },
                    { data: 'phone' },
                    { data: 'created_at' },
                    { data: 'actions', orderable: false, searchable: false, className: 'text-end' }
                ]
            });

            document.getElementById('usersFilterForm').addEventListener('submit', function (e) {
                e.preventDefault();
                table.ajax.reload();
            });

            document.addEventListener('click', function (e) {
                const editBtn = e.target.closest('.btn-edit-user');
                if (editBtn) {
                    document.getElementById('editUserName').value = editBtn.dataset.name || '';
                    document.getElementById('editUserPhone').value = editBtn.dataset.phone || '';
                    document.getElementById('editUserForm').action = updateUrlTemplate.replace('__ID__', editBtn.dataset.id);
                }

                const resetBtn = e.target.closest('.btn-reset-user');
                if (resetBtn) {
                    document.getElementById('resetUserPhone').textContent = resetBtn.dataset.phone || '-';
                    document.getElementById('resetUserForm').action = resetUrlTemplate.replace('__ID__', resetBtn.dataset.id);
                }
            });
        });
    </script>
@endpush
