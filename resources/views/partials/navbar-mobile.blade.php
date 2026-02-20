<nav class="navbar navbar-dark navbar-mobile sticky-top">
    <div class="container-fluid navbar-mobile-inner">
        <button class="btn btn-outline-light btn-sm d-inline-flex align-items-center gap-1 mobile-menu-btn"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#mobileMenu"
            aria-controls="mobileMenu">
            <span class="material-symbols-outlined" style="font-size:18px;">menu</span>
            Menu
        </button>

        <div class="navbar-brand-wrap">
            <img src="https://mahaddarussalampalas.ponpes.id/logo.png" width="40" alt="Logo Darussalam" height="32"
                class="rounded">
            <span class="navbar-brand-text">
                PSB Darussalam Al-Islami Palas Rumbai Pekanbaru Riau
            </span>
        </div>

        {{-- USER MENU --}}
        <div class="dropdown ms-auto mobile-user-menu">
            <button class="btn btn-outline-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                {{ auth()->user()->name }}
            </button>

            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('app.dashboard') }}">
                        Dashboard
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger">
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>

    </div>
</nav>
