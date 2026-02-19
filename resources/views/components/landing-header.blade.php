<div class="nav">
    <div class="container">
        <div class="nav-inner">
            <a class="brand" href="{{ url('/') }}" aria-label="Ma'had Darussalam Al-Islami">
                <div class="logo"><img src="https://mahaddarussalampalas.ponpes.id/logo.png" alt=""></div>
                <div>
                    <strong>Ma'had Darussalam Al-Islami</strong>
                    <span>Palas • Rumbai • Pekanbaru • Riau</span>
                </div>
            </a>
            <nav class="menu" aria-label="Navigasi">
                <a href="{{ url('/#visimisi') }}">Visi & Misi</a>
                <a href="{{ url('/#fasilitas') }}">Fasilitas</a>
                <a href="{{ url('/#pilar') }}">3 Pilar</a>
                <a href="{{ url('/#program') }}">Program</a>
                <a href="{{ url('/#kontak') }}">Kontak</a>
            </nav>
            <div class="cta">
                <a class="btn btn-ghost" href="{{ route('login') }}">Login</a>
                <a class="btn btn-primary" href="{{ route('parent.register') }}">Daftar Santri Baru</a>
            </div>
        </div>
    </div>
</div>
