@extends('layouts.app')
@section('title', 'Biaya & Metode Pembayaran PPDB — SMK Mitra Bintaro')
@section('content')
@php
    $tahunAjaran = $settings['tahun_ajaran'] ?? '2026/2027';
@endphp
<style>
.pub-header{padding:7rem 2rem 3.5rem;background:linear-gradient(135deg,#0f172a,#1e40af);color:white;text-align:center;}
.pub-header h1{font-family:'Outfit',sans-serif;font-size:clamp(2rem,4vw,2.8rem);font-weight:900;margin:0 0 .75rem;letter-spacing:-.5px;}
.pub-header p{opacity:.85;font-size:1.05rem;max-width:540px;margin:0 auto;line-height:1.6;}
.pub-body{max-width:900px;margin:0 auto;padding:4rem 2rem;}
.info-card{background:white;border:1px solid #e2e8f0;border-radius:18px;padding:2rem;box-shadow:0 4px 6px -1px rgba(0,0,0,.05);margin-bottom:2rem;}
.fee-table {width:100%;border-collapse:collapse;margin:1.5rem 0;}
.fee-table th {text-align:left;padding:1rem;background:#f8fafc;color:#0f172a;font-weight:800;font-size:.9rem;border-bottom:2px solid #e2e8f0;}
.fee-table td {padding:1rem;color:#475569;font-size:.92rem;border-bottom:1px solid #f1f5f9;}
.bank-card {border:1px solid #e2e8f0;border-radius:12px;padding:1.25rem;display:flex;align-items:center;gap:1.5rem;margin-bottom:1rem;}
.bank-logo {width:70px;font-weight:900;font-size:1.35rem;color:#1e3a8a;font-family:'Outfit',sans-serif;text-align:center;}
.bank-details h4 {font-size:1rem;font-weight:800;color:#0f172a;margin:0 0 .25rem;}
.bank-details p {font-size:.85rem;color:#64748b;margin:0;}
</style>

<div class="pub-header">
    <div style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(255,255,255,.15);padding:.4rem 1rem;border-radius:999px;font-size:.8rem;font-weight:700;margin-bottom:1rem;">
        <i class="fa-solid fa-credit-card"></i> BIAYA &amp; PEMBAYARAN
    </div>
    <h1>Informasi Biaya &amp; Pembayaran</h1>
    <p>Rincian investasi pendidikan calon peserta didik baru dan metode pembayaran resmi SMK Mitra Bintaro</p>
</div>

<div class="pub-body">
    {{-- Biaya Pendaftaran Card --}}
    <div class="info-card">
        <h2 style="font-family:'Outfit',sans-serif;font-size:1.35rem;font-weight:800;color:#0f172a;margin:0 0 .5rem;display:flex;align-items:center;gap:.5rem;">
            <i class="fa-solid fa-money-check-dollar" style="color:#1d4ed8;"></i> 1. Biaya Formulir Pendaftaran
        </h2>
        <p style="color:#64748b;font-size:.9rem;margin:0;">Biaya awal untuk pendaftaran akun dan keikutsertaan seleksi ujian CBT:</p>
        
        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:1.25rem;margin-top:1.25rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
            <div>
                <span style="font-size:.78rem;font-weight:700;color:#1e40af;text-transform:uppercase;letter-spacing:.5px;">Biaya Registrasi + Ujian CBT</span>
                <div style="font-size:1.5rem;font-weight:900;color:#1e3a8a;margin-top:.15rem;">Rp 150.000</div>
            </div>
            <div style="font-size:.8rem;color:#1e40af;line-height:1.5;max-width:320px;">
                *Biaya ini berlaku untuk semua pilihan program keahlian/jurusan dan sudah termasuk kartu peserta ujian online.
            </div>
        </div>
    </div>

    {{-- Investasi Pendidikan Daftar Ulang --}}
    <div class="info-card">
        <h2 style="font-family:'Outfit',sans-serif;font-size:1.35rem;font-weight:800;color:#0f172a;margin:0 0 .5rem;display:flex;align-items:center;gap:.5rem;">
            <i class="fa-solid fa-file-invoice-dollar" style="color:#1d4ed8;"></i> 2. Rincian Biaya Daftar Ulang (Setelah Diterima)
        </h2>
        <p style="color:#64748b;font-size:.9rem;margin:0;">Rincian perkiraan investasi pendidikan saat daftar ulang siswa baru Tahun Pelajaran {{ $tahunAjaran }}:</p>
        
        <table class="fee-table">
            <thead>
                <tr>
                    <th>Komponen Biaya</th>
                    <th>Rincian Biaya</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Uang Pangkal &amp; Gedung</strong></td>
                    <td style="font-weight:700;color:#0f172a;">Rp 1.500.000</td>
                    <td>Sekali bayar selama bersekolah. Bisa diangsur.</td>
                </tr>
                <tr>
                    <td><strong>Seragam Sekolah (5 Setel)</strong></td>
                    <td style="font-weight:700;color:#0f172a;">Rp 950.000</td>
                    <td>Termasuk atribut lengkap sekolah dan baju olahraga.</td>
                </tr>
                <tr>
                    <td><strong>SPP Bulanan (Juli)</strong></td>
                    <td style="font-weight:700;color:#0f172a;">Rp 300.000</td>
                    <td>Iuran SPP bulan pertama awal semester ganjil.</td>
                </tr>
                <tr>
                    <td><strong>Asuransi &amp; Kartu Pelajar</strong></td>
                    <td style="font-weight:700;color:#0f172a;">Rp 100.000</td>
                    <td>Berlaku selama satu tahun pelajaran berjalan.</td>
                </tr>
                <tr style="background:#f8fafc;font-weight:800;">
                    <td style="color:#0f172a;">Total Estimasi</td>
                    <td style="color:#1d4ed8;font-size:1.1rem;">Rp 2.850.000</td>
                    <td style="color:#1d4ed8;">*Dapat diangsur sesuai ketentuan sekolah</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Metode Pembayaran Card --}}
    <div class="info-card">
        <h2 style="font-family:'Outfit',sans-serif;font-size:1.35rem;font-weight:800;color:#0f172a;margin:0 0 .5rem;display:flex;align-items:center;gap:.5rem;">
            <i class="fa-solid fa-wallet" style="color:#1d4ed8;"></i> 3. Metode &amp; Prosedur Pembayaran
        </h2>
        <p style="color:#64748b;font-size:.9rem;margin:0 0 1.5rem;">Pembayaran pendaftaran atau daftar ulang dapat dilakukan melalui opsi berikut:</p>

        {{-- Bank Mandiri --}}
        <div class="bank-card">
            <div class="bank-logo" style="color:#004f9f;">MANDIRI</div>
            <div class="bank-details">
                <h4>Bank Mandiri (Transfer VA)</h4>
                <p>Nomor Rekening: <strong>123-000-987-6543</strong></p>
                <p>Atas Nama: <strong>SMK MITRA BINTARO PPDB</strong></p>
            </div>
        </div>

        {{-- Bank BCA --}}
        <div class="bank-card">
            <div class="bank-logo" style="color:#00569c;">BCA</div>
            <div class="bank-details">
                <h4>Bank BCA (Transfer Bank)</h4>
                <p>Nomor Rekening: <strong>543-210-9876</strong></p>
                <p>Atas Nama: <strong>YAYASAN MITRA BINTARO</strong></p>
            </div>
        </div>

        {{-- Cash di Ruang PPDB --}}
        <div class="bank-card">
            <div class="bank-logo" style="color:#059669;"><i class="fa-solid fa-building-columns"></i> CASH</div>
            <div class="bank-details">
                <h4>Tunai di Loket PPDB Sekolah</h4>
                <p>Melalui kasir keuangan di <strong>Gedung Utama Lantai 1 SMK Mitra Bintaro</strong></p>
                <p>Waktu layanan: Senin - Jumat (08.00 - 15.00 WIB) &amp; Sabtu (08.00 - 12.00 WIB)</p>
            </div>
        </div>

        <div style="margin-top:1.5rem;background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:1.25rem;font-size:.88rem;color:#78350f;line-height:1.6;">
            <i class="fa-solid fa-circle-check" style="margin-right:.35rem;"></i>
            <strong>Penting:</strong> Setelah melakukan transfer, harap unggah bukti transfer pembayaran di dashboard siswa untuk diverifikasi oleh admin panitia agar status pendaftaran Anda segera diperbarui.
        </div>
    </div>
</div>
@endsection
