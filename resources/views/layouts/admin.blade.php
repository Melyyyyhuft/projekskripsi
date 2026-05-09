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
            <div class="sidebar-title">
                PPDB Admin Panel
            </div>
            
            <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <span>🏠 Dashboard</span>
            </a>
            <a href="{{ route('admin.pengaturan.index') }}" class="sidebar-item {{ request()->is('admin/pengaturan') ? 'active' : '' }}">
                <span>⚙️ Pengaturan Sistem</span>
            </a>
            <a href="{{ route('admin.pendaftaran.index') }}" class="sidebar-item {{ request()->is('admin/pendaftaran') ? 'active' : '' }}">
                <span>📝 Pendaftaran</span>
            </a>
            <a href="{{ route('admin.bank_soal.index') }}" class="sidebar-item {{ request()->is('admin/bank_soal*') ? 'active' : '' }}">
                <span>📚 Bank Soal</span>
            </a>
            <a href="{{ route('admin.ujian.index') }}" class="sidebar-item {{ request()->is('admin/ujian*') ? 'active' : '' }}">
                <span>📝 Modul Ujian</span>
            </a>
            <a href="{{ route('admin.seleksi.index') }}" class="sidebar-item {{ request()->is('admin/seleksi*') ? 'active' : '' }}">
                <span>⚡ Proses Seleksi</span>
            </a>

            <div style="flex-grow: 1;"></div>
            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="sidebar-item" style="width: 100%; color: var(--accent); background: #fee2e2;">
                    <span>🚪 Keluar</span>
                </button>
            </form>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2>@yield('title')</h2>
                <div style="display: flex; align-items: center; gap: 1.5rem;">
                    
                    <!-- Lonceng Notifikasi -->
                    <a href="{{ route('admin.notifikasi.index') }}" style="position: relative; color: var(--dark); text-decoration: none; display: flex; align-items: center;">
                        <span style="font-size: 1.5rem;">🔔</span>
                        <span id="notifBadge" style="position: absolute; top: -5px; right: -8px; background: #ef4444; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7rem; font-weight: bold; display: none;">0</span>
                    </a>

                    <span style="font-weight: 500;">Halo, {{ Auth::user()->name }} 👋</span>
                </div>
            </header>

            @yield('content')
        </main>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fetchInterval = 15000; // 15 detik
            const notifBadge = document.getElementById('notifBadge');
            
            function updateBadge(count) {
                if (count > 0) {
                    notifBadge.style.display = 'inline-block';
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
                });
        });
    </script>
</body>
</html>
