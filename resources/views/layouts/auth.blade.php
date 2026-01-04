<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Login | PSB Mahad Darussalam')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Trezo Styles --}}
    @include('partials.styles')
    @stack('styles')
</head>

<body class="trezo-theme authentication-bg">

    {{-- Optional Preloader --}}
    @include('partials.preloader')

    <div class="auth-wrapper d-flex align-items-center justify-content-center min-vh-100">
        <div class="auth-card-wrapper w-100 px-3">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-4 col-lg-5 col-md-6 col-sm-10">

                        {{-- Brand / Logo --}}
                        <div class="text-center mb-4">
                            {{-- Ganti logo sesuai aset Trezo / Mahad --}}
                            <img src="https://mahaddarussalampalas.ponpes.id/logo.png" alt="Mahad Darussalam"
                                class="mb-3" style="max-height:70px">

                            <h4 class="mb-1">Mahad Darussalam</h4>
                            <p class="text-muted small mb-0">
                                Sistem Pendaftaran Santri Baru
                            </p>
                        </div>

                        {{-- Auth Content --}}
                        @yield('content')

                        {{-- Footer kecil --}}
                        <div class="text-center mt-4 small text-muted">
                            © {{ date('Y') }} Mahad Darussalam · Yayasan Al-Marwa
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Trezo Scripts --}}
    @include('partials.scripts')
    @stack('scripts')

</body>

</html>
