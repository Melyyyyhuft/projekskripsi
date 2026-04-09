<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPDB Web - @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">PPDB Online</div>
        <div class="nav-links">
            <a href="{{ url('/') }}" class="nav-item">Beranda</a>
            <a href="#tentang" class="nav-item">Tentang Kami</a>
            <a href="#jurusan" class="nav-item">Jurusan</a>
            
            <div class="dropdown">
                <a href="#" class="nav-item">PPDB Online ▼</a>
                <div class="dropdown-content">
                    <a href="{{ url('/login') }}">Login / Daftar</a>
                    <a href="{{ url('/ujian') }}">Ujian Online</a>
                    <a href="{{ url('/hasil') }}">Hasil Seleksi</a>
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

</body>
</html>
