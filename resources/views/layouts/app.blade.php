<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPDB Web - @yield('title')</title>
    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🎓</text></svg>">
    <!-- Main Style -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const navbar = document.querySelector('.navbar');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
        });
    </script>
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <i class="fa-solid fa-graduation-cap" style="color: var(--primary);"></i> PPDB Online
        </div>
        <div class="nav-links">
            <a href="{{ url('/') }}" class="nav-item {{ request()->is('/') ? 'active' : '' }}">Beranda</a>
            <a href="/#tentang" class="nav-item">Tentang Kami</a>
            <a href="/#jurusan" class="nav-item">Jurusan</a>
            
            <div class="dropdown">
                <a href="#" class="nav-item">Menu Akses <i class="fa-solid fa-chevron-down" style="font-size: 0.8rem;"></i></a>
                <div class="dropdown-content">
                    <a href="{{ url('/login') }}"><i class="fa-solid fa-right-to-bracket"></i> Login / Daftar</a>
                    <a href="{{ url('/ppdb/ujian-online') }}"><i class="fa-solid fa-laptop-code"></i> Ujian Online</a>
                    <a href="{{ url('/ppdb/hasil-seleksi') }}"><i class="fa-solid fa-bullhorn"></i> Hasil Seleksi</a>
                </div>
            </div>
            
            @auth
                <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('siswa.dashboard') }}" class="btn-primary" style="padding: 0.5rem 1.5rem;">
                    Dashboard
                </a>
            @else
                <a href="{{ url('/login') }}" class="btn-primary" style="padding: 0.5rem 1.5rem;">
                    Masuk
                </a>
            @endauth
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="footer">
        <div class="footer-grid">
            <div>
                <div class="footer-brand">
                    <i class="fa-solid fa-graduation-cap"></i> PPDB Online
                </div>
                <p>Mewujudkan generasi cerdas, berkarakter, dan berdaya saing global melalui sistem pendidikan yang modern dan transparan.</p>
                <div class="social-links">
                    <a href="#" class="social-btn"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="social-btn"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" class="social-btn"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="social-btn"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>
            
            <div>
                <h3 class="footer-title">Tautan Cepat</h3>
                <ul class="footer-links">
                    <li><a href="{{ url('/') }}">Beranda</a></li>
                    <li><a href="/#tentang">Tentang Kami</a></li>
                    <li><a href="/#jurusan">Pilihan Jurusan</a></li>
                    <li><a href="{{ url('/login') }}">Daftar Sekarang</a></li>
                </ul>
            </div>
            
            <div>
                <h3 class="footer-title">Lokasi Kami</h3>
                <div class="map-container">
                    <!-- Placeholder Map - Google Maps Iframe -->
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126920.24056230043!2d106.74542525164966!3d-6.229740134441584!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3e945e34b9d%3A0x100c5e82dd4b820!2sJakarta%2C%20Daerah%20Khusus%20Ibukota%20Jakarta!5e0!3m2!1sid!2sid!4v1689000000000!5m2!1sid!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <p style="margin-top: 1rem; font-size: 0.9rem;">
                    <i class="fa-solid fa-location-dot"></i> Jl. Pendidikan No. 123, Jakarta, Indonesia
                </p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} PPDB Online Sekolah. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
