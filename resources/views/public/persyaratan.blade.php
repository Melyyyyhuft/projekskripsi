@extends('layouts.app')
@section('title', 'Persyaratan PPDB — SMK Mitra Bintaro')
@section('content')
@php
    $tahunAjaran = $settings['tahun_ajaran'] ?? '2026/2027';
@endphp
<style>
.pub-header{padding:7rem 2rem 3.5rem;background:linear-gradient(135deg,#0f172a,#1e40af);color:white;text-align:center;}
.pub-header h1{font-family:'Outfit',sans-serif;font-size:clamp(2rem,4vw,2.8rem);font-weight:900;margin:0 0 .75rem;letter-spacing:-.5px;}
.pub-header p{opacity:.85;font-size:1.05rem;max-width:540px;margin:0 auto;line-height:1.6;}
.pub-body{max-width:900px;margin:0 auto;padding:4rem 2rem;}
.info-card{background:white;border:1px solid #e2e8f0;border-radius:18px;padding:2rem;box-shadow:0 4px 6px -1px rgba(0,0,0,.05),0 2px 4px -2px rgba(0,0,0,.05);margin-bottom:2rem;}
.req-list{display:flex;flex-direction:column;gap:1rem;margin:1.5rem 0;}
.req-item{display:flex;gap:1rem;align-items:flex-start;}
.req-icon{width:28px;height:28px;border-radius:50%;background:#eff6ff;color:#1d4ed8;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;margin-top:.1rem;}
.req-text{font-size:.95rem;color:#334155;line-height:1.5;}
.req-text strong{color:#0f172a;}
</style>

<div class="pub-header">
    <div style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(255,255,255,.15);padding:.4rem 1rem;border-radius:999px;font-size:.8rem;font-weight:700;margin-bottom:1rem;">
        <i class="fa-solid fa-file-shield"></i> BERKAS &amp; SYARAT
    </div>
    <h1>Persyaratan Pendaftaran PPDB</h1>
    <p>Informasi berkas dokumen wajib dan ketentuan pendaftaran untuk calon siswa baru Tahun Ajaran {{ $tahunAjaran }}</p>
</div>

<div class="pub-body">
    {{-- Dokumen Wajib Card --}}
    <div class="info-card">
        <h2 style="font-family:'Outfit',sans-serif;font-size:1.4rem;font-weight:800;color:#0f172a;margin:0 0 .5rem;display:flex;align-items:center;gap:.6rem;">
            <i class="fa-solid fa-folder-open" style="color:#1d4ed8;"></i> Dokumen Wajib (Softcopy/Scan)
        </h2>
        <p style="color:#64748b;font-size:.9rem;margin:0;">Dokumen berikut wajib di-scan/foto dengan jelas dan diunggah melalui dashboard pendaftaran siswa setelah membuat akun:</p>
        
        <div class="req-list">
            <div class="req-item">
                <div class="req-icon"><i class="fa-solid fa-check"></i></div>
                <div class="req-text"><strong>Kartu Keluarga (KK):</strong> Scan/foto KK asli terbaru untuk validasi data kependudukan dan alamat tinggal.</div>
            </div>
            <div class="req-item">
                <div class="req-icon"><i class="fa-solid fa-check"></i></div>
                <div class="req-text"><strong>Akta Kelahiran:</strong> Scan/foto Akta Kelahiran asli calon siswa baru.</div>
            </div>
            <div class="req-item">
                <div class="req-icon"><i class="fa-solid fa-check"></i></div>
                <div class="req-text"><strong>Ijazah SMP/MTs / Surat Keterangan Lulus (SKL):</strong> Surat Keterangan Lulus dari sekolah asal jika ijazah asli belum terbit.</div>
            </div>
            <div class="req-item">
                <div class="req-icon"><i class="fa-solid fa-check"></i></div>
                <div class="req-text"><strong>Kartu NISN:</strong> Tangkapan layar (screenshot) profil NISN aktif dari web Kemendikbud atau surat keterangan NISN dari sekolah asal.</div>
            </div>
            <div class="req-item">
                <div class="req-icon"><i class="fa-solid fa-check"></i></div>
                <div class="req-text"><strong>Pas Foto Terbaru:</strong> Foto formal terbaru ukuran 3x4 (berwarna, latar belakang bebas/merah/biru).</div>
            </div>
        </div>
    </div>

    {{-- Ketentuan Pendaftaran Card --}}
    <div class="info-card">
        <h2 style="font-family:'Outfit',sans-serif;font-size:1.4rem;font-weight:800;color:#0f172a;margin:0 0 .5rem;display:flex;align-items:center;gap:.6rem;">
            <i class="fa-solid fa-circle-exclamation" style="color:#1d4ed8;"></i> Ketentuan &amp; Kualifikasi
        </h2>
        <p style="color:#64748b;font-size:.9rem;margin:0 0 1.25rem;">Persyaratan umum calon peserta didik baru SMK Mitra Bintaro:</p>
        <ul style="padding-left:1.25rem;margin:0;display:flex;flex-direction:column;gap:.75rem;font-size:.92rem;color:#475569;line-height:1.55;">
            <li>Merupakan lulusan SMP, MTs, Paket B, atau bentuk lain yang sederajat.</li>
            <li>Usia maksimal 21 tahun pada saat awal tahun pelajaran baru berjalan.</li>
            <li>Sehat jasmani dan rohani, serta tidak memiliki cacat fisik yang dapat mengganggu kelancaran proses pembelajaran pada jurusan yang dipilih.</li>
            <li>Bersedia mematuhi segala tata tertib dan peraturan sekolah yang berlaku di SMK Mitra Bintaro.</li>
        </ul>
    </div>

    {{-- Kendala Dokumen Card --}}
    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:18px;padding:2rem;box-shadow:0 2px 4px rgba(0,0,0,.02);">
        <h3 style="font-family:'Outfit',sans-serif;font-size:1.2rem;font-weight:800;color:#92400e;margin:0 0 .5rem;display:flex;align-items:center;gap:.5rem;">
            <i class="fa-solid fa-triangle-exclamation" style="color:#d97706;"></i> Mengalami Kendala Dokumen?
        </h3>
        <p style="color:#78350f;font-size:.9rem;line-height:1.6;margin:0;">
            Bagi calon siswa baru yang memiliki kendala dalam melengkapi dokumen persyaratan (misal: kartu keluarga sedang diurus, ijazah/SKL belum dibagikan dari sekolah asal, dokumen rusak, atau kendala administrasi lainnya), Anda <strong>tetap dapat melakukan pendaftaran online terlebih dahulu</strong>.
        </p>
        <p style="color:#78350f;font-size:.9rem;line-height:1.6;margin:.75rem 0 0;">
            Silakan mendaftar online menggunakan dokumen sementara yang ada (seperti Surat Keterangan Lulus atau Surat Keterangan Siswa Aktif) dan segera hubungi Layanan Bantuan PPDB sekolah untuk bantuan verifikasi dokumen manual oleh petugas kami.
        </p>
    </div>
</div>
@endsection
