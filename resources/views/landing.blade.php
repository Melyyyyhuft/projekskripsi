@extends('layouts.app')
@section('title', 'Beranda')

@section('content')
<!-- HERO SECTION -->
<section class="hero">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    
    <div class="hero-content">
        <div class="badge">
            Pendaftaran Tahun Ajaran Baru Telah Dibuka
        </div>
        <h1 class="hero-title">Wujudkan Masa Depan Gemilang Bersama Kami<span class="text-gradient"></span></h1>
        <p class="hero-subtitle">Sistem Penerimaan Peserta Didik Baru yang dirancang khusus untuk memberikan pengalaman pendaftaran yang Cepat, Transparan, dan Modern.</p>
        <div class="hero-actions">
            @if($isPPDBOpen)
                <a href="{{ url('/login') }}" class="btn-primary" style="padding: 1rem 2.5rem; font-size: 1.125rem;">
                    Daftar Sekarang
                </a>
            @else
                <div style="display: flex; flex-direction: column; align-items: flex-start; gap: 0.5rem;">
                    <button class="btn-primary" style="padding: 1rem 2.5rem; font-size: 1.125rem; background: #94a3b8; cursor: not-allowed; border: none;" disabled>
                        Pendaftaran Ditutup <i class="fa-solid fa-lock"></i>
                    </button>
                    <small style="color: #64748b; font-weight: 600;">Kembali buka sesuai jadwal periode pendaftaran.</small>
                </div>
            @endif
            <a href="#tentang" class="btn-outline" style="padding: 1rem 2.5rem; font-size: 1.125rem;">
                Pelajari Lebih Lanjut
            </a>
        </div>
    </div>
</section>

<!-- FEATURES SECTION -->
<section style="padding: 6rem 2rem; background: var(--white); position: relative; z-index: 10;">
    <div class="section-title">
        <h2>Kenapa Memilih Kami?</h2>
        <p>Kami menawarkan sistem dan lingkungan pendidikan terbaik untuk mendukung perkembangan akademik dan karakter siswa.</p>
    </div>
    
    <div class="features-grid">
        <div class="feature-card glass-card">
            <div class="feature-icon">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <h3>Pendaftaran Cepat</h3>
            <p>Proses registrasi yang mudah dan cepat dari mana saja tanpa perlu antre di sekolah.</p>
        </div>
        <div class="feature-card glass-card">
            <div class="feature-icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h3>Sistem Transparan</h3>
            <p>Hasil seleksi dapat dipantau secara real-time dan dijamin objektivitasnya.</p>
        </div>
        <div class="feature-card glass-card">
            <div class="feature-icon">
                <i class="fa-solid fa-laptop-code"></i>
            </div>
            <h3>Ujian Berbasis CBT</h3>
            <p>Tes kemampuan akademik menggunakan Computer Based Test yang canggih dan akurat.</p>
        </div>
    </div>
</section>

<!-- ABOUT SECTION -->
<section id="tentang" style="padding: 6rem 2rem; background: var(--light-bg);">
    <div class="section-title">
        <h2>Profil Sekolah</h2>
        <p>Mengenal lebih dekat institusi pendidikan kami.</p>
    </div>
    
    <div class="glass-card" style="max-width: 900px; margin: 0 auto; display: flex; flex-wrap: wrap; gap: 3rem; align-items: center;">
        <div style="flex: 1; min-width: 300px;">
            <img src="https://images.unsplash.com/photo-1577896851231-70ef18881754?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Sekolah" style="width: 100%; border-radius: var(--radius-md); box-shadow: var(--shadow-md);">
        </div>
        <div style="flex: 1; min-width: 300px;">
            <h3 style="font-size: 2rem; margin-bottom: 1.5rem; color: var(--dark);">Membangun Generasi Emas</h3>
            <p style="font-size: 1.1rem; color: var(--gray-text); margin-bottom: 1.5rem;">
                Kami merupakan institusi pendidikan vokasi terdepan yang berkomitmen menghasilkan lulusan terbaik di bidang teknologi dan kreatif. 
            </p>
            <p style="font-size: 1.1rem; color: var(--gray-text); margin-bottom: 2rem;">
                Didukung dengan fasilitas yang super lengkap, laboratorium modern, dan tenaga pendidik profesional yang berpengalaman di bidangnya.
            </p>
            <div style="display: flex; gap: 2rem;">
                <div>
                    <h4 style="font-size: 2rem; color: var(--primary);">15+</h4>
                    <span style="color: var(--gray-text);">Tahun Pengalaman</span>
                </div>
                <div>
                    <h4 style="font-size: 2rem; color: var(--primary);">5000+</h4>
                    <span style="color: var(--gray-text);">Alumni Sukses</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JURUSAN SECTION -->
<section id="jurusan" style="padding: 8rem 2rem; background: var(--white);">
    <div class="section-title">
        <h2>Program Keahlian</h2>
        <p>Pilih jurusan yang sesuai dengan minat dan bakat Anda untuk masa depan yang lebih cerah.</p>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2.5rem; max-width: 1200px; margin: 0 auto;">
        @forelse($jurusans as $jurusan)
        <div class="glass-card" style="position: relative; overflow: hidden; padding: 0;">
            <div style="height: 150px; background: linear-gradient(135deg, rgba(59,130,246,0.8), rgba(139,92,246,0.8)); display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-graduation-cap" style="font-size: 4rem; color: rgba(255,255,255,0.5);"></i>
            </div>
            <div style="padding: 2.5rem;">

                <h3 style="font-size: 1.75rem; margin-bottom: 0.5rem;">{{ $jurusan->nama }}</h3>
                <p style="color: var(--gray-text); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-users"></i> Kuota: <strong>{{ $jurusan->kuota }} Siswa</strong>
                </p>
                <div style="height: 4px; width: 50px; background: var(--primary); border-radius: 2px;"></div>
            </div>
        </div>
        @empty
        <div class="text-center" style="grid-column: 1 / -1; padding: 3rem;">
            <p style="color: var(--gray-text); font-size: 1.2rem;">Belum ada data jurusan yang ditambahkan.</p>
        </div>
        @endforelse
    </div>
</section>

<!-- CALL TO ACTION -->
<section style="padding: 6rem 2rem; background: linear-gradient(135deg, var(--primary), var(--secondary)); color: var(--white); text-align: center;">
    <h2 style="font-size: 3rem; margin-bottom: 1.5rem; color: var(--white);">Siap Bergabung Bersama Kami?</h2>
    <p style="font-size: 1.25rem; margin-bottom: 3rem; opacity: 0.9; max-width: 600px; margin-left: auto; margin-right: auto;">
        Jangan lewatkan kesempatan untuk menjadi bagian dari komunitas belajar terbaik.
    </p>
    @if($isPPDBOpen)
        <a href="{{ url('/login') }}" class="btn-primary" style="background: var(--white); color: #000; padding: 1rem 3rem; font-size: 1.125rem; border: none;">
            Login & Daftar
        </a>
    @else
        <button class="btn-outline" style="border-color: rgba(255,255,255,0.5); color: rgba(255,255,255,0.7); padding: 1rem 3rem; font-size: 1.125rem; cursor: not-allowed;" disabled>
            Pendaftaran Belum Dibuka
        </button>
    @endif
</section>
@endsection
