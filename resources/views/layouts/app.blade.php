<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'PPDB Sekolah')</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

  {{-- Navbar dipanggil dari partial --}}
  @include('partials.navbar')

  {{-- Konten halaman --}}
  <main class="py-4">
    <div class="container">
      @yield('content')
    </div>
  </main>

  {{-- Footer --}}
  <footer class="bg-light text-center py-3 mt-5">
    &copy; {{ date('Y') }} PPDB Sekolah. All rights reserved.
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
