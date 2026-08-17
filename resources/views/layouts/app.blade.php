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
        .navbar-premium { padding: 0.75rem 3rem; display: flex; justify-content: space-between; align-items: center; background: rgba(255, 255, 255, 0.96); backdrop-filter: blur(12px); border-bottom: 1px solid #f1f5f9; position: fixed; top: 0; width: 100%; z-index: 1000; box-sizing: border-box; transition: all 0.3s ease; }
        .nav-brand-container { display: flex; align-items: center; gap: 0.85rem; text-decoration: none; }
        .nav-logo-img { width: 52px; height: auto; object-fit: contain; }
        .nav-school-name { font-family: 'Outfit', sans-serif; font-size: 1.25rem; font-weight: 900; color: #1e293b; line-height: 1; margin: 0; letter-spacing: -0.5px; }
        .nav-links-center { display: flex; gap: 1.25rem; align-items: center; flex-wrap: nowrap; }
        .nav-link-p { font-weight: 600; color: #475569; font-size: 0.88rem; text-decoration: none; transition: all 0.25s ease; position: relative; padding: 0.4rem 0.2rem; white-space: nowrap; }
        .nav-link-p:hover, .nav-link-p.active { color: #1d4ed8; }
        .nav-link-p.active::after { content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 2.5px; background: #1d4ed8; border-radius: 2px; }
        .nav-right-actions { display: flex; align-items: center; gap: 1rem; }
        .nav-social-icon { font-size: 1.15rem; color: #64748b; transition: all 0.25s ease; display: inline-flex; align-items: center; justify-content: center; }
        .nav-social-icon.ig:hover { color: #e1306c; transform: translateY(-2px); }
        .nav-social-icon.tiktok:hover { color: #000; transform: translateY(-2px); }
        .nav-social-icon.youtube:hover { color: #ff0000; transform: translateY(-2px); }
        .nav-social-icon.wa:hover { color: #25d366; transform: translateY(-2px); }
        .btn-masuk { background: linear-gradient(135deg, #0c42bb, #2563eb); color: white; padding: 0.45rem 1.4rem; border-radius: 8px; font-weight: 700; text-decoration: none; transition: all 0.3s ease; font-size: 0.88rem; box-shadow: 0 4px 10px rgba(12, 66, 187, 0.15); display: inline-flex; align-items: center; gap: 0.4rem; }
        .btn-masuk:hover { background: linear-gradient(135deg, #093395, #0c42bb); transform: translateY(-2px); box-shadow: 0 6px 15px rgba(12, 66, 187, 0.25); color: white; }
        
        .mobile-nav-toggle { display: none; background: transparent; border: none; font-size: 1.4rem; color: #1e293b; cursor: pointer; padding: 0.5rem; }
        .mobile-menu-drawer { display: none; position: fixed; top: 70px; left: 0; width: 100%; background: rgba(255,255,255,0.98); backdrop-filter: blur(15px); border-bottom: 1px solid #e2e8f0; padding: 1.25rem 2rem; box-shadow: 0 10px 25px rgba(0,0,0,0.08); z-index: 999; flex-direction: column; gap: 0.75rem; }
        .mobile-menu-drawer.open { display: flex; }
        .mobile-menu-drawer a { color: #334155; font-weight: 600; text-decoration: none; font-size: 0.95rem; padding: 0.5rem 0; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
        .mobile-menu-drawer a:hover { color: #1d4ed8; }

        @media (max-width: 1200px) {
            .navbar-premium { padding: 0.75rem 1.5rem; }
            .nav-links-center { gap: 0.85rem; }
            .nav-link-p { font-size: 0.82rem; }
        }

        @media (max-width: 1024px) {
            .navbar-premium { padding: 0.75rem 1.5rem; }
            .nav-links-center { display: none; }
            .mobile-nav-toggle { display: block; }
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const navbar = document.querySelector('.navbar-premium');
            const mobileToggle = document.getElementById('mobileToggle');
            const mobileDrawer = document.getElementById('mobileDrawer');

            // Route-based active state — highlight link that matches current URL path
            const currentPath = window.location.pathname;
            document.querySelectorAll('.nav-link-p[data-path]').forEach(link => {
                const linkPath = link.getAttribute('data-path');
                if (linkPath === '/' ? currentPath === '/' : currentPath.startsWith(linkPath)) {
                    link.classList.add('active');
                }
            });
            document.querySelectorAll('.mobile-menu-drawer a[data-path]').forEach(link => {
                const linkPath = link.getAttribute('data-path');
                if (linkPath === '/' ? currentPath === '/' : currentPath.startsWith(linkPath)) {
                    link.style.color = '#1d4ed8';
                    link.style.fontWeight = '800';
                }
            });

            if (mobileToggle && mobileDrawer) {
                mobileToggle.addEventListener('click', () => {
                    mobileDrawer.classList.toggle('open');
                    const icon = mobileToggle.querySelector('i');
                    if (mobileDrawer.classList.contains('open')) {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-xmark');
                    } else {
                        icon.classList.remove('fa-xmark');
                        icon.classList.add('fa-bars');
                    }
                });

                document.querySelectorAll('.mobile-menu-drawer a').forEach(item => {
                    item.addEventListener('click', () => {
                        mobileDrawer.classList.remove('open');
                        const icon = mobileToggle.querySelector('i');
                        if (icon) {
                            icon.classList.remove('fa-xmark');
                            icon.classList.add('fa-bars');
                        }
                    });
                });
            }

            window.addEventListener('scroll', () => {
                if (window.scrollY > 20) {
                    navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.06)';
                } else {
                    navbar.style.boxShadow = 'none';
                }
            });
        });
    </script>
</head>
<body>
    <nav class="navbar-premium">
        <a href="{{ url('/') }}" class="nav-brand-container">
            <img src="{{ asset('images/logo_sekolah.png') }}" alt="Logo SMK Mitra Bintaro" class="nav-logo-img">
            <div>
                <h1 class="nav-school-name">SMK MITRA BINTARO</h1>
                <p class="nav-tagline">Penerimaan Peserta Didik Baru</p>
            </div>
        </a>

        <div class="nav-links-center">
            <a href="{{ url('/') }}" class="nav-link-p" data-path="/">Beranda</a>
            <a href="{{ route('public.persyaratan') }}" class="nav-link-p" data-path="/persyaratan">Persyaratan</a>
            <a href="{{ route('public.biaya') }}" class="nav-link-p" data-path="/biaya-pembayaran">Biaya & Pembayaran</a>
            <a href="{{ route('public.faq') }}" class="nav-link-p" data-path="/faq">FAQ</a>
            <a href="{{ route('public.kontak') }}" class="nav-link-p" data-path="/kontak">Kontak</a>
        </div>

        <div class="nav-right-actions">
            @auth
                <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('siswa.dashboard') }}" class="btn-masuk">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
            @else
                <a href="{{ url('/login') }}" class="btn-masuk" style="background:transparent;color:#1e293b;border:1.5px solid #e2e8f0;box-shadow:none;">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk
                </a>
            @endauth

            <button type="button" class="mobile-nav-toggle" id="mobileToggle" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Mobile Drawer Menu -->
    <div class="mobile-menu-drawer" id="mobileDrawer">
        <a href="{{ url('/') }}" data-path="/"><span>Beranda</span> <i class="fa-solid fa-chevron-right" style="font-size:0.75rem;"></i></a>
        <a href="{{ route('public.persyaratan') }}" data-path="/persyaratan"><span>Persyaratan</span> <i class="fa-solid fa-chevron-right" style="font-size:0.75rem;"></i></a>
        <a href="{{ route('public.biaya') }}" data-path="/biaya-pembayaran"><span>Biaya & Pembayaran</span> <i class="fa-solid fa-chevron-right" style="font-size:0.75rem;"></i></a>
        <a href="{{ route('public.faq') }}" data-path="/faq"><span>FAQ (Tanya Jawab)</span> <i class="fa-solid fa-chevron-right" style="font-size:0.75rem;"></i></a>
        <a href="{{ route('public.kontak') }}" data-path="/kontak"><span>Kontak & Lokasi</span> <i class="fa-solid fa-chevron-right" style="font-size:0.75rem;"></i></a>
        @guest
        <a href="{{ url('/login') }}"><span>Masuk</span> <i class="fa-solid fa-chevron-right" style="font-size:0.75rem;"></i></a>
        @endguest
    </div>

    <main>
        @yield('content')
    </main>

    <footer class="footer">
        <div class="footer-grid">
            <div>
                <div class="footer-brand" style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1rem;">
                    <img src="{{ asset('images/logo_sekolah.png') }}" alt="Logo" style="width:42px;height:auto;">
                    <div>
                        <span style="font-family:'Outfit',sans-serif;font-weight:900;font-size:1.2rem;color:white;display:block;">SMK MITRA BINTARO</span>
                        <span style="font-size:0.75rem;color:rgba(255,255,255,0.7);font-weight:500;">PPDB Online Resmi</span>
                    </div>
                </div>
                <p style="line-height:1.6;font-size:0.9rem;opacity:0.85;margin-bottom:1.5rem;">
                    Mewujudkan generasi cerdas, berkarakter, terampil, dan berdaya saing global melalui sistem pendidikan vokasi yang modern, transparan, dan terintegrasi digital.
                </p>
                <div style="display:flex;gap:0.75rem;">
                    <a href="https://instagram.com/mitrabintaro" target="_blank" style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;color:white;text-decoration:none;transition:all 0.3s;"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://tiktok.com/@smk.mitrabintaro" target="_blank" style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;color:white;text-decoration:none;transition:all 0.3s;"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="https://youtube.com/@smkmitrabintaro-real" target="_blank" style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;color:white;text-decoration:none;transition:all 0.3s;"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>
            
            <div>
                <h3 class="footer-title">Navigasi Cepat</h3>
                <ul class="footer-links" style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;">
                    <li><a href="{{ url('/') }}"><i class="fa-solid fa-angle-right" style="font-size:0.75rem;"></i> Beranda</a></li>
                    <li><a href="{{ route('public.periode') }}"><i class="fa-solid fa-angle-right" style="font-size:0.75rem;"></i> Periode PPDB</a></li>
                    <li><a href="{{ route('public.persyaratan') }}"><i class="fa-solid fa-angle-right" style="font-size:0.75rem;"></i> Persyaratan</a></li>
                    <li><a href="{{ route('public.jurusan') }}"><i class="fa-solid fa-angle-right" style="font-size:0.75rem;"></i> Kuota Jurusan</a></li>
                    <li><a href="{{ route('public.biaya') }}"><i class="fa-solid fa-angle-right" style="font-size:0.75rem;"></i> Biaya & Bayar</a></li>
                    <li><a href="{{ route('public.alur') }}"><i class="fa-solid fa-angle-right" style="font-size:0.75rem;"></i> Alur PPDB</a></li>
                    <li><a href="{{ route('public.faq') }}"><i class="fa-solid fa-angle-right" style="font-size:0.75rem;"></i> FAQ / Tanya Jawab</a></li>
                    <li><a href="{{ route('public.kontak') }}"><i class="fa-solid fa-angle-right" style="font-size:0.75rem;"></i> Kontak Panitia</a></li>
                    <li><a href="{{ url('/register') }}" style="color:#60a5fa;font-weight:700;"><i class="fa-solid fa-arrow-right" style="font-size:0.75rem;"></i> Daftar Sekarang</a></li>
                </ul>
            </div>
            
            <div>
                <h3 class="footer-title">Pusat Layanan PPDB</h3>
                <div class="map-container" style="border-radius:10px;overflow:hidden;margin-bottom:1rem;height:140px;">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.3151952825665!2d106.6824302!3d-6.2221044999999995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69fb3a946bee63%3A0x7d966024c6903b4b!2sSMK%20Mitra%20Bintaro%20(Gedung%20Baru)!5e0!3m2!1sid!2sid!4v1778407953835!5m2!1sid!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <p style="font-size: 0.85rem; line-height: 1.5; color: rgba(255,255,255,0.85); margin-bottom: 0.5rem;">
                    <i class="fa-solid fa-location-dot" style="color: #60a5fa; margin-right: 0.35rem;"></i> Jl. Sultan Ageng Tirtayasa No.6, RT.007/RW.008, Kunciran Indah, Kec. Pinang, Kota Tangerang, Banten 15144
                </p>
                <p style="font-size: 0.85rem; color: rgba(255,255,255,0.85);">
                    <i class="fa-solid fa-headset" style="color: #60a5fa; margin-right: 0.35rem;"></i> Layanan Bantuan: Ruang PPDB Gedung Utama Lantai 1
                </p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} SMK Mitra Bintaro. Seluruh Hak Cipta Dilindungi Undang-Undang.</p>
        </div>
    </footer>
</body>
</html>
