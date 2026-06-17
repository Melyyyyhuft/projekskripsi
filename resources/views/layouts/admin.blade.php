<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script>
        if(localStorage.getItem('theme') === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    </script>
</head>
<body>
    <div class="siswa-bg-pattern"></div>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-title">
                <div class="sidebar-title-icon-wrap">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div class="sidebar-title-text">
                    <span class="sidebar-title-main">PPDB</span>
                    <span class="sidebar-title-sub">Admin Panel</span>
                </div>
            </div>

            <div class="sidebar-nav-label">Utama</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.pengaturan.index') }}" class="sidebar-item {{ request()->is('admin/pengaturan') ? 'active' : '' }}">
                <i class="fa-solid fa-sliders"></i>
                <span>Pengaturan Sistem</span>
            </a>

            <div class="sidebar-divider"></div>
            <div class="sidebar-nav-label">Pendaftaran</div>
            <a href="{{ route('admin.pendaftaran.index') }}" class="sidebar-item {{ request()->is('admin/pendaftaran') ? 'active' : '' }}">
                <i class="fa-solid fa-clipboard-list"></i>
                <span>Data Pendaftaran</span>
            </a>

            <div class="sidebar-divider"></div>
            <div class="sidebar-nav-label">Ujian &amp; Seleksi</div>
            <a href="{{ route('admin.bank_soal.index') }}" class="sidebar-item {{ request()->is('admin/bank_soal*') ? 'active' : '' }}">
                <i class="fa-solid fa-book-open"></i>
                <span>Bank Soal</span>
            </a>
            <a href="{{ route('admin.ujian.index') }}" class="sidebar-item {{ request()->is('admin/ujian*') ? 'active' : '' }}">
                <i class="fa-solid fa-laptop-code"></i>
                <span>Modul Ujian</span>
            </a>
            <a href="{{ route('admin.penempatan.index') }}" class="sidebar-item {{ request()->is('admin/penempatan*') ? 'active' : '' }}">
                <i class="fa-solid fa-square-poll-vertical"></i>
                <span>Hasil Seleksi</span>
            </a>



            <!-- Profile Widget at the bottom -->
            <div class="sidebar-profile-card" style="cursor: default;" title="Administrator">
                <div class="sidebar-profile-avatar-wrap">
                    <div class="sidebar-profile-avatar" style="background: linear-gradient(135deg, #1e40af, #3b82f6);">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="sidebar-profile-status"></div>
                </div>
                <div class="sidebar-profile-info">
                    <span class="sidebar-profile-name">{{ Auth::user()->name }}</span>
                    <span class="sidebar-profile-email">Administrator</span>
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
                    <h2 style="margin:0;color:var(--dark);font-size:1.15rem;font-weight:800;letter-spacing:-.02em;">@yield('title')</h2>
                </div>
                <div style="display:flex;align-items:center;gap:1.5rem;">
                    
                    <button onclick="toggleTheme()" class="header-icon-btn" title="Toggle Dark Mode">
                        <i class="fa-solid fa-moon" id="themeIcon"></i>
                    </button>

                    {{-- Bell notifikasi --}}
                    <a href="{{ route('admin.notifikasi.index') }}" class="header-icon-btn">
                        <i class="fa-regular fa-bell"></i>
                        <span id="notifBadge" class="notif-badge">0</span>
                    </a>

                    {{-- Divider --}}
                    <div style="width:1px;height:24px;background:#e2e8f0;"></div>

                    {{-- User info --}}
                    <div class="dropdown">
                        <div style="display:flex;align-items:center;gap:.75rem;cursor:pointer;transition:all .2s;" onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'" onclick="this.nextElementSibling.classList.toggle('show')">
                            <div style="text-align:right;line-height:1.2;">
                                <span style="font-weight:700;font-size:.875rem;color:var(--dark);display:block;">{{ Auth::user()->name }}</span>
                                <span style="font-size:.72rem;color:var(--gray-text);font-weight:600;">Administrator <i class="fa-solid fa-chevron-down" style="font-size: .6rem; margin-left: 2px;"></i></span>
                            </div>
                            <div style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,var(--primary),var(--secondary));color:white;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1rem;box-shadow:0 4px 12px rgba(59,130,246,.25);">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        </div>
                        <div class="dropdown-content" style="min-width: 160px; right: 0;">
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

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        document.addEventListener('DOMContentLoaded', function () {
            const fetchInterval = 15000; // 15 detik
            const notifBadge = document.getElementById('notifBadge');
            
            function updateBadge(count) {
                if (count > 0) {
                    notifBadge.style.display = 'flex';
                    notifBadge.innerText = count > 99 ? '99+' : count;
                } else {
                    notifBadge.style.display = 'none';
                }
            }

            function checkNewRegistrations() {
                fetch('/admin/api/cek-notifikasi')
                    .then(response => response.json())
                    .then(data => {
                        let currentCount = data.count;
                        let lastCount = localStorage.getItem('unreadNotifCount');
                        
                        updateBadge(currentCount);

                        if (lastCount !== null && currentCount > lastCount && data.latest) {
                            // Ada notifikasi baru yang belum dibaca
                            const isRevisi = data.latest.type === 'revisi';
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: isRevisi ? 'warning' : 'info',
                                iconColor: isRevisi ? '#f59e0b' : '#10b981',
                                title: isRevisi ? 'Revisi Masuk!' : 'Pendaftaran Baru!',
                                text: data.latest.pesan,
                                showConfirmButton: false,
                                timer: 6000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('click', () => {
                                        window.location.href = "{{ route('admin.notifikasi.index') }}";
                                    })
                                }
                            });
                        }
                        
                        localStorage.setItem('unreadNotifCount', currentCount);
                    })
                    .catch(err => console.error('Error fetching notification:', err));
            }

            // Jalankan polling
            setInterval(checkNewRegistrations, fetchInterval);
            
            // Inisialisasi saat pertama load
            fetch('/admin/api/cek-notifikasi')
                .then(response => response.json())
                .then(data => {
                    localStorage.setItem('unreadNotifCount', data.count);
                    updateBadge(data.count);
                })
                .catch(err => console.error(err));
        });

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
