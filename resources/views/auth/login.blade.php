@extends('layouts.auth')

@section('title', 'Login Orang Tua')

@section('content')
    <div class="card trezo-card">
        <div class="card-body">

            <h5 class="mb-3 text-center">Login Orang Tua / Wali</h5>

            @if ($errors->any())
                <div class="alert alert-danger border-0 rounded-3">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nomor WhatsApp</label>
                    <input type="text" name="username" class="form-control" placeholder="08xxxxxxxxxx" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button class="btn btn-primary w-100">
                    Login
                </button>
            </form>

            <div class="text-center mt-3 small">
                Belum punya akun?
                <a href="{{ route('register') }}">Daftar PSB</a>
            </div>

        </div>
    </div>
@endsection
