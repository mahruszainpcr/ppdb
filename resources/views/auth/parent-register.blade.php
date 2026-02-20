@extends('layouts.auth')
@section('title', 'Registrasi Orang Tua')

@section('content')
    <div class="card trezo-card">
        <div class="card-body p-4">
            <h5 class="mb-1">Registrasi Orang Tua / Wali</h5>
            <div class="text-muted small mb-3">
                Gunakan nomor WhatsApp aktif untuk login dan menerima informasi.
            </div>

            @if ($errors->any())
                <div class="alert alert-danger border-0 rounded-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('parent.register.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nama Orang Tua / Wali <span class="text-danger">*</span></label>
                    <input name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nomor WhatsApp <span class="text-danger">*</span></label>
                    <input name="phone" class="form-control" placeholder="contoh: 08xxxxxxxxxx"
                        value="{{ old('phone') }}" required>
                    <div class="form-text">Nomor akan disimpan dengan format 08xxxxxxxxxx.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" required>
                    <div class="form-text">Minimal 8 karakter.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ulangi Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <button class="btn btn-primary w-100">Daftar & Lanjut Isi Form</button>
            </form>

            <hr class="border-opacity-25 my-3">

            <div class="text-center">
                <span class="text-muted small">Sudah punya akun?</span>
                <a href="{{ route('login') }}" class="btn btn-link">Login</a>
            </div>
        </div>
    </div>
@endsection
