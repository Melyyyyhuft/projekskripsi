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
            <a href="#" class="sidebar-item">
                <span>📅 Pengaturan</span>
            </a>
            <a href="#" class="sidebar-item">
                <span>🎓 Jurusan</span>
            </a>
            <a href="{{ route('admin.pendaftaran.index') }}" class="sidebar-item {{ request()->is('admin/pendaftaran') ? 'active' : '' }}">
                <span>📝 Pendaftaran</span>
            </a>
            <a href="{{ route('admin.ujian.index') }}" class="sidebar-item {{ request()->is('admin/ujian*') ? 'active' : '' }}">
                <span>📝 Modul Ujian</span>
            </a>
            <a href="{{ route('admin.seleksi.index') }}" class="sidebar-item {{ request()->is('admin/seleksi') ? 'active' : '' }}">
                <span>🏆 Hasil Seleksi</span>
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
