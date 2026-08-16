@extends('layouts.app')
@section('title', 'Daftar Jurusan & Kuota — SMK Mitra Bintaro')
@section('content')
@php
    $tahunAjaran = $settings['tahun_ajaran'] ?? '2026/2027';
@endphp
<style>
.pub-header{padding:7rem 2rem 3.5rem;background:linear-gradient(135deg,#0f172a,#1e40af);color:white;text-align:center;}
.pub-header h1{font-family:'Outfit',sans-serif;font-size:clamp(2rem,4vw,2.8rem);font-weight:900;margin:0 0 .75rem;letter-spacing:-.5px;}
.pub-header p{opacity:.85;font-size:1.05rem;max-width:540px;margin:0 auto;line-height:1.6;}
.pub-body{max-width:1100px;margin:0 auto;padding:4rem 2rem;}
.jurusan-card{background:white;border:1px solid #e2e8f0;border-radius:20px;padding:2rem;box-shadow:0 4px 6px -1px rgba(0,0,0,.05);display:grid;grid-template-columns:auto 1fr auto;gap:2rem;align-items:center;margin-bottom:1.75rem;transition:all .3s ease;}
.jurusan-card:hover{transform:translateY(-4px);box-shadow:0 12px 20px -5px rgba(0,0,0,.08);border-color:#cbd5e1;}
.jurusan-icon{width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,#1d4ed8,#2563eb);color:white;display:flex;align-items:center;justify-content:center;font-size:2rem;}
.stat-box{text-align:center;background:#f8fafc;border:1px solid #f1f5f9;border-radius:12px;padding:.75rem 1.25rem;min-width:110px;}
.stat-num{font-size:1.25rem;font-weight:900;color:#0f172a;}
.stat-label{font-size:.72rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-top:.15rem;}
@media(max-width:768px){
    .jurusan-card{grid-template-columns:1fr;gap:1.25rem;text-align:center;padding:1.5rem;}
    .jurusan-icon{margin:0 auto;}
    .stats-row{display:flex;justify-content:center;gap:.75rem;flex-wrap:wrap;}
}
</style>

<div class="pub-header">
    <div style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(255,255,255,.15);padding:.4rem 1rem;border-radius:999px;font-size:.8rem;font-weight:700;margin-bottom:1rem;">
        <i class="fa-solid fa-graduation-cap"></i> JURUSAN &amp; KUOTA PPDB
    </div>
    <h1>Program Keahlian &amp; Kuota</h1>
    <p>Informasi daya tampung kuota, jumlah pendaftar, dan sisa kuota untuk setiap program kejuruan Tahun Ajaran {{ $tahunAjaran }}</p>
</div>

<div class="pub-body">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <h2 style="font-family:'Outfit',sans-serif;font-size:1.4rem;font-weight:800;color:#0f172a;margin:0 0 .25rem;">Daftar Jurusan Tersedia</h2>
            <p style="color:#64748b;font-size:.9rem;margin:0;">Pilih kompetensi keahlian yang sesuai dengan bakat dan karir impian Anda.</p>
        </div>
        <div style="font-size:.85rem;color:#475569;background:#f1f5f9;padding:.5rem 1rem;border-radius:8px;font-weight:600;display:inline-flex;align-items:center;gap:.4rem;">
            <i class="fa-solid fa-arrows-rotate"></i> Pembaruan Real-Time dari Database
        </div>
    </div>

    <div>
        @forelse($jurusans as $jurusan)
        @php
            $kuota = $jurusan->kuota ?? 0;
            $pendaftar = $jurusan->pendaftar_count ?? 0;
            $sisa = $jurusan->sisa_kuota ?? 0;
            
            $isFull = $sisa <= 0;
            $persen = $kuota > 0 ? min(100, round((($kuota-$sisa)/$kuota)*100)) : 0;
            $color = $isFull ? '#ef4444' : ($sisa <= 15 ? '#f59e0b' : '#10b981');
            $statusLabel = $isFull ? 'Kuota Penuh' : ($sisa <= 15 ? 'Sisa Sedikit' : 'Tersedia');
        @endphp
        <div class="jurusan-card">
            <div class="jurusan-icon">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            
            <div>
                <div style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap;margin-bottom:.5rem;justify-content:inherit;">
                    <h3 style="font-size:1.25rem;font-weight:800;color:#0f172a;margin:0;">{{ $jurusan->nama }}</h3>
                    <span style="font-size:0.75rem;font-weight:700;padding:0.25rem 0.65rem;border-radius:999px;background:{{ $isFull ? '#fee2e2' : ($sisa <= 15 ? '#fef3c7' : '#ecfdf5') }};color:{{ $isFull ? '#991b1b' : ($sisa <= 15 ? '#92400e' : '#065f46') }};">
                        {{ $statusLabel }}
                    </span>
                </div>
                
                <p style="color:#64748b;font-size:.9rem;line-height:1.5;margin:0 0 1rem;max-width:550px;">
                    Membekali siswa dengan keahlian teknis terstandarisasi industri, dibimbing oleh praktisi ahli dan kurikulum modern siap kerja.
                </p>

                {{-- Progress Bar --}}
                <div style="max-width:400px;margin:0 auto 0 0;">
                    <div style="display:flex;justify-content:space-between;font-size:0.75rem;color:#64748b;margin-bottom:.35rem;">
                        <span>Persentase Terisi</span>
                        <span>{{ $persen }}%</span>
                    </div>
                    <div style="width:100%;height:6px;background:#e2e8f0;border-radius:999px;overflow:hidden;">
                        <div style="height:100%;border-radius:999px;background:{{ $color }};width:{{ $persen }}%;transition:width .6s ease;"></div>
                    </div>
                </div>
            </div>

            <div class="stats-row" style="display:flex;gap:.75rem;align-self:center;">
                <div class="stat-box">
                    <div class="stat-num">{{ $kuota }}</div>
                    <div class="stat-label">Total Kuota</div>
                </div>
                <div class="stat-box">
                    <div class="stat-num" style="color:#1d4ed8;">{{ $pendaftar }}</div>
                    <div class="stat-label">Pendaftar</div>
                </div>
                <div class="stat-box" style="border-color:{{ $isFull ? '#fca5a5' : '#bbf7d0' }};background:{{ $isFull ? '#fef2f2' : '#f0fdf4' }};">
                    <div class="stat-num" style="color:{{ $color }};">{{ $sisa }}</div>
                    <div class="stat-label">Sisa Kuota</div>
                </div>
            </div>
        </div>
        @empty
        <div style="background:white;border:1px solid #e2e8f0;border-radius:18px;padding:4rem 2rem;text-align:center;">
            <i class="fa-solid fa-inbox" style="font-size:3rem;color:#cbd5e1;margin-bottom:1rem;"></i>
            <h3 style="font-size:1.15rem;font-weight:700;color:#64748b;margin:0;">Belum ada data jurusan yang tersedia saat ini.</h3>
        </div>
        @endforelse
    </div>

    {{-- Info Bantuan Ringkas --}}
    <div style="margin-top:3rem;background:#eff6ff;border:1px solid #bfdbfe;border-radius:18px;padding:2rem;display:flex;gap:1.5rem;align-items:center;flex-wrap:wrap;">
        <div style="width:48px;height:48px;background:#1d4ed8;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.35rem;flex-shrink:0;">
            <i class="fa-solid fa-circle-question"></i>
        </div>
        <div style="flex:1;">
            <h3 style="font-size:1.05rem;font-weight:800;color:#1e40af;margin:0 0 .3rem;">Bagaimana jika kuota jurusan pilihan Anda sudah penuh?</h3>
            <p style="color:#1e40af;font-size:.88rem;line-height:1.5;margin:0;">
                Anda masih dapat mendaftar pada jurusan alternatif lain yang masih tersedia kuotanya. Selain itu, Anda dapat berkonsultasi langsung dengan petugas Layanan PPDB kami di sekolah untuk rekomendasi program keahlian terbaik.
            </p>
        </div>
    </div>
</div>
@endsection
