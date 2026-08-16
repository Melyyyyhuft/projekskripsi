@extends('layouts.app')
@section('title', 'FAQ (Tanya Jawab) PPDB — SMK Mitra Bintaro')
@section('content')
@php
    $tahunAjaran = $settings['tahun_ajaran'] ?? '2026/2027';
@endphp
<style>
.pub-header{padding:7rem 2rem 3.5rem;background:linear-gradient(135deg,#0f172a,#1e40af);color:white;text-align:center;}
.pub-header h1{font-family:'Outfit',sans-serif;font-size:clamp(2rem,4vw,2.8rem);font-weight:900;margin:0 0 .75rem;letter-spacing:-.5px;}
.pub-header p{opacity:.85;font-size:1.05rem;max-width:540px;margin:0 auto;line-height:1.6;}
.pub-body{max-width:850px;margin:0 auto;padding:4rem 2rem;}
.faq-item{background:white;border:1px solid #e2e8f0;border-radius:14px;margin-bottom:1rem;overflow:hidden;transition:all .25s ease;}
.faq-item:hover{border-color:#cbd5e1;box-shadow:0 4px 10px rgba(0,0,0,.03);}
.faq-trigger{width:100%;padding:1.25rem 1.5rem;text-align:left;background:transparent;border:none;outline:none;font-size:1rem;font-weight:700;color:#0f172a;display:flex;justify-content:space-between;align-items:center;cursor:pointer;gap:1rem;}
.faq-content{padding:0 1.5rem 1.25rem;color:#475569;font-size:.92rem;line-height:1.6;display:none;}
.faq-icon{font-size:.85rem;color:#64748b;transition:transform .25s ease;}
.faq-item.active {border-color:#bfdbfe;box-shadow:0 4px 12px rgba(29,78,216,.05);}
.faq-item.active .faq-trigger{color:#1d4ed8;}
.faq-item.active .faq-icon{transform:rotate(180deg);color:#1d4ed8;}
.faq-item.active .faq-content{display:block;}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.faq-trigger').forEach(trigger => {
        trigger.addEventListener('click', () => {
            const item = trigger.closest('.faq-item');
            const isActive = item.classList.contains('active');
            
            // Close all items
            document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
            
            // Toggle current item
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });
});
</script>

<div class="pub-header">
    <div style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(255,255,255,.15);padding:.4rem 1rem;border-radius:999px;font-size:.8rem;font-weight:700;margin-bottom:1rem;">
        <i class="fa-solid fa-circle-question"></i> FAQ / TANYA JAWAB
    </div>
    <h1>Pertanyaan &amp; Jawaban PPDB</h1>
    <p>Temukan jawaban cepat atas pertanyaan umum seputar proses penerimaan peserta didik baru SMK Mitra Bintaro</p>
</div>

<div class="pub-body">
    <div style="text-align:center;margin-bottom:2.5rem;">
        <h2 style="font-family:'Outfit',sans-serif;font-size:1.5rem;font-weight:800;color:#0f172a;margin:0 0 .25rem;">Pertanyaan Umum</h2>
        <p style="color:#64748b;font-size:.95rem;margin:0;">Klik pada pertanyaan di bawah untuk melihat jawaban detail.</p>
    </div>

    <div>
        {{-- Q1 --}}
        <div class="faq-item active">
            <button type="button" class="faq-trigger">
                <span>Apakah pendaftaran dilakukan secara online?</span>
                <i class="fa-solid fa-chevron-down faq-icon"></i>
            </button>
            <div class="faq-content">
                Ya, seluruh rangkaian pendaftaran PPDB SMK Mitra Bintaro Tahun Pelajaran {{ $tahunAjaran }} dilakukan secara online. Mulai dari pendaftaran akun, melengkapi formulir biodata, mengunggah berkas scan persyaratan, hingga mengerjakan ujian seleksi CBT dilakukan secara mandiri melalui website portal PPDB resmi ini.
            </div>
        </div>

        {{-- Q2 --}}
        <div class="faq-item">
            <button type="button" class="faq-trigger">
                <span>Bagaimana jika tidak memiliki perangkat untuk mendaftar?</span>
                <i class="fa-solid fa-chevron-down faq-icon"></i>
            </button>
            <div class="faq-content">
                Bagi calon siswa atau orang tua/wali yang mengalami kendala teknis (tidak memiliki handphone android, laptop, atau kendala jaringan internet) untuk mendaftar online, dipersilakan untuk datang langsung ke <strong>Ruang PPDB</strong> di gedung sekolah SMK Mitra Bintaro. Petugas panitia kami akan mendampingi dan memandu pengisian pendaftaran online Anda hingga selesai. 
                <br><br>
                <em>Harap dicatat: kedatangan fisik ke sekolah merupakan bentuk layanan pendampingan bantuan pendaftaran online, bukan jalur pendaftaran offline mandiri yang terpisah.</em>
            </div>
        </div>

        {{-- Q3 --}}
        <div class="faq-item">
            <button type="button" class="faq-trigger">
                <span>Bagaimana jika berkas dokumen pendaftaran belum lengkap?</span>
                <i class="fa-solid fa-chevron-down faq-icon"></i>
            </button>
            <div class="faq-content">
                Anda diperbolehkan untuk membuat akun dan melakukan pengisian biodata serta pemilihan jurusan terlebih dahulu. Kekurangan dokumen berkas persyaratan dapat diunggah menyusul di dashboard siswa sebelum batas akhir tanggal penutupan pendaftaran PPDB berjalan.
            </div>
        </div>

        {{-- Q4 --}}
        <div class="faq-item">
            <button type="button" class="faq-trigger">
                <span>Kapan calon siswa dapat mengikuti ujian CBT seleksi?</span>
                <i class="fa-solid fa-chevron-down faq-icon"></i>
            </button>
            <div class="faq-content">
                Ujian seleksi berbasis CBT dapat diikuti setelah berkas dokumen pendaftaran yang diunggah dinyatakan valid dan disetujui oleh tim verifikasi admin sekolah. Setelah disetujui, tombol <strong>Mulai Ujian</strong> akan aktif secara otomatis pada menu Ujian di dalam dashboard siswa.
            </div>
        </div>

        {{-- Q5 --}}
        <div class="faq-item">
            <button type="button" class="faq-trigger">
                <span>Bagaimana jika kuota jurusan pilihan sudah penuh?</span>
                <i class="fa-solid fa-chevron-down faq-icon"></i>
            </button>
            <div class="faq-content">
                Jika daya tampung kuota pada salah satu jurusan pilihan utama sudah penuh, calon siswa disarankan untuk mendaftar pada program keahlian kejuruan alternatif lain yang masih membuka kuota pendaftaran. Calon siswa juga dipersilakan melakukan konsultasi minat bakat dengan tim guru bimbingan PPDB kami di sekolah untuk alternatif terbaik.
            </div>
        </div>

        {{-- Q6 --}}
        <div class="faq-item">
            <button type="button" class="faq-trigger">
                <span>Bagaimana cara melakukan pembayaran biaya pendaftaran?</span>
                <i class="fa-solid fa-chevron-down faq-icon"></i>
            </button>
            <div class="faq-content">
                Pembayaran administrasi PPDB dapat diselesaikan secara transfer melalui nomor virtual account Bank Mandiri atau rekening Bank BCA resmi yang tertera pada <a href="{{ route('public.biaya') }}" style="color:#1d4ed8;font-weight:700;">halaman biaya</a>. Setelah transfer, bukti transfer wajib difoto dan diunggah di dashboard siswa. Alternatif lain adalah pembayaran tunai secara langsung di loket keuangan PPDB sekolah.
            </div>
        </div>
    </div>

    {{-- Info Card Helpdesk --}}
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:18px;padding:2rem;margin-top:3rem;text-align:center;">
        <h3 style="font-size:1.1rem;font-weight:800;color:#1e40af;margin:0 0 .5rem;">Punya pertanyaan lain yang belum terjawab?</h3>
        <p style="color:#64748b;font-size:.9rem;line-height:1.5;margin:0 0 1.25rem;">Tim Helpdesk panitia PPDB kami siap membantu menjawab semua keraguan dan membimbing pendaftaran Anda.</p>
        <a href="{{ route('public.kontak') }}" style="background:#1d4ed8;color:white;padding:.65rem 1.75rem;border-radius:10px;font-weight:700;font-size:.88rem;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;">
            <i class="fa-solid fa-comments"></i> Hubungi Panitia PPDB
        </a>
    </div>
</div>
@endsection
