@extends('welcome')
@section('title', 'Registrasi Orang Tua')

@push('styles')
    <style>
        .register-page {
            background: linear-gradient(180deg, #f8fafc 0%, #eef6f2 100%);
            min-height: calc(100vh - 60px);
            padding: 48px 20px 72px;
        }

        .register-page__wrap {
            max-width: 520px;
            margin: 0 auto;
        }

        .register-page__card {
            background: #fff;
            border: 1px solid rgba(15, 23, 42, .08);
            border-radius: 18px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, .10);
            padding: 24px;
        }

        .register-page__title {
            margin: 0 0 4px;
            font-size: 20px;
            color: #0f172a;
        }

        .register-page__subtitle {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .register-page__alert {
            margin-bottom: 16px;
            border-radius: 12px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px 14px;
            font-size: 13px;
        }

        .register-page__alert ul {
            margin: 0;
            padding-left: 18px;
        }

        .register-page__group {
            margin-bottom: 14px;
        }

        .register-page__label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .register-page__req {
            color: #dc2626;
        }

        .register-page__input {
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

        .register-page__input:focus {
            border-color: #1E7F5C;
            box-shadow: 0 0 0 3px rgba(30, 127, 92, .12);
        }

        .register-page__help {
            margin-top: 5px;
            font-size: 12px;
            color: #64748b;
        }

        .register-page__submit {
            width: 100%;
            margin-top: 4px;
        }

        .register-page__divider {
            border: 0;
            border-top: 1px solid rgba(15, 23, 42, .08);
            margin: 16px 0;
        }

        .register-page__foot {
            text-align: center;
            font-size: 13px;
            color: #64748b;
        }

        .register-page__foot a {
            color: #1E7F5C;
            font-weight: 600;
            margin-left: 6px;
        }

        @media (max-width: 640px) {
            .register-page {
                padding: 32px 14px 56px;
            }

            .register-page__card {
                padding: 18px;
                border-radius: 16px;
            }
        }
    </style>
@endpush

@section('content')
    <x-landing-header />

    <section class="register-page">
        <div class="register-page__wrap">
            <div class="register-page__card">
                <h1 class="register-page__title">Registrasi Orang Tua / Wali</h1>
                <div class="register-page__subtitle">
                    Gunakan nomor WhatsApp aktif untuk login dan menerima informasi.
                </div>

                @if ($errors->any())
                    <div class="register-page__alert">
                        <ul>
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('parent.register.store') }}">
                    @csrf

                    <div class="register-page__group">
                        <label class="register-page__label">
                            Nama Orang Tua / Wali <span class="register-page__req">*</span>
                        </label>
                        <input name="name" class="register-page__input" value="{{ old('name') }}" required>
                    </div>

                    <div class="register-page__group">
                        <label class="register-page__label">
                            Nomor WhatsApp <span class="register-page__req">*</span>
                        </label>
                        <input name="phone" class="register-page__input" placeholder="contoh: 08xxxxxxxxxx"
                            value="{{ old('phone') }}" required>
                        <div class="register-page__help">Nomor akan disimpan dengan format 08xxxxxxxxxx.</div>
                    </div>

                    <div class="register-page__group">
                        <label class="register-page__label">
                            Password <span class="register-page__req">*</span>
                        </label>
                        <input type="password" name="password" class="register-page__input" required>
                        <div class="register-page__help">Minimal 8 karakter.</div>
                    </div>

                    <div class="register-page__group">
                        <label class="register-page__label">
                            Ulangi Password <span class="register-page__req">*</span>
                        </label>
                        <input type="password" name="password_confirmation" class="register-page__input" required>
                    </div>

                    <button class="btn btn-primary register-page__submit">Daftar & Lanjut Isi Form</button>
                </form>

                <hr class="register-page__divider">

                <div class="register-page__foot">
                    <span>Sudah punya akun?</span>
                    <a href="{{ route('login') }}">Login</a>
                </div>
            </div>
        </div>
    </section>

    <x-landing-footer />
@endsection
