<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'PSB Mahad Darussalam')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @include('partials.styles')
    @stack('styles')

    <style>
        /* Desktop */
        @media (min-width: 992px) {
            .app-wrapper {
                display: flex;
                min-height: 100vh;
            }

            .sidebar-desktop {
                width: 280px;
            }

            .content-wrapper {
                flex: 1;
            }

            .navbar-mobile {
                display: none;
            }
        }

        /* Mobile */
        @media (max-width: 991.98px) {
            .sidebar-desktop {
                display: none;
            }
        }
    </style>
</head>

<body class="trezo-theme">

    {{-- NAVBAR MOBILE ONLY --}}
    <div class="navbar-mobile d-lg-none">
        @include('partials.navbar-mobile')
    </div>

    {{-- OFFCANVAS MENU (MOBILE ONLY) --}}
    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header">
            <h6 class="offcanvas-title">Menu</h6>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            @include('partials.sidebar')
        </div>
    </div>

    <div class="app-wrapper">
        {{-- SIDEBAR DESKTOP ONLY --}}
        <aside class="sidebar-desktop d-none d-lg-block">
            @include('partials.sidebar')
        </aside>

        {{-- CONTENT --}}
        <main class="content-wrapper">
            <div class="container-fluid py-3">
                @yield('content')
            </div>
            @include('partials.footer')
        </main>
    </div>

    @include('partials.scripts')
    @stack('scripts')
</body>

</html>
