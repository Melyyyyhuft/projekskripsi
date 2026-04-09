@extends('layouts.app')
@section('title', 'Beranda')

@section('content')
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Selamat Datang di PPDB Sekolah</h1>
        <p class="hero-subtitle">Mari wujudkan masa depan yang gemilang bersama kami. Pendaftaran Cepat, Transparan, dan Modern.</p>
        <a href="{{ url('/login') }}" class="btn-primary" style="font-size: 1.125rem; padding: 1rem 2.5rem;">Daftar Sekarang</a>
    </div>
</section>

<section id="tentang" style="padding: 6rem 4rem; text-align: center; background: #fff;">
    <h2 style="font-size: 2.5rem; margin-bottom: 2rem; color: var(--primary);">Profil Sekolah</h2>
    <div class="glass-card" style="max-width: 800px; margin: 0 auto; box-shadow: none; border: 1px solid #e2e8f0;">
        <p style="font-size: 1.125rem; color: var(--gray-text);">Kami merupakan institusi pendidikan vokasi terdepan yang berkomitmen menghasilkan lulusan terbaik di bidang teknologi dan kreatif. Didukung dengan fasilitas yang super lengkap dan tenaga pendidik profesional.</p>
    </div>
</section>

<section id="jurusan" style="padding: 6rem 4rem; text-align: center; background: var(--light-bg);">
    <h2 style="font-size: 2.5rem; margin-bottom: 3rem; color: var(--primary);">Pilihan Jurusan</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; max-width: 1200px; margin: 0 auto;">
        @foreach($jurusans as $jurusan)
        <div class="glass-card" style="box-shadow: var(--shadow-sm); border: 1px solid #e2e8f0; transition: var(--transition);">
            <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">{{ $jurusan->nama }}</h3>
            <p style="color: var(--gray-text); margin-bottom: 1.5rem;">Kuota: <strong>{{ $jurusan->kuota }} Siswa</strong></p>
            <span style="display: inline-block; padding: 0.5rem 1rem; background: #e0e7ff; color: var(--primary); border-radius: var(--radius-sm); font-size: 0.875rem; font-weight: 600;">Program Unggulan</span>
        </div>
        @endforeach
    </div>
</section>
@endsection
