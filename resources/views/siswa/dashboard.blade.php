@extends('layouts.siswa')
@section('title', 'Dashboard Calon Siswa')

@section('content')

@if (session('success'))
    <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #c3e6cb;">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #f5c6cb;">
        {{ session('error') }}
    </div>
@endif

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
    @php
        $bisaUjian = false;
        $pesan = 'Kerjakan ujian setelah berkas Anda diverifikasi oleh Panitia.';
        $opacity = 0.7;
        $link = '#';
        $btnText = 'Mulai Ujian Online';

        if ($pendaftaran) {
            if ($pendaftaran->status == 'lolos_admin') {
                if ($ujian_aktif) {
                    $bisaUjian = true;
                    $pesan = 'Berkas Anda telah diverifikasi. Silakan kerjakan ujian seleksi online.';
                    $opacity = 1;
                    $link = route('siswa.ujian');
                } else {
                    $pesan = 'Berkas Anda telah diverifikasi, namun belum ada jadwal ujian yang aktif.';
                }
            } elseif (in_array($pendaftaran->status, ['sudah_ujian', 'diterima', 'tidak_diterima'])) {
                $pesan = 'Anda sudah mengikuti ujian seleksi online.';
                $btnText = 'Sudah Ujian';
            } elseif ($pendaftaran->status == 'ditolak_admin') {
                $pesan = 'Pendaftaran Anda ditolak. Anda tidak dapat mengikuti ujian.';
            }
        }
    @endphp

    <div class="glass-card" style="opacity: {{ $opacity }}; {{ $bisaUjian ? 'border: 2px solid var(--primary); transform: scale(1.02); box-shadow: var(--shadow-glow);' : '' }}">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: {{ $bisaUjian ? 'var(--primary)' : 'var(--gray-text)' }}; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">2</div>
            <h3 style="font-size: 1.25rem;">Ujian Seleksi Online</h3>
        </div>
        <p style="color: var(--gray-text); margin-bottom: 1.5rem;">{{ $pesan }}</p>
        
        @if ($bisaUjian)
            <a href="{{ $link }}" class="btn-primary" style="display: block; width: 100%; text-align: center;">{{ $btnText }}</a>
        @elseif ($btnText == 'Sudah Ujian')
            <button disabled style="display: block; width: 100%; text-align: center; padding: 0.75rem 1.5rem; background-color: #6c757d; color: white; border: none; border-radius: 0.5rem; font-weight: 500; cursor: not-allowed;">{{ $btnText }}</button>
        @else
            <button disabled style="display: block; width: 100%; text-align: center; padding: 0.75rem 1.5rem; background-color: #e2e8f0; color: #64748b; border: none; border-radius: 0.5rem; font-weight: 500; cursor: not-allowed;">Belum Memenuhi Syarat</button>
        @endif
    </div>
</div>
@endsection
