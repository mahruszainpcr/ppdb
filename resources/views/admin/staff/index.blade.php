@extends('layouts.app')
@section('title', 'Manajemen Admin & Ustadz')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Manajemen Admin & Ustadz</h4>
            <div class="text-muted">Kelola akun admin dan ustadz (password awal = no HP).</div>
        </div>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                Tambah Akun
            </button>
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
            <form class="row g-2" id="staffFilterForm">
                <div class="col-md-4">
                    <input name="search" class="form-control" placeholder="Cari nama / no HP / email"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">Semua Role</option>
                        <option value="admin">Admin</option>
                        <option value="ustadz">Ustadz</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Cari</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-light w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card trezo-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="staffTable">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Role</th>
                            <th>No WhatsApp</th>
                            <th>Email</th>
                            <th>Dibuat</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL CREATE --}}
    <div class="modal fade" id="createUserModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content trezo-card">
                <form method="POST" action="{{ route('admin.staff.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Akun</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Nama</label>
                        <input name="name" class="form-control mb-2" required>

                        <label class="form-label">No WhatsApp</label>
                        <input name="phone" class="form-control mb-2" required>

                        <label class="form-label">Email (opsional)</label>
                        <input name="email" type="email" class="form-control mb-2">

                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="admin">Admin</option>
                            <option value="ustadz">Ustadz</option>
                        </select>

                        <div class="text-muted small mt-2">
                            Password awal akan diset sama dengan nomor HP.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-light" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary">Simpan</button>
                    </div>
                </form>
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
                        <h5 class="modal-title">Edit Akun</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Nama</label>
                        <input name="name" class="form-control mb-2" id="editUserName" required>

                        <label class="form-label">No WhatsApp</label>
                        <input name="phone" class="form-control mb-2" id="editUserPhone" required>

                        <label class="form-label">Email (opsional)</label>
                        <input name="email" type="email" class="form-control mb-2" id="editUserEmail">

                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" id="editUserRole" required>
                            <option value="admin">Admin</option>
                            <option value="ustadz">Ustadz</option>
                        </select>
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

    {{-- MODAL DELETE --}}
    <div class="modal fade" id="deleteUserModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content trezo-card">
                <form method="POST" id="deleteUserForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Hapus Akun</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div>Yakin hapus akun:</div>
                        <div class="fw-semibold" id="deleteUserName"></div>
                        <div class="alert alert-danger mt-2 mb-0">
                            Data akun akan dihapus permanen.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-light" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-danger">Hapus</button>
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
            const updateUrlTemplate = @json(route('admin.staff.update', ['user' => '__ID__']));
            const resetUrlTemplate = @json(route('admin.staff.resetPassword', ['user' => '__ID__']));
            const deleteUrlTemplate = @json(route('admin.staff.destroy', ['user' => '__ID__']));

            const table = $('#staffTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                lengthChange: true,
                pageLength: 15,
                ajax: {
                    url: @json(route('admin.staff.data')),
                    data: function (d) {
                        d.search = document.querySelector('#staffFilterForm input[name="search"]').value;
                        d.role = document.querySelector('#staffFilterForm select[name="role"]').value;
                    }
                },
                columns: [
                    { data: 'name' },
                    { data: 'role' },
                    { data: 'phone' },
                    { data: 'email' },
                    { data: 'created_at' },
                    { data: 'actions', orderable: false, searchable: false, className: 'text-end' }
                ]
            });

            document.getElementById('staffFilterForm').addEventListener('submit', function (e) {
                e.preventDefault();
                table.ajax.reload();
            });

            document.addEventListener('click', function (e) {
                const editBtn = e.target.closest('.btn-edit-user');
                if (editBtn) {
                    document.getElementById('editUserName').value = editBtn.dataset.name || '';
                    document.getElementById('editUserPhone').value = editBtn.dataset.phone || '';
                    document.getElementById('editUserEmail').value = editBtn.dataset.email || '';
                    document.getElementById('editUserRole').value = editBtn.dataset.role || 'ustadz';
                    document.getElementById('editUserForm').action = updateUrlTemplate.replace('__ID__', editBtn.dataset.id);
                }

                const resetBtn = e.target.closest('.btn-reset-user');
                if (resetBtn) {
                    document.getElementById('resetUserPhone').textContent = resetBtn.dataset.phone || '-';
                    document.getElementById('resetUserForm').action = resetUrlTemplate.replace('__ID__', resetBtn.dataset.id);
                }

                const deleteBtn = e.target.closest('.btn-delete-user');
                if (deleteBtn) {
                    document.getElementById('deleteUserName').textContent = deleteBtn.dataset.name || '-';
                    document.getElementById('deleteUserForm').action = deleteUrlTemplate.replace('__ID__', deleteBtn.dataset.id);
                }
            });
        });
    </script>
@endpush
