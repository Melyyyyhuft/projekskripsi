<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siswa Panel - @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* ── Profile Modal ── */
        #profileModal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(15,23,42,.5);
            backdrop-filter: blur(3px);
            align-items: center;
            justify-content: center;
        }
        #profileModal.open { display: flex; }

        .pm-box {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0,0,0,.18);
            width: 100%;
            max-width: 340px;
            overflow: hidden;
            animation: scaleIn .2s ease-out;
        }

        /* Header strip */
        .pm-header {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            padding: 1.25rem 1.25rem 2.5rem;
            position: relative;
            text-align: center;
        }
        .pm-close {
            position: absolute;
            top: .6rem; right: .6rem;
            background: rgba(255,255,255,.2);
            border: none; color: white;
            width: 28px; height: 28px;
            border-radius: 50%; cursor: pointer;
            font-size: .85rem;
            display: flex; align-items: center; justify-content: center;
        }
        .pm-close:hover { background: rgba(255,255,255,.35); }

        /* Avatar dengan overlap ke body */
        .pm-avatar-wrap {
            position: relative;
            display: inline-block;
            margin-top: .25rem;
        }
        .pm-avatar {
            width: 72px; height: 72px;
            border-radius: 50%;
            background: white;
            color: #3b82f6;
            font-size: 1.8rem; font-weight: 900;
            display: flex; align-items: center; justify-content: center;
            border: 3px solid rgba(255,255,255,.7);
            box-shadow: 0 4px 14px rgba(0,0,0,.15);
            overflow: hidden;
            cursor: pointer;
        }
        .pm-avatar img {
            width: 100%; height: 100%;
            object-fit: cover;
        }
        /* Tombol kamera di atas avatar */
        .pm-camera-btn {
            position: absolute;
            bottom: 0; right: 0;
            width: 24px; height: 24px;
            background: white;
            border: 2px solid #3b82f6;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .6rem; color: #3b82f6;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0,0,0,.15);
        }

        /* Body */
        .pm-body {
            padding: 1rem 1.25rem 1.25rem;
        }
        .pm-name {
            text-align: center;
            font-size: 1rem; font-weight: 700;
            color: var(--dark); margin: 0 0 .1rem;
        }
        .pm-role {
            text-align: center;
            font-size: .75rem; color: var(--gray-text);
            margin: 0 0 1rem;
        }

        .pm-row {
            display: flex; align-items: center; gap: .6rem;
            padding: .55rem .75rem;
            background: #f8fafc;
            border-radius: 8px;
            margin-bottom: .5rem;
        }
        .pm-icon {
            width: 28px; height: 28px;
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem; flex-shrink: 0;
        }
        .pm-row-text p { margin: 0; }
        .pm-label { font-size: .68rem; color: #94a3b8; font-weight: 600; }
        .pm-value { font-size: .85rem; font-weight: 600; color: var(--dark); word-break: break-all; }

        /* Upload foto hint */
        .pm-upload-hint {
            text-align: center;
            font-size: .72rem; color: #94a3b8;
            margin: .25rem 0 .75rem;
        }

        /* Logout */
        .pm-logout {
            width: 100%; padding: .7rem;
            background: #fee2e2; color: #dc2626;
            border: none; border-radius: 8px;
            font-weight: 600; font-size: .85rem;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: .4rem;
            margin-top: .75rem;
        }
        .pm-logout:hover { background: #fecaca; }

        /* Header avatar */
        .header-profile-avatar {
            cursor: pointer;
            transition: transform .2s, box-shadow .2s;
            overflow: hidden;
        }
        .header-profile-avatar:hover {
            transform: scale(1.08);
            box-shadow: 0 0 0 3px rgba(59,130,246,.3);
        }
        .header-profile-avatar img {
            width: 100%; height: 100%; object-fit: cover;
        }
    </style>
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
            <div class="sidebar-title">PPDB Siswa Panel</div>
            <div class="sidebar-nav-label">Menu</div>
            <a href="{{ route('siswa.dashboard') }}" class="sidebar-item {{ request()->is('siswa/dashboard') ? 'active' : '' }}">
                <span>🏠 Dashboard Utama</span>
            </a>

            <div class="sidebar-divider"></div>
            <div class="sidebar-nav-label">Pendaftaran</div>
            <a href="{{ route('siswa.pendaftaran') }}" class="sidebar-item {{ request()->is('siswa/pendaftaran') ? 'active' : '' }}">
                <span>📋 Pendaftaran &amp; Berkas</span>
            </a>

            <div class="sidebar-divider"></div>
            <div class="sidebar-nav-label">Seleksi</div>
            <a href="{{ route('siswa.ujian') }}" class="sidebar-item {{ request()->is('siswa/ujian*') ? 'active' : '' }}">
                <span>💻 Ujian Online (CBT)</span>
            </a>
            <a href="{{ route('siswa.hasil') }}" class="sidebar-item {{ request()->is('siswa/hasil') ? 'active' : '' }}">
                <span>📢 Hasil Seleksi</span>
            </a>

            <form action="{{ route('logout') }}" method="POST" style="padding: 1.5rem 1rem 1.5rem; margin-top: auto;">
                @csrf
                <button type="submit" class="sidebar-item danger-btn" style="width: 100%; background: rgba(239, 68, 68, 0.08); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.15); border-radius: 10px; margin: 0; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">
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
                    <h2 style="margin:0;color:var(--dark);font-size:1.15rem;font-weight:800;letter-spacing:-.02em;">@yield('title')</h2>
                </div>
                <div style="display:flex;align-items:center;gap:1.5rem;">
                    <button onclick="toggleTheme()" class="header-icon-btn" title="Toggle Dark Mode">
                        <i class="fa-solid fa-moon" id="themeIcon"></i>
                    </button>
                    <div style="text-align:right;line-height:1.2;">
                        <span style="font-weight:700;font-size:.875rem;color:var(--dark);display:block;">{{ Auth::user()->name }}</span>
                        <span style="font-size:.72rem;color:var(--gray-text);font-weight:600;">{{ Auth::user()->email }}</span>
                    </div>
                    {{-- Avatar klik buka modal --}}
                    <div class="header-profile-avatar" onclick="document.getElementById('profileModal').classList.add('open')" title="Profil Saya"
                         style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,var(--primary),var(--secondary));color:white;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1rem;box-shadow:0 4px 12px rgba(59,130,246,.25);cursor:pointer;transition:transform .2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        @if(Auth::user()->foto)
                            <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="foto" style="width:100%;height:100%;object-fit:cover;border-radius:12px;">
                        @else
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        @endif
                    </div>
                </div>
            </header>

            @if(session('success'))
            <div style="background:#d1fae5;color:#065f46;padding:.75rem 1.25rem;border-radius:8px;margin-bottom:1.5rem;font-size:.875rem;">✅ {{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div style="background:#fee2e2;color:#991b1b;padding:.75rem 1.25rem;border-radius:8px;margin-bottom:1.5rem;font-size:.875rem;">⚠️ {{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Mobile Overlay -->
    <div id="mobileOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:999;backdrop-filter:blur(2px);" onclick="toggleSidebar()"></div>

    {{-- ═══ Modal Profil ═══ --}}
    <div id="profileModal" onclick="if(event.target===this)this.classList.remove('open')">
        <div class="pm-box">

            {{-- Header --}}
            <div class="pm-header">
                <button class="pm-close" onclick="document.getElementById('profileModal').classList.remove('open')">
                    <i class="fa-solid fa-xmark"></i>
                </button>

                <div class="pm-avatar-wrap">
                    <div class="pm-avatar" onclick="document.getElementById('fotoInput').click()" title="Klik untuk ganti foto">
                        @if(Auth::user()->foto)
                            <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="foto profil">
                        @else
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="pm-camera-btn" onclick="document.getElementById('fotoInput').click()">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div class="pm-body">
                <p class="pm-name">{{ Auth::user()->name }}</p>
                <p class="pm-role">Calon Siswa PPDB</p>

                {{-- Upload foto form (tersembunyi) --}}
                <form action="{{ route('siswa.profil.foto') }}" method="POST" enctype="multipart/form-data" id="fotoForm">
                    @csrf
                    <input type="file" id="fotoInput" name="foto" accept="image/jpeg,image/png,image/webp"
                           style="display:none;" onchange="document.getElementById('fotoForm').submit();">
                </form>
                <p class="pm-upload-hint" style="font-size:.7rem;text-align:center;color:#64748b;margin-bottom:1rem;cursor:pointer;" onclick="document.getElementById('fotoInput').click()">
                    <i class="fa-solid fa-camera"></i> Klik foto untuk mengganti (JPG/PNG, maks 2MB)
                </p>

                @if($errors->has('foto'))
                <p style="color:#dc2626;font-size:.75rem;text-align:center;margin:-.5rem 0 .5rem;">{{ $errors->first('foto') }}</p>
                @endif

                <div class="pm-row">
                    <div class="pm-icon" style="background:#eff6ff;color:#3b82f6;"><i class="fa-solid fa-user"></i></div>
                    <div class="pm-row-text">
                        <p class="pm-label">Nama Lengkap</p>
                        <p class="pm-value">{{ Auth::user()->name }}</p>
                    </div>
                </div>

                <div class="pm-row">
                    <div class="pm-icon" style="background:#f0fdf4;color:#059669;"><i class="fa-solid fa-envelope"></i></div>
                    <div class="pm-row-text" style="min-width:0;">
                        <p class="pm-label">Email</p>
                        <p class="pm-value">{{ Auth::user()->email }}</p>
                    </div>
                </div>

                <div class="pm-row">
                    <div class="pm-icon" style="background:#fdf4ff;color:#9333ea;"><i class="fa-solid fa-calendar"></i></div>
                    <div class="pm-row-text">
                        <p class="pm-label">Bergabung</p>
                        <p class="pm-value">{{ Auth::user()->created_at->format('d M Y') }}</p>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="pm-logout">
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar dari Akun
                    </button>
                </form>
            </div>
        </div>
    </div>

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

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') document.getElementById('profileModal').classList.remove('open');
        });

        // Buka modal otomatis jika ada error validasi foto
        @if($errors->has('foto'))
            document.getElementById('profileModal').classList.add('open');
        @endif

        // Notifikasi Toast
        @if(session('success_foto'))
            Swal.fire({
                toast: true, position: 'top-end', icon: 'success',
                title: '{{ session('success_foto') }}',
                showConfirmButton: false, timer: 3000
            });
        @endif

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
    </script>
</body>
</html>
