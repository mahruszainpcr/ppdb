@extends('layouts.auth')
@section('title', 'Login Orang Tua')

@section('content')
    <div class="card trezo-card">
        <div class="card-body p-4">
            <h5 class="mb-1">Login Orang Tua / Wali</h5>
            <div class="text-muted small mb-3">
                Masuk untuk melanjutkan pengisian dan melihat status/kelulusan.
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

            <form method="POST" action="{{ route('parent.login.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nomor WhatsApp <span class="text-danger">*</span></label>
                    <input name="phone" class="form-control" placeholder="contoh: 08xxxxxxxxxx"
                        value="{{ old('phone') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button class="btn btn-primary w-100">Login</button>
            </form>

            <hr class="border-opacity-25 my-3">

            <div class="text-center">
                <span class="text-muted small">Belum punya akun?</span>
                <a href="{{ route('parent.register') }}" class="btn btn-link">Registrasi</a>
            </div>
        </div>
    </div>
@endsection
