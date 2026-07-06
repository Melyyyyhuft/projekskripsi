@extends('layouts.app')
@section('title', 'Beranda')

@section('content')
<style>
    .hero-premium {
        min-height: 85vh;
        margin-top: 80px;
        display: flex;
        align-items: center;
        background: white;
        overflow: hidden;
        position: relative;
    }
    .hero-container {
        width: 100%;
        max-width: 1440px;
        margin: 0 auto;
        display: flex;
        padding: 0;
        position: relative;
    }
    .hero-left {
        flex: 1;
        padding: 4rem 4rem 4rem 7rem;
        z-index: 10;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: linear-gradient(to right, #ffffff 45%, rgba(255,255,255,0.8) 65%, transparent 100%);
    }
    .hero-right {
        flex: 1.4;
        position: relative;
        height: 700px;
        margin-left: -200px; /* Deep overlap for smooth transition */
    }
    .hero-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        mask-image: linear-gradient(to left, black 65%, transparent);
        -webkit-mask-image: linear-gradient(to left, black 65%, transparent);
    }
    .hero-title-p {
        font-family: 'Outfit', sans-serif;
        font-size: clamp(2.5rem, 4.5vw, 4rem);
        font-weight: 900;
        color: #1e293b;
        line-height: 1.1;
        margin-bottom: 0.5rem;
        letter-spacing: -1px;
    }
    .hero-title-script {
        font-family: 'Dancing Script', cursive;
        display: block;
        color: #1d4ed8;
        font-size: 0.9em;
        margin-top: 0.2rem;
        font-weight: 700;
    }
    .hero-subtitle-p {
        font-size: 1.05rem;
        color: #475569;
        line-height: 1.6;
        margin-bottom: 2.5rem;
        max-width: 500px;
        font-weight: 500;
        opacity: 0.9;
    }
    .btn-daftar-now {
        background: linear-gradient(135deg, #0c42bb, #2563eb);
        color: white;
        padding: 1rem 2.8rem;
        border-radius: 14px;
        font-size: 1.1rem;
        font-weight: 800;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.8rem;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 10px 25px -5px rgba(12, 66, 187, 0.4);
    }
    .btn-daftar-now:hover {
        background: linear-gradient(135deg, #093395, #0c42bb);
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 15px 35px -5px rgba(12, 66, 187, 0.5);
    }
    
    @media (max-width: 1024px) {
        .hero-premium { min-height: auto; }
        .hero-container { flex-direction: column; }
        .hero-left { padding: 4rem 1.5rem; text-align: center; align-items: center; background: white; margin-left: 0; }
        .hero-subtitle-p { margin-left: auto; margin-right: auto; }
        .hero-right { order: -1; height: 350px; margin-left: 0; }
        .hero-img { mask-image: none; -webkit-mask-image: none; }
    }
</style>

<!-- HERO SECTION PREMIUM -->
<section class="hero-premium">
    <div class="hero-container">
        <!-- Content Left -->
        <div class="hero-left animate-slide-up">
            <h1 class="hero-title-p">
                Langkah Awal<br>
                Meraih Masa Depan<br>
                <span class="hero-title-script">Bersama Kami</span>
            </h1>

            <p class="hero-subtitle-p">
                SMK Mitra Bintaro menghadirkan sistem PPDB yang <strong>mudah, cepat,</strong> dan <strong>terpercaya</strong> untuk membantu Anda mewujudkan masa depan terbaik.
            </p>

            <div class="hero-actions-p">
                @if($isPPDBOpen)
                    <a href="{{ url('/register') }}" class="btn-daftar-now">
                        Daftar Sekarang
                    </a>
                @else
                    <button class="btn-daftar-now" style="background: #94a3b8; cursor: not-allowed;" disabled>
                        Pendaftaran Ditutup
                    </button>
                @endif
            </div>
        </div>

        <!-- Image Right -->
        <div class="hero-right">
            <img src="{{ asset('images/hero_school.png') }}" alt="SMK Mitra Bintaro Hero" class="hero-img">
        </div>
    </div>
</section>

<!-- FEATURES SECTION -->
<section style="padding: 6rem 2rem; background: var(--white); position: relative; z-index: 10;">
    <div class="section-title">
        <h2>Kenapa Memilih SMK Mitra Bintaro?</h2>
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
            <h3 style="font-size: 2rem; margin-bottom: 1.5rem; color: var(--dark);">Membangun Generasi Siswa yang Berkarakter</h3>
            <p style="font-size: 1.1rem; color: var(--gray-text); margin-bottom: 1.5rem;">
                Kami tidak hanya berfokus pada pencapaian akademik, tetapi juga pada pembentukan karakter, kedisiplinan, dan tanggung jawab.
            </p>
            <p style="font-size: 1.1rem; color: var(--gray-text); margin-bottom: 2rem;">
                Dengan bimbingan guru serta lingkungan belajar yang positif, setiap siswa didorong untuk berkembang menjadi pribadi yang lebih baik dan siap menyongsong masa depan.
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

<!-- ALUR PENDAFTARAN SECTION -->
<section id="alur" style="padding: 8rem 2rem; background: #f8fafc;">
    <div class="section-title">
        <h2>Alur Pendaftaran</h2>
        <p>Proses pendaftaran yang mudah dan transparan untuk memudahkan Anda menjadi bagian dari kami.</p>
    </div>
    
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 2rem; max-width: 1200px; margin: 0 auto;">
        <!-- Step 1 -->
        <div class="glass-card" style="flex: 1; min-width: 250px; text-align: center; display: flex; flex-direction: column; align-items: center;">
            <div style="width: 60px; height: 60px; background: #eff6ff; color: #1d4ed8; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 900; margin-bottom: 1.5rem; border: 2px solid #dbeafe;">1</div>
            <h3 style="font-size: 1.25rem; margin-bottom: 1.25rem;">Registrasi Akun</h3>
            <p style="color: #64748b; font-size: 0.9rem;">Buat akun siswa menggunakan NISN dan email yang aktif.</p>
        </div>
        <!-- Step 2 -->
        <div class="glass-card" style="flex: 1; min-width: 250px; text-align: center; display: flex; flex-direction: column; align-items: center;">
            <div style="width: 60px; height: 60px; background: #eff6ff; color: #1d4ed8; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 900; margin-bottom: 1.5rem; border: 2px solid #dbeafe;">2</div>
            <h3 style="font-size: 1.25rem; margin-bottom: 1.25rem;">Lengkapi Biodata</h3>
            <p style="color: #64748b; font-size: 0.9rem;">Isi formulir pendaftaran, nilai rapor, dan unggah berkas fisik.</p>
        </div>
        <!-- Step 3 -->
        <div class="glass-card" style="flex: 1; min-width: 250px; text-align: center; display: flex; flex-direction: column; align-items: center;">
            <div style="width: 60px; height: 60px; background: #eff6ff; color: #1d4ed8; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 900; margin-bottom: 1.5rem; border: 2px solid #dbeafe;">3</div>
            <h3 style="font-size: 1.25rem; margin-bottom: 1.25rem;">Ujian & Hasil</h3>
            <p style="color: #64748b; font-size: 0.9rem;">Ikuti ujian online CBT dan pantau pengumuman kelulusan.</p>
        </div>
    </div>
</section>

<!-- CALL TO ACTION -->
<section style="padding: 6rem 2rem; background: linear-gradient(135deg, var(--primary), var(--secondary)); color: var(--white); text-align: center;">
    <h2 style="font-size: 3rem; margin-bottom: 1.5rem; color: var(--white);">Siap Bergabung Bersama SMK Mitra Bintaro?</h2>
    <p style="font-size: 1.25rem; margin-bottom: 3rem; opacity: 0.9; max-width: 600px; margin-left: auto; margin-right: auto;">
        Jangan lewatkan kesempatan untuk menjadi bagian dari komunitas belajar terbaik.
    </p>
    @if($isPPDBOpen)
        <a href="{{ url('/register') }}" class="btn-primary" style="background: var(--white); color: #000; padding: 1rem 3rem; font-size: 1.125rem; border: none;">
            Daftar Sekarang
        </a>
    @else
        <button class="btn-outline" style="border-color: rgba(255,255,255,0.5); color: rgba(255,255,255,0.7); padding: 1rem 3rem; font-size: 1.125rem; cursor: not-allowed;" disabled>
            Pendaftaran Belum Dibuka
        </button>
    @endif
</section>
@endsection
