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
            <button class="nav-toggle" type="button" aria-controls="landingMobileMenu" aria-expanded="false">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <nav class="menu" aria-label="Navigasi">
                <a href="{{ url('/#visimisi') }}">Visi & Misi</a>
                <a href="{{ url('/#fasilitas') }}">Fasilitas</a>
                <a href="{{ url('/#pilar') }}">3 Pilar</a>
                <a href="{{ url('/#program') }}">Program</a>
                <a href="{{ route('news.index') }}">Informasi</a>
                <a href="{{ url('/#kontak') }}">Kontak</a>
            </nav>
            <div class="cta">
                <a class="btn btn-ghost" href="{{ route('login') }}">Login</a>
                <a class="btn btn-primary" href="{{ route('parent.register') }}">Daftar Santri Baru</a>
            </div>
        </div>
        <div id="landingMobileMenu" class="mobile-menu" hidden>
            <nav class="mobile-links" aria-label="Navigasi Mobile">
                <a href="{{ url('/#visimisi') }}">Visi & Misi</a>
                <a href="{{ url('/#fasilitas') }}">Fasilitas</a>
                <a href="{{ url('/#pilar') }}">3 Pilar</a>
                <a href="{{ url('/#program') }}">Program</a>
                <a href="{{ route('news.index') }}">Informasi</a>
                <a href="{{ url('/#kontak') }}">Kontak</a>
            </nav>
            <div class="mobile-cta">
                <a class="btn btn-ghost" href="{{ route('login') }}">Login</a>
                <a class="btn btn-primary" href="{{ route('parent.register') }}">Daftar Santri Baru</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const nav = document.querySelector('.nav');
        if (!nav) return;
        const toggle = nav.querySelector('.nav-toggle');
        const menu = nav.querySelector('#landingMobileMenu');
        if (!toggle || !menu) return;

        const closeMenu = () => {
            menu.classList.remove('open');
            menu.setAttribute('hidden', '');
            toggle.setAttribute('aria-expanded', 'false');
        };

        toggle.addEventListener('click', () => {
            const willOpen = !menu.classList.contains('open');
            if (willOpen) {
                menu.classList.add('open');
                menu.removeAttribute('hidden');
                toggle.setAttribute('aria-expanded', 'true');
            } else {
                closeMenu();
            }
        });

        menu.addEventListener('click', (event) => {
            if (event.target.closest('a')) {
                closeMenu();
            }
        });
    });
</script>
