<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siswa Panel - @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
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
            <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2>@yield('title')</h2>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <span style="font-weight: 500;">Halo, {{ Auth::user()->name }} 👋</span>
                </div>
            </header>

            @yield('content')
        </main>
    </div>
</body>
</html>
