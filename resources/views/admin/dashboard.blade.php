@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Dashboard Admin</h4>
            <div class="text-muted">PPDB / PSB Mahad Darussalam</div>
        </div>

        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button class="btn btn-outline-light btn-sm">Logout</button>
        </form>
    </div>

    <div class="card trezo-card">
        <div class="card-body">
            <a href="{{ route('admin.registrations.index') }}" class="btn btn-primary">
                Lihat Data Pendaftar
            </a>
        </div>
    </div>
@endsection
