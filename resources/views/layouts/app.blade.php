<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'PSB Mahad Darussalam')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @include('partials.styles')
    @stack('styles')

    <style>
        html, body {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }

        .app-wrapper,
        .content-wrapper,
        .container-fluid {
            max-width: 100%;
            overflow-x: hidden;
        }

        /* Desktop */
        @media (min-width: 992px) {
            .app-wrapper {
                display: flex;
                min-height: 100vh;
            }

            .sidebar-desktop {
                width: 320px !important;
                min-width: 320px !important;
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

            .offcanvas-body {
                overflow-y: auto;
            }

            .offcanvas-body .sidebar-area {
                position: relative;
                top: 0;
                left: 0;
                width: 100%;
                min-height: 100%;
                height: auto;
            }

            .offcanvas-body .layout-menu {
                max-height: none;
            }

            .offcanvas-header,
            .offcanvas-body {
                background: linear-gradient(180deg, #0f3a2b, #0b2f23);
                color: #fff;
            }

            .content-wrapper {
                padding: 0;
            }
        }

        .sidebar-area {
            background: linear-gradient(180deg, #0f3a2b, #0b2f23);
            color: #fff;
            min-height: 100vh;
            width: 320px !important;
            min-width: 320px !important;
        }

        .layout-menu {
            width: 320px !important;
            min-width: 320px !important;
        }

        .navbar-mobile {
            background: linear-gradient(90deg, #0f3a2b, #0b2f23);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .navbar-mobile .navbar-mobile-inner {
            position: relative;
            min-height: 56px;
        }

        .navbar-mobile .navbar-brand-wrap {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            align-items: center;
            gap: 8px;
            text-align: center;
            max-width: 62%;
        }

        .navbar-mobile .navbar-brand-text {
            font-size: 13px;
            line-height: 1.2;
            color: #fff;
        }

        .navbar-mobile .mobile-menu-btn,
        .navbar-mobile .mobile-user-menu {
            z-index: 2;
        }

        @media (max-width: 575.98px) {
            .navbar-mobile .navbar-brand-wrap {
                max-width: 56%;
            }

            .navbar-mobile .navbar-brand-text {
                font-size: 12px;
            }
        }

        .sidebar-top {
            padding: 16px 16px 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .brand-top {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            text-decoration: none;
        }

        .brand-logo {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 6px;
        }

        .brand-text strong {
            display: block;
            font-size: 14px;
            line-height: 1.1;
        }

        .brand-text span {
            display: block;
            font-size: 11px;
            opacity: 0.8;
        }

        .top-actions {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }

        .top-action-btn {
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .top-action-btn.danger {
            background: rgba(239, 68, 68, 0.18);
            border-color: rgba(239, 68, 68, 0.35);
        }

        .layout-menu .menu-title-text {
            color: #fff;
            font-size: 11px;
            letter-spacing: 0.08em;
        }

        .layout-menu .menu-link {
            color: #fff !important;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
        }

        .layout-menu .menu-link.active,
        .layout-menu .menu-link:hover {
            color: #fff;
        }

        .layout-menu .menu-link:not(.active):hover {
            color: #34d399 !important;
        }

        .layout-menu .menu-link:not(.active):hover .title,
        .layout-menu .menu-link:not(.active):hover .menu-icon,
        .layout-menu .menu-link:not(.active):hover .material-symbols-outlined {
            color: #34d399 !important;
        }

        .layout-menu .menu-link.active {
            background: #fff;
            border-left: 3px solid #34d399;
            box-shadow: inset 0 0 0 1px rgba(52, 211, 153, 0.15);
            color: #34d399 !important;
        }

        .layout-menu .menu-link.active .title,
        .layout-menu .menu-link.active .material-symbols-outlined {
            color: #34d399 !important;
        }

        .layout-menu .menu-item > .menu-link {
            border-radius: 8px;
        }

        .layout-menu .menu-link.disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .layout-menu .menu-sub {
            padding-left: 12px;
            margin-top: 4px;
            border-left: 1px dashed rgba(255, 255, 255, 0.12);
        }

        .layout-menu .menu-sub .menu-link {
            font-size: 12px;
            padding: 8px 12px 8px 28px;
            color: #fff !important;
        }

        .layout-menu .menu-link:not(.active),
        .layout-menu .menu-sub .menu-link:not(.active) {
            color: #fff !important;
        }

        .layout-menu .menu-sub {
            display: none;
        }

        .layout-menu .menu-item.open > .menu-sub {
            display: block;
        }

        .menu-chevron {
            margin-left: auto;
            font-size: 18px;
            transition: transform 0.2s ease;
            opacity: 0.7;
        }

        .menu-item.open > .menu-link .menu-chevron,
        .menu-link[aria-expanded="true"] .menu-chevron {
            transform: rotate(180deg);
        }

        .badge-soon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 2px 8px;
            font-size: 10px;
            border-radius: 999px;
            margin-left: 8px;
            background: rgba(201, 162, 77, 0.18);
            color: #f5d58a;
            border: 1px solid rgba(201, 162, 77, 0.35);
        }

        .layout-menu .menu-icon {
            width: 26px;
            height: 26px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #fff !important;
        }

        .layout-menu .menu-link.active .menu-icon {
            background: rgba(52, 211, 153, 0.12);
            color: #34d399 !important;
        }

        .layout-menu .menu-link:not(.active):hover .menu-icon {
            background: rgba(52, 211, 153, 0.12);
        }

        .layout-menu .menu-inner {
            padding-bottom: 24px;
        }

        .content-wrapper .container-fluid {
            padding: 16px 18px;
        }

        @media (max-width: 991.98px) {
            .content-wrapper .container-fluid {
                padding: 14px 12px;
            }

            .content-wrapper h1,
            .content-wrapper h2,
            .content-wrapper h3,
            .content-wrapper h4 {
                word-break: break-word;
            }

            .content-wrapper .table-responsive {
                border-radius: 12px;
                overflow-x: auto;
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
            @include('partials.sidebar', ['menuId' => 'layout-menu-mobile'])
        </div>
    </div>

    <div class="app-wrapper">
        {{-- SIDEBAR DESKTOP ONLY --}}
        <aside class="sidebar-desktop d-none d-lg-block">
            @include('partials.sidebar', ['menuId' => 'layout-menu-desktop'])
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.layout-menu').forEach((menu) => {
                menu.querySelectorAll('.menu-toggle').forEach((toggle) => {
                    toggle.addEventListener('click', (event) => {
                        event.preventDefault();
                        const item = toggle.closest('.menu-item');
                        if (!item) return;
                        const isOpen = item.classList.contains('open');
                        item.classList.toggle('open', !isOpen);
                        toggle.setAttribute('aria-expanded', String(!isOpen));
                    });
                });
            });
        });
    </script>
</body>

</html>
