@extends('layouts.app')
@section('title', 'Hubungi Kami — PPDB SMK Mitra Bintaro')
@section('content')
@php
    $linkWa = $settings['link_wa'] ?? 'https://wa.me/628119999999';
    $noTelp = $settings['no_telp'] ?? '021-5555678';
    $emailSchool = $settings['email'] ?? 'info@smkmitrabintaro.sch.id';
@endphp
<style>
.pub-header{padding:7rem 2rem 3.5rem;background:linear-gradient(135deg,#0f172a,#1e40af);color:white;text-align:center;}
.pub-header h1{font-family:'Outfit',sans-serif;font-size:clamp(2rem,4vw,2.8rem);font-weight:900;margin:0 0 .75rem;letter-spacing:-.5px;}
.pub-header p{opacity:.85;font-size:1.05rem;max-width:540px;margin:0 auto;line-height:1.6;}
.pub-body{max-width:1100px;margin:0 auto;padding:4rem 2rem;}
.grid-2{display:grid;grid-template-columns:1.2fr 1fr;gap:2.5rem;}
.info-card{background:white;border:1px solid #e2e8f0;border-radius:18px;padding:2rem;box-shadow:0 4px 6px -1px rgba(0,0,0,.05);}
.contact-item{display:flex;gap:1.25rem;align-items:flex-start;margin-bottom:1.5rem;}
.contact-icon{width:46px;height:46px;border-radius:12px;background:#eff6ff;color:#1d4ed8;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;}
.contact-details h4{font-size:.95rem;font-weight:800;color:#0f172a;margin:0 0 .25rem;}
.contact-details p{font-size:.88rem;color:#475569;margin:0;line-height:1.5;}
.map-wrap{border-radius:18px;overflow:hidden;border:1px solid #e2e8f0;height:350px;box-shadow:0 4px 6px -1px rgba(0,0,0,.05);margin-top:1.5rem;}
@media(max-width:768px){
    .grid-2{grid-template-columns:1fr;gap:2rem;}
}
</style>

<div class="pub-header">
    <div style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(255,255,255,.15);padding:.4rem 1rem;border-radius:999px;font-size:.8rem;font-weight:700;margin-bottom:1rem;">
        <i class="fa-solid fa-headset"></i> LAYANAN BANTUAN
    </div>
    <h1>Hubungi Panitia PPDB</h1>
    <p>Layanan helpdesk bantuan pendaftaran online resmi dan informasi alamat lokasi SMK Mitra Bintaro</p>
</div>

<div class="pub-body">
    <div class="grid-2">
        {{-- Kolom Kiri: Info Kontak --}}
        <div>
            <div class="info-card">
                <h2 style="font-family:'Outfit',sans-serif;font-size:1.4rem;font-weight:800;color:#0f172a;margin:0 0 1.5rem;display:flex;align-items:center;gap:.5rem;">
                    <i class="fa-solid fa-comments" style="color:#1d4ed8;"></i> Hubungi Layanan Daring
                </h2>

                <div class="contact-item">
                    <div class="contact-icon"><i class="fa-brands fa-whatsapp"></i></div>
                    <div class="contact-details">
                        <h4>Chat WhatsApp Panitia</h4>
                        <p>Konsultasi chat fast-response dengan panitia PPDB:</p>
                        <a href="{{ $linkWa }}" target="_blank" style="color:#10b981;font-weight:700;text-decoration:none;font-size:.9rem;display:inline-block;margin-top:.25rem;">
                            Hubungi via WhatsApp →
                        </a>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon"><i class="fa-solid fa-phone"></i></div>
                    <div class="contact-details">
                        <h4>Nomor Telepon Sekolah</h4>
                        <p>Hubungi bagian administrasi kantor sekolah (Jam Kerja):</p>
                        <p style="font-weight:700;color:#0f172a;margin-top:.2rem;">{{ $noTelp }}</p>
                    </div>
                </div>

                <div class="contact-item" style="margin-bottom:0;">
                    <div class="contact-icon"><i class="fa-solid fa-envelope"></i></div>
                    <div class="contact-details">
                        <h4>Alamat Surat Elektronik (Email)</h4>
                        <p>Kirimkan pertanyaan atau aduan kendala teknis ke email resmi:</p>
                        <p style="font-weight:700;color:#0f172a;margin-top:.2rem;">{{ $emailSchool }}</p>
                    </div>
                </div>
            </div>

            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:18px;padding:2rem;margin-top:1.5rem;">
                <h3 style="font-family:'Outfit',sans-serif;font-size:1.15rem;font-weight:800;color:#1e40af;margin:0 0 .5rem;display:flex;align-items:center;gap:.5rem;">
                    <i class="fa-solid fa-circle-info"></i> Layanan Bantuan PPDB Online
                </h3>
                <p style="color:#1e40af;font-size:.88rem;line-height:1.6;margin:0;">
                    PPDB dilakukan secara online. Bagi calon siswa atau orang tua yang mengalami kendala dalam proses pendaftaran, dapat datang langsung ke <strong>Ruang PPDB Gedung Utama Lantai 1</strong> untuk mendapatkan bantuan pengisian formulir atau verifikasi berkas oleh petugas panitia.
                </p>
                <p style="color:#1e40af;font-size:.88rem;line-height:1.6;margin:.5rem 0 0;font-weight:700;">
                    Kedatangan ke sekolah adalah murni layanan bantuan pendampingan pendaftaran online, bukan jalur pendaftaran offline mandiri yang berbeda.
                </p>
            </div>
        </div>

        {{-- Kolom Kanan: Lokasi & Maps --}}
        <div>
            <div class="info-card" style="height:100%;box-sizing:border-box;display:flex;flex-direction:column;">
                <h2 style="font-family:'Outfit',sans-serif;font-size:1.4rem;font-weight:800;color:#0f172a;margin:0 0 1.25rem;display:flex;align-items:center;gap:.5rem;">
                    <i class="fa-solid fa-location-dot" style="color:#1d4ed8;"></i> Alamat &amp; Lokasi Sekolah
                </h2>
                
                <p style="font-size:.9rem;color:#475569;line-height:1.6;margin:0 0 1.25rem;">
                    <strong>SMK Mitra Bintaro (Gedung Baru)</strong><br>
                    Jl. Sultan Ageng Tirtayasa No.6, RT.007/RW.008, Kunciran Indah, Kec. Pinang, Kota Tangerang, Banten 15144
                </p>

                <div class="map-wrap" style="flex:1;">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.3151952825665!2d106.6824302!3d-6.2221044999999995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69fb3a946bee63%3A0x7d966024c6903b4b!2sSMK%20Mitra%20Bintaro%20(Gedung%20Baru)!5e0!3m2!1sid!2sid!4v1778407953835!5m2!1sid!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
