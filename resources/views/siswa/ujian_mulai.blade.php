@extends('layouts.siswa')
@section('title', 'Mulai Ujian CBT')

@section('content')
<div class="glass-card" style="max-width: 600px; margin: 2rem auto; text-align: center; padding: 3rem 2rem;">
    <div style="font-size: 4rem; margin-bottom: 1rem;">⏱️</div>
    <h2 style="color: var(--primary); margin-bottom: 1rem;">Siap Memulai Ujian?</h2>
    
    <p style="color: var(--gray-text); margin-bottom: 2rem; line-height: 1.6;">
        Anda akan mengerjakan ujian <strong>{{ $ujian->judul }}</strong>.<br>
        Waktu ujian adalah <strong>{{ $ujian->durasi_menit }} menit</strong>.<br>
        Waktu akan mulai berjalan saat Anda menekan tombol di bawah. Pastikan koneksi internet Anda stabil.
    </p>

    <div style="background: #fff8f1; border-left: 4px solid #f59e0b; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 2rem; text-align: left; font-size: 0.9rem;">
        <strong>Perhatian:</strong> Jangan menutup browser atau me-refresh halaman selama ujian berlangsung untuk menghindari masalah pada pengiriman jawaban. Jika waktu habis, jawaban akan dikirimkan otomatis.
    </div>

    <form action="{{ route('siswa.ujian.mulai') }}" method="POST">
        @csrf
        <button type="submit" class="btn-primary" style="font-size: 1.1rem; padding: 1rem 2rem; width: 100%;">
            🚀 Mulai Ujian Sekarang
        </button>
    </form>
</div>
@endsection
