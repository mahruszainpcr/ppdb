@extends('layouts.auth')

@section('content')
    <div class="card trezo-card">
        <div class="card-body p-4">
            <h5 class="mb-1">Admin Login</h5>
            <p class="text-muted mb-4">Masuk untuk mengelola data pendaftar.</p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Username (No WA / Email)</label>
                    <input name="username" class="form-control" value="{{ old('username') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
@endsection
