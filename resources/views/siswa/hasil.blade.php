@extends('layouts.siswa')
@section('title', 'Pengumuman Hasil Seleksi')

@section('content')
<div style="max-width: 700px; margin: 0 auto; text-align: center;">

    @if(!$hasil)
        <div class="glass-card" style="padding: 4rem 2rem;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">⏳</div>
            <h2 style="color: var(--dark); margin-bottom: 1rem;">Belum Ada Pengumuman</h2>
            <p style="color: var(--gray-text);">Sistem sedang dalam proses seleksi. Pengumuman kelulusan akan ditampilkan pada halaman ini setelah proses seleksi diselesaikan oleh Panitia PPDB.</p>
            <p style="color: var(--gray-text); margin-top: 1rem;">Status Anda saat ini: <strong style="text-transform: uppercase;">{{ $pendaftaran->status }}</strong></p>
        </div>
    @else
        @if($hasil->status_kelulusan)
            <!-- DITERIMA -->
            <div class="glass-card" style="background: linear-gradient(135deg, #059669, #10b981); color: white; padding: 4rem 2rem;">
                <div style="font-size: 5rem; margin-bottom: 1rem;">🎉</div>
                <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">SELAMAT!</h1>
                <p style="font-size: 1.25rem; opacity: 0.9; margin-bottom: 2rem;">Anda dinyatakan <strong style="font-weight: 800; font-size: 1.5rem;">DITERIMA</strong> di jurusan <strong>{{ $pendaftaran->jurusan->nama }}</strong>.</p>
                
                <div style="background: rgba(255,255,255,0.2); padding: 1.5rem; border-radius: var(--radius-md); display: inline-block; text-align: left;">
                    <p style="margin-bottom: 0.5rem;">Total Skor Akhir: <strong>{{ $hasil->skor_akhir }}</strong></p>
                    <p>Posisi Ranking Peringkat: <strong>#{{ $hasil->ranking }}</strong></p>
                </div>
            </div>
            <div style="margin-top: 2rem;">
                <button class="btn-primary" onclick="window.print()" style="font-size: 1.125rem;">🖨️ Cetak Bukti Kelulusan</button>
            </div>
        @else
            <!-- TIDAK DITERIMA -->
            <div class="glass-card" style="background: linear-gradient(135deg, #dc2626, #ef4444); color: white; padding: 4rem 2rem;">
                <div style="font-size: 5rem; margin-bottom: 1rem;">😔</div>
                <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">MOHON MAAF</h1>
                <p style="font-size: 1.25rem; opacity: 0.9; margin-bottom: 2rem;">Anda dinyatakan <strong style="font-weight: 800; font-size: 1.5rem;">TIDAK DITERIMA</strong> di jurusan <strong>{{ $pendaftaran->jurusan->nama }}</strong> karena kuota telah penuh.</p>
                
                <div style="background: rgba(255,255,255,0.2); padding: 1.5rem; border-radius: var(--radius-md); display: inline-block; text-align: left;">
                    <p style="margin-bottom: 0.5rem;">Total Skor Akhir: <strong>{{ $hasil->skor_akhir }}</strong></p>
                    <p>Posisi Ranking Peringkat: <strong>#{{ $hasil->ranking }}</strong></p>
                </div>
                <p style="margin-top: 2rem; opacity: 0.8;">Tetap semangat dan pantang menyerah!</p>
            </div>
        @endif
    @endif

</div>
@endsection
