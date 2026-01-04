<nav class="navbar navbar-dark bg-dark sticky-top">
    <div class="container-fluid">

        <div class="d-flex align-items-center gap-2">
            <img src="https://mahaddarussalampalas.ponpes.id/logo.png" width="40" alt="Logo Darussalam" height="32"
                class="rounded">

            <span class="navbar-brand mb-0 h6">
                PSB Darussalam Al-Islami Palas
            </span>
        </div>

        {{-- USER MENU --}}
        <div class="dropdown ms-auto">
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
