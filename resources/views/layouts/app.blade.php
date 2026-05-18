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
            <i class="fa-solid fa-graduation-cap" style="color: var(--primary);"></i> SMK MITRA BINTARO
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
                    <a href="https://tiktok.com/@smk.mitrabintaro" class="social-btn"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="https://instagram.com/mitrabintaro" class="social-btn"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://youtube.com/@smkmitrabintaro-real?si=XbGJnuRTiMQs6RvR" class="social-btn"><i class="fa-brands fa-youtube"></i></a>
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
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.3151952825665!2d106.6824302!3d-6.2221044999999995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69fb3a946bee63%3A0x7d966024c6903b4b!2sSMK%20Mitra%20Bintaro%20(Gedung%20Baru)!5e0!3m2!1sid!2sid!4v1778407953835!5m2!1sid!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <p style="margin-top: 1rem; font-size: 0.9rem;">
                    <i class="fa-solid fa-location-dot"></i> Jl. Sultan Ageng Tirtayasa No.6, RT.007/RW.008, Kunciran Indah, Kec. Pinang, Kota Tangerang, Banten 15144
                </p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} PPDB Online Sekolah. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
