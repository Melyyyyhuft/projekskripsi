@extends('layouts.siswa')
@section('title', 'Dashboard Calon Siswa')

@section('content')
<div class="glass-card" style="margin-bottom: 2rem; background: linear-gradient(135deg, var(--primary), var(--secondary)); color: var(--white);">
    <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Selamat Datang, {{ Auth::user()->name }}!</h2>
    <p style="opacity: 0.9;">Ini adalah portal pendaftaran peserta didik baru. Ikuti tahapan di bawah ini untuk menyelesaikan pendaftaran Anda.</p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
    <!-- Step 1 -->
    <div class="glass-card" style="border: 2px solid var(--primary); transform: scale(1.02); box-shadow: var(--shadow-glow);">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">1</div>
            <h3 style="font-size: 1.25rem;">Isi Biodata & Pendaftaran</h3>
        </div>
        <p style="color: var(--gray-text); margin-bottom: 1.5rem;">Lengkapi data diri, nilai rapor, dan jurusan tujuan Anda.</p>
        <a href="{{ route('siswa.pendaftaran') }}" class="btn-primary" style="display: block; width: 100%; text-align: center;">Mulai Isi Data</a>
    </div>

    <!-- Step 2 -->
    <div class="glass-card" style="opacity: 0.7;">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--gray-text); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">2</div>
            <h3 style="font-size: 1.25rem;">Ujian Seleksi Online</h3>
        </div>
        <p style="color: var(--gray-text); margin-bottom: 1.5rem;">Kerjakan ujian setelah berkas Anda diverifikasi oleh Panitia.</p>
        <a href="{{ route('siswa.ujian') }}" class="btn-primary" style="display: block; width: 100%; text-align: center;">Mulai Ujian Online</a>
    </div>
</div>
@endsection
