@extends('welcome')
@section('title', 'Login Orang Tua')

@push('styles')
    <style>
        .parent-auth-page {
            background: linear-gradient(180deg, #f8fafc 0%, #eef6f2 100%);
            min-height: calc(100vh - 60px);
            padding: 48px 20px 72px;
        }

        .parent-auth-page__wrap {
            max-width: 520px;
            margin: 0 auto;
        }

        .parent-auth-page__card {
            background: #fff;
            border: 1px solid rgba(15, 23, 42, .08);
            border-radius: 18px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, .10);
            padding: 24px;
        }

        .parent-auth-page__title {
            margin: 0 0 4px;
            font-size: 20px;
            color: #0f172a;
        }

        .parent-auth-page__subtitle {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .parent-auth-page__alert {
            margin-bottom: 16px;
            border-radius: 12px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px 14px;
            font-size: 13px;
        }

        .parent-auth-page__alert ul {
            margin: 0;
            padding-left: 18px;
        }

        .parent-auth-page__group {
            margin-bottom: 14px;
        }

        .parent-auth-page__label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .parent-auth-page__req {
            color: #dc2626;
        }

        .parent-auth-page__input {
            width: 100%;
            border: 1px solid #dbe2ea;
            border-radius: 12px;
            padding: 11px 12px;
            font: inherit;
            color: #0f172a;
            background: #fff;
            outline: none;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .parent-auth-page__input:focus {
            border-color: #1E7F5C;
            box-shadow: 0 0 0 3px rgba(30, 127, 92, .12);
        }

        .parent-auth-page__submit {
            width: 100%;
            margin-top: 4px;
        }

        .parent-auth-page__divider {
            border: 0;
            border-top: 1px solid rgba(15, 23, 42, .08);
            margin: 16px 0;
        }

        .parent-auth-page__foot {
            text-align: center;
            font-size: 13px;
            color: #64748b;
        }

        .parent-auth-page__foot a {
            color: #1E7F5C;
            font-weight: 600;
            margin-left: 6px;
        }

        @media (max-width: 640px) {
            .parent-auth-page {
                padding: 32px 14px 56px;
            }

            .parent-auth-page__card {
                padding: 18px;
                border-radius: 16px;
            }
        }
    </style>
@endpush

@section('content')
    <x-landing-header />

    <section class="parent-auth-page">
        <div class="parent-auth-page__wrap">
            <div class="parent-auth-page__card">
                <h1 class="parent-auth-page__title">Login Orang Tua / Wali</h1>
                <div class="parent-auth-page__subtitle">
                    Masuk untuk melanjutkan pengisian dan melihat status/kelulusan.
                </div>

                @if ($errors->any())
                    <div class="parent-auth-page__alert">
                        <ul>
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('parent.login.store') }}">
                    @csrf

                    <div class="parent-auth-page__group">
                        <label class="parent-auth-page__label">
                            Nomor WhatsApp <span class="parent-auth-page__req">*</span>
                        </label>
                        <input name="phone" class="parent-auth-page__input" placeholder="contoh: 08xxxxxxxxxx"
                            value="{{ old('phone') }}" required>
                    </div>

                    <div class="parent-auth-page__group">
                        <label class="parent-auth-page__label">
                            Password <span class="parent-auth-page__req">*</span>
                        </label>
                        <input type="password" name="password" class="parent-auth-page__input" required>
                    </div>

                    <button class="btn btn-primary parent-auth-page__submit">Login</button>
                </form>

                <hr class="parent-auth-page__divider">

                <div class="parent-auth-page__foot">
                    <span>Belum punya akun?</span>
                    <a href="{{ route('parent.register') }}">Registrasi</a>
                </div>
            </div>
        </div>
    </section>

    <x-landing-footer />
@endsection
