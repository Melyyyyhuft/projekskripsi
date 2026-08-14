<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kepala Sekolah - @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        if(localStorage.getItem('theme') === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    </script>
    <style>
        .kepsek-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            padding: 0.25rem 0.65rem;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="siswa-bg-pattern"></div>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-title">
                <div class="sidebar-title-icon-wrap" style="background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white;">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
                <div class="sidebar-title-text">
                    <span class="sidebar-title-main">PPDB</span>
                    <span class="sidebar-title-sub">Kepala Sekolah</span>
                </div>
            </div>

            <div class="sidebar-nav-label">Menu Eksekutif</div>
            <a href="{{ route('kepala_sekolah.laporan') }}" class="sidebar-item {{ request()->is('kepala-sekolah/laporan*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Laporan Seleksi</span>
            </a>

            <div class="sidebar-divider"></div>
            <div class="sidebar-nav-label">Pengaturan Akun</div>
            <a href="{{ route('kepala_sekolah.profile') }}" class="sidebar-item {{ request()->is('kepala-sekolah/profile*') ? 'active' : '' }}">
                <i class="fa-solid fa-key"></i>
                <span>Ganti Password</span>
            </a>

            <!-- Profile Widget at the bottom -->
            <div class="sidebar-profile-card" style="cursor: default;" title="Kepala Sekolah">
                <div class="sidebar-profile-avatar-wrap">
                    <div class="sidebar-profile-avatar" style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="sidebar-profile-status" style="background: #10b981;"></div>
                </div>
                <div class="sidebar-profile-info">
                    <span class="sidebar-profile-name">{{ Auth::user()->name }}</span>
                    <span class="sidebar-profile-email">Kepala Sekolah</span>
                </div>
            </div>

            <!-- Logout Form -->
            <form action="{{ route('logout') }}" method="POST" class="sidebar-logout-form">
                @csrf
                <button type="submit" class="sidebar-logout-btn">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header>
                <div style="display:flex;align-items:center;gap:1rem;">
                    <button class="mobile-menu-btn" id="mobileMenuBtn" onclick="toggleSidebar()" style="background:none;border:none;cursor:pointer;font-size:1.2rem;display:none;">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div>
                        <h2 style="margin:0;color:var(--dark);font-size:1.15rem;font-weight:800;letter-spacing:-.02em;">@yield('title')</h2>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:1.5rem;">
                    
                    <button onclick="toggleTheme()" class="header-icon-btn" title="Toggle Dark Mode">
                        <i class="fa-solid fa-moon" id="themeIcon"></i>
                    </button>

                    {{-- Divider --}}
                    <div style="width:1px;height:24px;background:#e2e8f0;"></div>

                    {{-- User info --}}
                    <div class="dropdown">
                        <div style="display:flex;align-items:center;gap:.75rem;cursor:pointer;transition:all .2s;" onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'" onclick="this.nextElementSibling.classList.toggle('show')">
                            <div style="text-align:right;line-height:1.2;">
                                <span style="font-weight:700;font-size:.875rem;color:var(--dark);display:block;">{{ Auth::user()->name }}</span>
                                <span style="font-size:.72rem;color:var(--gray-text);font-weight:600;">Kepala Sekolah <i class="fa-solid fa-chevron-down" style="font-size: .6rem; margin-left: 2px;"></i></span>
                            </div>
                            <div style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg, #4f46e5, #7c3aed);color:white;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1rem;box-shadow:0 4px 12px rgba(99,102,241,.3);">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        </div>
                        <div class="dropdown-content" style="min-width: 170px; right: 0;">
                            <a href="{{ route('kepala_sekolah.profile') }}" style="color: var(--dark); border-bottom: 1px solid #f1f5f9;">
                                <i class="fa-solid fa-key"></i> Ganti Password
                            </a>
                            <a href="#" onclick="event.preventDefault(); document.getElementById('header-logout-form').submit();" style="color: #ef4444;">
                                <i class="fa-solid fa-right-from-bracket"></i> Keluar
                            </a>
                            <form id="header-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            @yield('content')
        </main>
    </div>

    <!-- Mobile Overlay -->
    <div id="mobileOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:999;backdrop-filter:blur(2px);" onclick="toggleSidebar()"></div>

    <script>
        // Mobile Sidebar Toggle
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('show');
            const overlay = document.getElementById('mobileOverlay');
            if (document.querySelector('.sidebar').classList.contains('show')) {
                overlay.style.display = 'block';
            } else {
                overlay.style.display = 'none';
            }
        }

        // Theme Toggle Logic
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const targetTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', targetTheme);
            localStorage.setItem('theme', targetTheme);
            document.getElementById('themeIcon').className = targetTheme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
        }
        
        // Init theme
        if(localStorage.getItem('theme') === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
            document.addEventListener('DOMContentLoaded', () => {
                const icon = document.getElementById('themeIcon');
                if(icon) icon.className = 'fa-solid fa-sun';
            });
        }

        // Click outside to close dropdown
        window.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-content.show').forEach(dd => {
                    dd.classList.remove('show');
                });
            }
        });
    </script>
</body>
</html>
