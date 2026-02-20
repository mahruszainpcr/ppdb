<div class="sidebar-area" id="sidebar-area">
    <div class="logo position-relative sidebar-top">
        <a href="{{ url('/') }}" class="brand-top">
            <img src="https://mahaddarussalampalas.ponpes.id/logo.png" alt="logo-icon" class="brand-logo">
            <div class="brand-text">
                <strong>Darussalam Al-Islami</strong>
                <span>Palas • Rumbai</span>
            </div>
        </a>
        <div class="top-actions">
            @if (auth()->check())
                <a href="{{ route('password.change') }}" class="top-action-btn" title="Ganti Password">
                    <span class="material-symbols-outlined">lock</span>
                </a>
                @if (in_array(auth()->user()->role, ['admin', 'ustadz']))
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="top-action-btn danger" title="Logout">
                            <span class="material-symbols-outlined">logout</span>
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="top-action-btn danger" title="Logout">
                            <span class="material-symbols-outlined">logout</span>
                        </button>
                    </form>
                @endif
            @endif
        </div>
        <button
            class="sidebar-burger-menu bg-transparent p-0 border-0 opacity-0 z-n1 position-absolute top-50 end-0 translate-middle-y"
            id="sidebar-burger-menu">
            <i data-feather="x"></i>
        </button>
    </div>

    <aside id="layout-menu" class="layout-menu menu-vertical menu active" data-simplebar>
        <ul class="menu-inner">

            {{-- ================= ADMIN ================= --}}
            @if (auth()->check() && in_array(auth()->user()->role, ['admin', 'ustadz']))
                <li class="menu-title small text-uppercase">
                    <span class="menu-title-text">Dashboard</span>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="menu-link {{ Request::is('admin') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">dashboard</span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>

                <li class="menu-title small text-uppercase">
                    <span class="menu-title-text">PPDB</span>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.registrations.index') }}"
                        class="menu-link {{ Request::is('admin/registrations*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">groups</span>
                        <span class="title">Data Pendaftar</span>
                    </a>
                </li>

                <li class="menu-title small text-uppercase">
                    <span class="menu-title-text">Konten</span>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.news-categories.index') }}"
                        class="menu-link {{ Request::is('admin/news-categories*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">category</span>
                        <span class="title">Kategori Berita</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.news-posts.index') }}"
                        class="menu-link {{ Request::is('admin/news-posts*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">article</span>
                        <span class="title">Postingan Berita</span>
                    </a>
                </li>

                @if (auth()->user()->role === 'admin')
                    <li class="menu-title small text-uppercase">
                        <span class="menu-title-text">Administrasi</span>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('admin.staff.index') }}"
                            class="menu-link {{ Request::is('admin/staff*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined menu-icon">manage_accounts</span>
                            <span class="title">Admin & Ustadz</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('admin.users.index') }}"
                            class="menu-link {{ Request::is('admin.users.index') ? 'active' : '' }}">
                            <span class="material-symbols-outlined menu-icon">person</span>
                            <span class="title">User Wali</span>
                        </a>
                    </li>
                @endif

                <li class="menu-title small text-uppercase">
                    <span class="menu-title-text">Sistem Management</span>
                </li>
                <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <span class="material-symbols-outlined menu-icon">school</span>
                        <span class="title">Portal Santri</span>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item"><a class="menu-link disabled" href="#">Dashboard Pribadi <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Jadwal Akademik Real-time <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">E-Rapor & Nilai <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Presensi Mandiri <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Modul Setoran (Sorogan) <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Antrian Setoran Digital <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Unggah Rekaman Setoran <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Histori & Catatan Koreksi <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Pengajuan Izin Online <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Perpustakaan Digital <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Status Pembayaran SPP <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Unduh Formulir & Surat <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Pendaftaran Ulang Semester <span class="badge-soon">Soon</span></a></li>
                    </ul>
                </li>

                <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <span class="material-symbols-outlined menu-icon">co_present</span>
                        <span class="title">Portal Ustadz</span>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item"><a class="menu-link disabled" href="#">Dashboard Kelas <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Manajemen Kelas Maya <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Daftar Santri per Kelas <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Input Nilai <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Input Presensi Santri <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Unggah Materi Ajar <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Manajemen Sorogan <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Antrian Setoran Santri <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Nilai & Catatan Koreksi <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Capaian Pembelajaran Individu <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Kalender & Jadwal Pribadi <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Presensi Kehadiran Ustadz <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Komunikasi <span class="badge-soon">Soon</span></a></li>
                    </ul>
                </li>

                <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <span class="material-symbols-outlined menu-icon">family_restroom</span>
                        <span class="title">Portal Wali</span>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item"><a class="menu-link disabled" href="#">Dashboard Perkembangan <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Laporan Akademik <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Monitoring Keuangan <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Notifikasi & Pengumuman <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Konsultasi Online <span class="badge-soon">Soon</span></a></li>
                    </ul>
                </li>

                <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <span class="material-symbols-outlined menu-icon">work</span>
                        <span class="title">Admin & TU</span>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item"><a class="menu-link disabled" href="#">Manajemen Pengguna <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Impor Massal (Excel) <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Master Tahun Ajaran <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Master Semester <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Master Kelas/Tingkatan <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Master Mapel/Kitab <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Ustadz Pengampu <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Susun Jadwal Akademik <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Kalender Akademik <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Input Tagihan <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Pembayaran Manual <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Laporan Keuangan <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Verifikasi Izin <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Verifikasi Daftar Ulang <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Surat Keterangan Digital <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Laporan & Ekspor (PDF/Excel) <span class="badge-soon">Soon</span></a></li>
                    </ul>
                </li>

                <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <span class="material-symbols-outlined menu-icon">insights</span>
                        <span class="title">Executive</span>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item"><a class="menu-link disabled" href="#">Dashboard Eksekutif <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">KPI Santri Aktif <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Grafik Kehadiran <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Distribusi Nilai <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Rekap Keuangan <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Status Pengajuan Izin <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Laporan Cepat <span class="badge-soon">Soon</span></a></li>
                        <li class="menu-item"><a class="menu-link disabled" href="#">Monitoring Sistem <span class="badge-soon">Soon</span></a></li>
                    </ul>
                </li>

                <li class="menu-title small text-uppercase">
                    <span class="menu-title-text">Akun</span>
                </li>
                <li class="menu-item">
                    <a href="{{ route('password.change') }}"
                        class="menu-link {{ Request::is('change-password') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">lock</span>
                        <span class="title">Ganti Password</span>
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
                <li class="menu-title small text-uppercase">
                    <span class="menu-title-text">Dashboard</span>
                </li>
                <li class="menu-item">
                    <a href="/app" class="menu-link {{ Request::is('app') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">dashboard</span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>

                <li class="menu-title small text-uppercase">
                    <span class="menu-title-text">PPDB</span>
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

                <li class="menu-title small text-uppercase">
                    <span class="menu-title-text">Akun</span>
                </li>
                <li class="menu-item">
                    <a href="{{ route('password.change') }}"
                        class="menu-link {{ Request::is('change-password') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">lock</span>
                        <span class="title">Ganti Password</span>
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
