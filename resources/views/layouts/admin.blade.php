<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-title">PPDB Admin Panel</div>

            <div class="sidebar-nav-label">Utama</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <span>🏠 Dashboard</span>
            </a>
            <a href="{{ route('admin.pengaturan.index') }}" class="sidebar-item {{ request()->is('admin/pengaturan') ? 'active' : '' }}">
                <span>⚙️ Pengaturan Sistem</span>
            </a>

            <div class="sidebar-divider"></div>
            <div class="sidebar-nav-label">Pendaftaran</div>
            <a href="{{ route('admin.pendaftaran.index') }}" class="sidebar-item {{ request()->is('admin/pendaftaran') ? 'active' : '' }}">
                <span>📝 Data Pendaftaran</span>
            </a>

            <div class="sidebar-divider"></div>
            <div class="sidebar-nav-label">Ujian & Seleksi</div>
            <a href="{{ route('admin.bank_soal.index') }}" class="sidebar-item {{ request()->is('admin/bank_soal*') ? 'active' : '' }}">
                <span>📚 Bank Soal</span>
            </a>
            <a href="{{ route('admin.ujian.index') }}" class="sidebar-item {{ request()->is('admin/ujian*') ? 'active' : '' }}">
                <span>🖥️ Modul Ujian</span>
            </a>
            <a href="{{ route('admin.seleksi.index') }}" class="sidebar-item {{ request()->is('admin/seleksi*') ? 'active' : '' }}">
                <span>⚡ Proses Seleksi</span>
            </a>
            <a href="{{ route('admin.penempatan.index') }}" class="sidebar-item {{ request()->is('admin/penempatan*') ? 'active' : '' }}">
                <span>🏫 Seleksi &amp; Penempatan</span>
            </a>

            <div style="flex-grow:1;"></div>
            <div class="sidebar-divider"></div>

            <form action="{{ route('logout') }}" method="POST" style="padding:.5rem .75rem .75rem;">
                @csrf
                <button type="submit" class="sidebar-item" style="width:100%;background:rgba(239,68,68,.12);color:#fca5a5;margin:0;border-radius:10px;">
                    <span>🚪 Keluar</span>
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
                    <h2 style="margin:0;color:#0f172a;font-size:1.15rem;font-weight:800;letter-spacing:-.02em;">@yield('title')</h2>
                </div>
                <div style="display:flex;align-items:center;gap:1.5rem;">

                    {{-- Bell notifikasi --}}
                    <a href="{{ route('admin.notifikasi.index') }}" class="header-icon-btn">
                        <i class="fa-regular fa-bell"></i>
                        <span id="notifBadge" class="notif-badge">0</span>
                    </a>

                    {{-- Divider --}}
                    <div style="width:1px;height:24px;background:#e2e8f0;"></div>

                    {{-- User info --}}
                    <div style="display:flex;align-items:center;gap:.75rem;cursor:pointer;transition:all .2s;" onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
                        <div style="text-align:right;line-height:1.2;">
                            <span style="font-weight:700;font-size:.875rem;color:#0f172a;display:block;">{{ Auth::user()->name }}</span>
                            <span style="font-size:.72rem;color:#64748b;font-weight:600;">Administrator</span>
                        </div>
                        <div style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,var(--primary),var(--secondary));color:white;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1rem;box-shadow:0 4px 12px rgba(59,130,246,.25);">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
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

                        if (lastCount !== null && currentCount > lastCount) {
                            // Ada notifikasi baru yang belum dibaca
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'info',
                                title: 'Pendaftaran Baru!',
                                text: 'Ada pendaftar baru. Cek menu Notifikasi.',
                                showConfirmButton: false,
                                timer: 5000,
                                timerProgressBar: true,
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
    </script>
</body>
</html>
