<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPDB Web - @yield('title')</title>
    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🎓</text></svg>">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Outfit:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Main Style -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <style>
        .nav-tagline { font-size: 0.7rem; font-weight: 600; color: #64748b; margin: 0; text-transform: none; font-family: 'Inter', sans-serif; letter-spacing: 0; }
        .navbar-premium { padding: 0.75rem 4rem; display: flex; justify-content: space-between; align-items: center; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-bottom: 1px solid #f1f5f9; position: fixed; top: 0; width: 100%; z-index: 1000; box-sizing: border-box; }
        .nav-brand-container { display: flex; align-items: center; gap: 1rem; text-decoration: none; }
        .nav-logo-img { width: 65px; height: auto; object-fit: contain; }
        .nav-school-name { font-family: 'Outfit', sans-serif; font-size: 1.35rem; font-weight: 900; color: #1e293b; line-height: 1; margin: 0; }
        .nav-links-center { display: flex; gap: 2rem; align-items: center; }
        .nav-link-p { font-weight: 600; color: #475569; font-size: 0.95rem; text-decoration: none; transition: all 0.3s ease; position: relative; padding: 0.5rem 0; }
        .nav-link-p:hover, .nav-link-p.active { color: #1d4ed8; }
        .nav-link-p.active::after { content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 3px; background: #1d4ed8; border-radius: 2px; }
        .nav-right-actions { display: flex; align-items: center; gap: 1.25rem; }
        .nav-social-icon { font-size: 1.25rem; color: #64748b; transition: all 0.3s ease; }
        .nav-social-icon.ig:hover { color: #e1306c; }
        .nav-social-icon.wa:hover { color: #25d366; }
        .nav-social-icon.map:hover { color: #3b82f6; }
        .btn-masuk { background: linear-gradient(135deg, #0c42bb, #2563eb); color: white; padding: 0.5rem 1.75rem; border-radius: 8px; font-weight: 700; text-decoration: none; transition: all 0.3s ease; font-size: 0.9rem; box-shadow: 0 4px 10px rgba(12, 66, 187, 0.1); }
        .btn-masuk:hover { background: linear-gradient(135deg, #093395, #0c42bb); transform: translateY(-2px); box-shadow: 0 6px 15px rgba(12, 66, 187, 0.2); }
        
        @media (max-width: 1024px) {
            .navbar-premium { padding: 0.75rem 2rem; }
            .nav-links-center { display: none; }
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const navbar = document.querySelector('.navbar-premium');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 20) {
                    navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.05)';
                    navbar.style.padding = '0.5rem 4rem';
                } else {
                    navbar.style.boxShadow = 'none';
                    navbar.style.padding = '0.75rem 4rem';
                }
            });

            // Scroll Spy for Navbar Active Links
            const navLinks = document.querySelectorAll('.nav-links-center .nav-link-p');
            const sections = document.querySelectorAll('section[id]');
            
            function updateActiveLink() {
                let scrollY = window.scrollY;
                let current = '';

                sections.forEach(section => {
                    const sectionTop = section.offsetTop - 200; // Offset for fixed navbar
                    if (scrollY >= sectionTop) {
                        current = section.getAttribute('id');
                    }
                });

                // If near top, reset to Beranda
                if (scrollY < 150) {
                    current = ''; 
                }

                navLinks.forEach(link => {
                    link.classList.remove('active');
                    const href = link.getAttribute('href');
                    if (current === '') {
                        if (href === '{{ url('/') }}' || href === '/') {
                            link.classList.add('active');
                        }
                    } else {
                        if (href.includes('#' + current)) {
                            link.classList.add('active');
                        }
                    }
                });
            }

            window.addEventListener('scroll', updateActiveLink);
            window.addEventListener('hashchange', updateActiveLink);
            updateActiveLink(); // Initial check
        });
    </script>
</head>
<body>
    <nav class="navbar-premium">
        <a href="{{ url('/') }}" class="nav-brand-container">
            <img src="{{ asset('images/logo_sekolah.png') }}" alt="Logo SMK Mitra Bintaro" class="nav-logo-img">
            <div>
                <h1 class="nav-school-name">SMK MITRA BINTARO</h1>
            </div>
        </a>

        <div class="nav-links-center">
            <a href="{{ url('/') }}" class="nav-link-p">Beranda</a>
            <a href="/#tentang" class="nav-link-p">Tentang Kami</a>
            <a href="/#jurusan" class="nav-link-p">Jurusan</a>
            <a href="/#alur" class="nav-link-p">Alur Pendaftaran</a>
        </div>

        <div class="nav-right-actions">
            <a href="https://instagram.com/mitrabintaro" target="_blank" class="nav-social-icon ig"><i class="fa-brands fa-instagram"></i></a>
            <a href="https://tiktok.com/@smk.mitrabintaro" target="_blank" class="nav-social-icon tiktok"><i class="fa-brands fa-tiktok"></i></a>
            <a href="https://youtube.com/@smkmitrabintaro-real" target="_blank" class="nav-social-icon youtube"><i class="fa-brands fa-youtube"></i></a>
            
            @auth
                <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('siswa.dashboard') }}" class="btn-masuk">
                    Dashboard
                </a>
            @else
                <a href="{{ url('/login') }}" class="btn-masuk">
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

            </div>
            
            <div>
                <h3 class="footer-title">Tautan Cepat</h3>
                <ul class="footer-links">
                    <li><a href="{{ url('/') }}">Beranda</a></li>
                    <li><a href="/#tentang">Tentang Kami</a></li>
                    <li><a href="/#jurusan">Pilihan Jurusan</a></li>
                    <li><a href="{{ url('/register') }}">Daftar Sekarang</a></li>
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
