<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="{{ url('/') }}">PPDB Sekolah</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="{{ url('/') }}">Beranda</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ url('/tentang-kami') }}">Tentang Kami</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ url('/jurusan') }}">Jurusan</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            PPDB Online
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ url('/ppdb/daftar') }}">Login / Daftar</a></li>
            <li><a class="dropdown-item" href="{{ url('/ppdb/ujian-online') }}">Ujian Online</a></li>
            <li><a class="dropdown-item" href="{{ url('/ppdb/hasil-seleksi') }}">Hasil Seleksi</a></li>
          </ul>
        </li>
      </ul>

      {{-- Sosmed kanan --}}
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="https://instagram.com/sekolah" target="_blank"><i class="bi bi-instagram"></i></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="https://wa.me/6281234567890" target="_blank"><i class="bi bi-whatsapp"></i></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="https://maps.google.com/?q=alamatsekolah" target="_blank"><i class="bi bi-geo-alt"></i></a>
        </li>
      </ul>
    </div>
  </div>
</nav>
