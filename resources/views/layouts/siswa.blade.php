<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siswa Panel - @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="siswa-bg-pattern"></div>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-title">
                PPDB Siswa Panel
            </div>
            
            <a href="{{ route('siswa.dashboard') }}" class="sidebar-item {{ request()->is('siswa/dashboard') ? 'active' : '' }}">
                <span>🏠 Dashboard Utama</span>
            </a>
            <a href="{{ route('siswa.pendaftaran') }}" class="sidebar-item {{ request()->is('siswa/pendaftaran') ? 'active' : '' }}">
                <span>📝 Form Pendaftaran</span>
            </a>
            <a href="#" class="sidebar-item">
                <span>📂 Upload Berkas</span>
            </a>
            <a href="{{ route('siswa.ujian') }}" class="sidebar-item {{ request()->is('siswa/ujian') ? 'active' : '' }}">
                <span>💻 Ujian Online (CBT)</span>
            </a>
            <a href="{{ route('siswa.hasil') }}" class="sidebar-item {{ request()->is('siswa/hasil') ? 'active' : '' }}">
                <span>📢 Hasil Seleksi</span>
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
            <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; background: var(--white); padding: 1rem 2rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
                <h2 style="margin: 0; color: var(--dark);">@yield('title')</h2>
                <div style="display: flex; align-items: center; gap: 1.5rem;">
                    <div style="text-align: right; line-height: 1.2;">
                        <span style="font-weight: 600; font-size: 0.95rem; color: var(--dark); display: block;">Halo, {{ Auth::user()->name }}</span>
                        <span style="font-size: 0.8rem; color: var(--gray-text);">Calon Siswa</span>
                    </div>
                    
                    <div class="dropdown">
                        <div class="header-profile-avatar">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="dropdown-content" style="min-width: 150px; top: 120%;">
                            <a href="#"><i class="fa-solid fa-user"></i> Profil Saya</a>
                            <div style="border-top: 1px solid #e2e8f0; margin: 0.5rem 0;"></div>
                            <form action="{{ route('logout') }}" method="POST" style="margin: 0; padding: 0;">
                                @csrf
                                <button type="submit" style="width: 100%; text-align: left; background: none; border: none; padding: 0.75rem 1.25rem; color: var(--accent); font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 0.75rem;">
                                    <i class="fa-solid fa-sign-out-alt"></i> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            @yield('content')
        </main>
    </div>
</body>
</html>
