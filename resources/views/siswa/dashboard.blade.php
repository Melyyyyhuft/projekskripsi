@extends('layouts.siswa')
@section('title', 'Dashboard Calon Siswa')

@section('content')

@if (session('success'))
    <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #c3e6cb;" class="animate-fade-in">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #f5c6cb;" class="animate-fade-in">
        {{ session('error') }}
    </div>
@endif

@php
    $bisaUjian = false;
    $pesan = 'Kerjakan ujian setelah berkas Anda diverifikasi oleh Panitia.';
    $link = '#';
    $btnText = 'Mulai Ujian Online';
    
    $step1Completed = false;
    $step2Completed = false;
    $step3Completed = false;
    $progressPercent = 0;

    if ($pendaftaran) {
        $step1Completed = true;
        $progressPercent = 33;
        
        if ($pendaftaran->status == 'lolos_admin') {
            $step2Completed = true;
            $progressPercent = 66;
            
            if ($ujian_aktif) {
                $bisaUjian = true;
                $pesan = 'Berkas Anda telah diverifikasi. Silakan kerjakan ujian seleksi online.';
                $link = route('siswa.ujian');
            } else {
                $pesan = 'Berkas Anda telah diverifikasi, namun belum ada jadwal ujian yang aktif.';
            }
        } elseif (in_array($pendaftaran->status, ['sudah_ujian', 'diterima', 'tidak_diterima'])) {
            $step2Completed = true;
            $step3Completed = true;
            $progressPercent = 100;
            $pesan = 'Anda sudah mengikuti ujian seleksi online.';
            $btnText = 'Sudah Ujian';
        } elseif ($pendaftaran->status == 'ditolak_admin') {
            $pesan = 'Pendaftaran Anda ditolak. Anda tidak dapat mengikuti ujian.';
        } elseif ($pendaftaran->status == 'menunggu_verifikasi') {
            $pesan = 'Berkas Anda sedang diverifikasi oleh panitia. Mohon menunggu.';
            $progressPercent = 50;
        }
    }
@endphp

<!-- Top Statistics -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;" class="animate-slide-up">
    <!-- Stat 1 -->
    <div class="stats-card">
        <div class="stats-icon" style="background: rgba(59, 130, 246, 0.1); color: var(--primary);">
            <i class="fa-solid fa-clipboard-user"></i>
        </div>
        <div style="flex: 1;">
            <p style="margin: 0; font-size: 0.875rem; color: var(--gray-text); font-weight: 600;">Status Pendaftaran</p>
            <h3 style="margin: 0; font-size: 1.1rem; color: var(--dark);">
                {{ $pendaftaran ? ucwords(str_replace('_', ' ', $pendaftaran->status)) : 'Belum Mendaftar' }}
            </h3>
        </div>
    </div>
    
    <!-- Stat 2 -->
    <div class="stats-card">
        <div class="stats-icon" style="background: rgba(139, 92, 246, 0.1); color: var(--secondary);">
            <i class="fa-solid fa-laptop-file"></i>
        </div>
        <div style="flex: 1;">
            <p style="margin: 0; font-size: 0.875rem; color: var(--gray-text); font-weight: 600;">Status Ujian</p>
            <h3 style="margin: 0; font-size: 1.1rem; color: var(--dark);">
                {{ $bisaUjian ? 'Siap Dikerjakan' : ($step3Completed ? 'Selesai' : 'Belum Tersedia') }}
            </h3>
        </div>
    </div>

    <!-- Stat 3 -->
    <div class="stats-card" style="flex-direction: column; align-items: stretch; justify-content: center; gap: 0.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <p style="margin: 0; font-size: 0.875rem; color: var(--gray-text); font-weight: 600;">Kelengkapan Berkas</p>
            <span style="font-weight: 700; color: var(--primary);">{{ $progressPercent }}%</span>
        </div>
        <div class="progress-bar-container">
            <div class="progress-bar-fill" style="width: {{ $progressPercent }}%;"></div>
        </div>
    </div>
</div>

<!-- Welcome Card -->
<div class="glass-card welcome-gradient animate-slide-up" style="margin-bottom: 2.5rem; padding: 2.5rem;">
    <h2 style="font-size: 1.8rem; margin-bottom: 0.5rem; color: var(--dark);">Selamat Datang, {{ Auth::user()->name }}! 👋</h2>
    <p style="color: var(--gray-text); font-size: 1.1rem; max-width: 700px; position: relative; z-index: 1;">
        Ini adalah portal pendaftaran peserta didik baru. Ikuti tahapan di bawah ini dengan berurutan untuk menyelesaikan pendaftaran Anda dengan baik.
    </p>
</div>

<!-- Progress Steps Horizontal -->
<div class="glass-card animate-slide-up" style="margin-bottom: 2rem; padding-top: 2.5rem; animation-delay: 0.2s;">
    <h3 style="text-align: center; margin-bottom: 2.5rem; color: var(--dark);">Alur Pendaftaran Anda</h3>
    
    <div class="step-container">
        <!-- Step 1 -->
        <div class="step-item {{ $step1Completed ? 'completed' : 'active' }}">
            <div class="step-circle">
                @if($step1Completed)
                    <i class="fa-solid fa-check"></i>
                @else
                    1
                @endif
            </div>
            <div class="step-label">Isi Biodata & Berkas</div>
        </div>

        <!-- Step 2 -->
        <div class="step-item {{ $step2Completed ? 'completed' : ($step1Completed ? 'active' : '') }}">
            <div class="step-circle">
                @if($step2Completed)
                    <i class="fa-solid fa-check"></i>
                @else
                    2
                @endif
            </div>
            <div class="step-label">Verifikasi Panitia</div>
        </div>

        <!-- Step 3 -->
        <div class="step-item {{ $step3Completed ? 'completed' : ($step2Completed ? 'active' : '') }}">
            <div class="step-circle">
                @if($step3Completed)
                    <i class="fa-solid fa-check"></i>
                @else
                    3
                @endif
            </div>
            <div class="step-label">Ujian Online</div>
        </div>
    </div>
</div>

<!-- Action Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;" class="animate-slide-up">
    <!-- Card 1: Pendaftaran -->
    <div class="glass-card hover-scale" style="{{ $step1Completed ? 'border: 2px solid #10b981;' : 'border: 2px solid var(--primary);' }}">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: {{ $step1Completed ? '#10b981' : 'var(--primary)' }}; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fa-solid fa-file-signature"></i>
            </div>
            <h3 style="font-size: 1.25rem; margin: 0;">Biodata & Pendaftaran</h3>
        </div>
        <p style="color: var(--gray-text); margin-bottom: 1.5rem; min-height: 48px;">
            Lengkapi data diri, nilai rapor, dokumen persyaratan, dan pilih jurusan tujuan Anda.
        </p>
        
        @if($step1Completed)
            <a href="{{ route('siswa.pendaftaran') }}" class="btn-outline" style="display: block; width: 100%; text-align: center; border-color: #10b981; color: #10b981;">Lihat / Edit Data</a>
        @else
            <a href="{{ route('siswa.pendaftaran') }}" class="btn-primary" style="display: block; width: 100%; text-align: center;">Mulai Isi Data</a>
        @endif
    </div>

    <!-- Card 2: Ujian -->
    <div class="glass-card hover-scale" style="opacity: {{ $bisaUjian || $step3Completed ? '1' : '0.6' }}; {{ $bisaUjian ? 'border: 2px solid var(--primary);' : '' }}">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: {{ $bisaUjian ? 'var(--primary)' : ($step3Completed ? '#10b981' : 'var(--gray-text)') }}; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fa-solid fa-desktop"></i>
            </div>
            <h3 style="font-size: 1.25rem; margin: 0;">Ujian Seleksi Online</h3>
        </div>
        <p style="color: var(--gray-text); margin-bottom: 1.5rem; min-height: 48px;">{{ $pesan }}</p>
        
        @if ($bisaUjian)
            <a href="{{ $link }}" class="btn-primary" style="display: block; width: 100%; text-align: center;">{{ $btnText }}</a>
        @elseif ($step3Completed)
            <button disabled style="display: block; width: 100%; text-align: center; padding: 0.875rem 2rem; background-color: #10b981; color: white; border: none; border-radius: 999px; font-weight: 600; cursor: not-allowed;"><i class="fa-solid fa-check"></i> Sudah Ujian</button>
        @else
            <button disabled style="display: block; width: 100%; text-align: center; padding: 0.875rem 2rem; background-color: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; border-radius: 999px; font-weight: 600; cursor: not-allowed;">Belum Tersedia</button>
        @endif
    </div>
</div>
@endsection
