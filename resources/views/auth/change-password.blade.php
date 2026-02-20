@extends('layouts.app')
@section('title', 'Ganti Password')

@section('content')
    <div class="card trezo-card">
        <div class="card-body">
            <h4 class="mb-1">Ganti Password</h4>
            <div class="text-muted mb-3">Gunakan password baru yang kuat dan mudah diingat.</div>

            @if (session('status') === 'password-updated')
                <div class="alert alert-success">Password berhasil diperbarui.</div>
            @endif

            @if ($errors->updatePassword->any())
                <div class="alert alert-danger">
                    {{ $errors->updatePassword->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update.self') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Password Saat Ini</label>
                    <input type="password" name="current_password" class="form-control" required
                        autocomplete="current-password">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-control" required autocomplete="new-password">
                </div>

                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-control" required
                        autocomplete="new-password">
                </div>

                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
