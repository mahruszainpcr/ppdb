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

        .parent-auth-page__password-wrap {
            position: relative;
        }

        .parent-auth-page__password-wrap .parent-auth-page__input {
            padding-right: 88px;
        }

        .parent-auth-page__toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #1E7F5C;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            padding: 4px 6px;
        }

        .parent-auth-page__submit {
            width: 100%;
            margin-top: 4px;
        }

        .parent-auth-page__info {
            margin-top: 14px;
            border-radius: 12px;
            border: 1px solid rgba(30, 127, 92, .24);
            background: #f0fdf4;
            padding: 12px;
        }

        .parent-auth-page__info-title {
            font-size: 13px;
            font-weight: 700;
            color: #14532d;
            margin-bottom: 4px;
        }

        .parent-auth-page__info-text {
            font-size: 12px;
            color: #166534;
            margin-bottom: 10px;
            line-height: 1.5;
        }

        .parent-auth-page__info-link {
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            border-radius: 999px;
            background: linear-gradient(135deg, #1E7F5C 0%, #166534 100%);
            border: 0;
            color: #fff;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 12px 24px rgba(22, 101, 52, .18);
            transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
        }

        .parent-auth-page__info-link:hover {
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(22, 101, 52, .22);
            filter: brightness(1.03);
        }

        .parent-auth-page__info-link:focus-visible {
            outline: 0;
            box-shadow: 0 0 0 4px rgba(30, 127, 92, .16), 0 14px 28px rgba(22, 101, 52, .22);
        }

        .parent-auth-page__info-fallback {
            font-size: 12px;
            color: #64748b;
            margin: 0;
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
                        <div class="parent-auth-page__password-wrap">
                            <input type="password" name="password" id="parentLoginPassword" class="parent-auth-page__input"
                                required>
                            <button type="button" id="toggleParentLoginPassword"
                                class="parent-auth-page__toggle-password">Lihat</button>
                        </div>
                    </div>

                    <button class="btn btn-primary parent-auth-page__submit">Login</button>
                </form>

                @php
                    $activePeriod = \App\Models\Period::query()->active()->latest('id')->first();

                @endphp

                <div class="parent-auth-page__info">
                    <div class="parent-auth-page__info-title">Bergabung Group Info</div>
                    <div class="parent-auth-page__info-text">
                        Bergabung dengan group info untuk mendapatkan username dan password dan info login pendaftaran.
                    </div>


                        <a href="https://chat.whatsapp.com/FAUOWIpZJSfHiz6JBsRTfB" target="_blank" rel="noopener"
                            class="parent-auth-page__info-link">
                            Gabung Group WA Info
                        </a>
                </div>

                <hr class="parent-auth-page__divider">

                {{-- <div class="parent-auth-page__foot">
                    <span>Belum punya akun?</span>
                    <a href="{{ url('/register') }}">Registrasi</a>
                </div> --}}
            </div>
        </div>
    </section>

    <x-landing-footer />
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('parentLoginPassword');
            const toggleButton = document.getElementById('toggleParentLoginPassword');

            if (!passwordInput || !toggleButton) {
                return;
            }

            toggleButton.addEventListener('click', function() {
                const show = passwordInput.type === 'password';
                passwordInput.type = show ? 'text' : 'password';
                toggleButton.textContent = show ? 'Sembunyikan' : 'Lihat';
            });
        });
    </script>
@endpush
