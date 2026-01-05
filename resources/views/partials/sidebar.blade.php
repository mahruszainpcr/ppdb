<div class="sidebar-area" id="sidebar-area">
    <div class="logo position-relative">
        <a href="index" class="d-block text-decoration-none position-relative">
            <img src="https://mahaddarussalampalas.ponpes.id/logo.png" alt="logo-icon">

        </a>
        <button
            class="sidebar-burger-menu bg-transparent p-0 border-0 opacity-0 z-n1 position-absolute top-50 end-0 translate-middle-y"
            id="sidebar-burger-menu">
            <i data-feather="x"></i>
        </button>
    </div>

    <aside id="layout-menu" class="layout-menu menu-vertical menu active" data-simplebar>
        <ul class="menu-inner">

            <li class="menu-title small text-uppercase">
                <span class="menu-title-text">APPS</span>
            </li>



            {{-- ================= ADMIN ================= --}}
            @if (auth()->check() && auth()->user()->role === 'admin')
                <li class="menu-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="menu-link {{ Request::is('admin') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">dashboard</span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.users.index') }}"
                        class="menu-link {{ Request::is('admin.users.index') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">person</span>
                        <span class="title">User</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('admin.registrations.index') }}"
                        class="menu-link {{ Request::is('admin/registrations*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">groups</span>
                        <span class="title">Data Pendaftar</span>
                    </a>
                </li>

                {{-- <li class="menu-item">
                    <a href="{{ route('periods.index') }}"
                        class="menu-link {{ Request::is('admin/periods*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">event</span>
                        <span class="title">Periode / Gelombang</span>
                    </a>
                </li> --}}

                <li class="menu-item">
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="menu-link btn btn-link p-0 text-start w-100">
                            <span class="material-symbols-outlined menu-icon">logout</span>
                            <span class="title">Logout</span>
                        </button>
                    </form>
                </li>

                {{-- ================= ORANG TUA ================= --}}
            @elseif(auth()->check() && auth()->user()->role === 'parent')
                <li class="menu-item">
                    <a href="/app" class="menu-link {{ Request::is('app') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">dashboard</span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('psb.wizard') }}" class="menu-link {{ Request::is('app/psb*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">edit_document</span>
                        <span class="title">Form Pendaftaran</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('psb.result') }}"
                        class="menu-link {{ Request::is('app/result') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">task_alt</span>
                        <span class="title">Pengumuman</span>
                    </a>
                </li>

                <li class="menu-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="menu-link btn btn-link p-0 text-start w-100">
                            <span class="material-symbols-outlined menu-icon">logout</span>
                            <span class="title">Logout</span>
                        </button>
                    </form>
                </li>
            @endif
        </ul>
    </aside>
</div>
