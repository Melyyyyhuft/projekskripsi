@extends('layouts.app')
@section('title', 'Alur Pendaftaran PPDB — SMK Mitra Bintaro')
@section('content')
@php
    $tahunAjaran = $settings['tahun_ajaran'] ?? '2026/2027';
@endphp
<style>
.pub-header{padding:7rem 2rem 3.5rem;background:linear-gradient(135deg,#0f172a,#1e40af);color:white;text-align:center;}
.pub-header h1{font-family:'Outfit',sans-serif;font-size:clamp(2rem,4vw,2.8rem);font-weight:900;margin:0 0 .75rem;letter-spacing:-.5px;}
.pub-header p{opacity:.85;font-size:1.05rem;max-width:540px;margin:0 auto;line-height:1.6;}
.pub-body{max-width:900px;margin:0 auto;padding:4rem 2rem;}
.step-card {background:white;border:1px solid #e2e8f0;border-radius:18px;padding:2rem;box-shadow:0 4px 6px -1px rgba(0,0,0,.05);display:flex;gap:2rem;margin-bottom:2rem;position:relative;}
.step-badge {width:50px;height:50px;border-radius:50%;background:#eff6ff;color:#1d4ed8;border:2px solid #dbeafe;display:flex;align-items:center;justify-content:center;font-size:1.35rem;font-weight:900;flex-shrink:0;z-index:2;}
.step-content h3 {font-size:1.2rem;font-weight:800;color:#0f172a;margin:0 0 .5rem;}
.step-content p {font-size:.92rem;color:#475569;line-height:1.6;margin:0;}
.step-content ul {margin:1rem 0 0;padding-left:1.25rem;display:flex;flex-direction:column;gap:.5rem;font-size:.88rem;color:#64748b;}
@media(max-width:640px){
    .step-card {flex-direction:column;gap:1rem;padding:1.5rem;}
}
</style>

<div class="pub-header">
    <div style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(255,255,255,.15);padding:.4rem 1rem;border-radius:999px;font-size:.8rem;font-weight:700;margin-bottom:1rem;">
        <i class="fa-solid fa-route"></i> ALUR PENDAFTARAN
    </div>
    <h1>Alur Pendaftaran PPDB</h1>
    <p>Prosedur pendaftaran calon siswa baru dari awal pembuatan akun hingga diterima resmi di SMK Mitra Bintaro</p>
</div>

<div class="pub-body">
    <div style="text-align:center;margin-bottom:3rem;">
        <h2 style="font-family:'Outfit',sans-serif;font-size:1.5rem;font-weight:800;color:#0f172a;margin:0 0 .25rem;">4 Tahap Mudah Menjadi Siswa Baru</h2>
        <p style="color:#64748b;font-size:.95rem;margin:0;">Prosedur pendaftaran dilakukan secara online demi kenyamanan dan efisiensi waktu.</p>
    </div>

    <div>
        {{-- Step 1 --}}
        <div class="step-card">
            <div class="step-badge">1</div>
            <div class="step-content">
                <h3>Membuat Akun &amp; Registrasi</h3>
                <p>
                    Calon siswa baru mendaftarkan akun di portal PPDB sekolah dengan menginputkan nama lengkap, email aktif, nomor handphone, dan password.
                </p>
                <ul>
                    <li>Akses menu <strong>Daftar</strong> pada navbar atas.</li>
                    <li>Simpan alamat email dan password Anda untuk login kembali sewaktu-waktu.</li>
                    <li>Satu akun hanya berlaku untuk satu calon siswa pendaftar.</li>
                </ul>
            </div>
        </div>

        {{-- Step 2 --}}
        <div class="step-card">
            <div class="step-badge">2</div>
            <div class="step-content">
                <h3>Mengisi Biodata &amp; Upload Dokumen</h3>
                <p>
                    Setelah memiliki akun, silakan masuk ke dashboard siswa. Isi semua formulir pendaftaran meliputi data diri, data orang tua, data sekolah asal, nilai rapor semester 1-5, dan pilih jurusan keahlian yang diminati.
                </p>
                <ul>
                    <li>Unggah scan/foto KK, Akta Kelahiran, dan SKL/Ijazah pada kolom upload berkas.</li>
                    <li>Unggah pas foto formal berwarna ukuran 3x4.</li>
                    <li>Pastikan semua berkas terunggah lengkap untuk mempercepat verifikasi oleh admin.</li>
                </ul>
            </div>
        </div>

        {{-- Step 3 --}}
        <div class="step-card">
            <div class="step-badge">3</div>
            <div class="step-content">
                <h3>Verifikasi Berkas &amp; Mengikuti Ujian Seleksi</h3>
                <p>
                    Tim panitia PPDB akan memverifikasi berkas pendaftaran Anda. Setelah berkas dinyatakan valid, Anda akan mendapatkan kartu ujian dan dapat mengerjakan Ujian Online CBT (Computer Based Test) melalui menu Ujian di dashboard siswa.
                </p>
                <ul>
                    <li>Ujian seleksi mencakup Tes Potensi Akademik, Matematika Dasar, Bahasa Inggris, dan Tes Minat Bakat.</li>
                    <li>Ujian dapat dikerjakan secara mandiri dari rumah menggunakan perangkat HP/Laptop.</li>
                    <li>Skor ujian akan langsung tercatat di sistem seleksi sekolah.</li>
                </ul>
            </div>
        </div>

        {{-- Step 4 --}}
        <div class="step-card" style="border-color:#bbf7d0;background:#f0fdf4;">
            <div class="step-badge" style="background:#ecfdf5;color:#059669;border-color:#a7f3d0;">4</div>
            <div class="step-content">
                <h3 style="color:#065f46;">Pengumuman Seleksi &amp; Daftar Ulang</h3>
                <p style="color:#1e3a8a;">
                    Pantau menu Hasil Kelulusan pada dashboard akun Anda untuk melihat pengumuman kelulusan sesuai tanggal pengumuman.
                </p>
                <ul style="color:#047857;">
                    <li>Peserta didik yang dinyatakan <strong>DITERIMA</strong> wajib mendownload Surat Kelulusan resmi.</li>
                    <li>Melakukan konfirmasi daftar ulang dan penyelesaian administrasi daftar ulang (biaya seragam, dll).</li>
                    <li>Menyerahkan berkas fisik asli ke sekolah pada saat masa awal orientasi siswa baru.</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Info Bantuan Desk --}}
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:18px;padding:2rem;margin-top:3rem;display:flex;gap:1.5rem;align-items:center;flex-wrap:wrap;">
        <div style="width:48px;height:48px;background:#1d4ed8;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.35rem;flex-shrink:0;">
            <i class="fa-solid fa-headset"></i>
        </div>
        <div style="flex:1;">
            <h3 style="font-size:1.05rem;font-weight:800;color:#1e40af;margin:0 0 .3rem;">Mengalami kendala teknologi atau tidak memiliki HP/Laptop untuk mendaftar?</h3>
            <p style="color:#1e40af;font-size:.88rem;line-height:1.5;margin:0;">
                PPDB dilakukan secara online. Bagi calon siswa atau orang tua yang mengalami kendala dalam proses pendaftaran, dapat datang ke <strong>Ruang PPDB</strong> untuk mendapatkan bantuan dari petugas. Jelaskan kedatangan ke sekolah merupakan layanan bantuan pendaftaran online, bukan jalur pendaftaran offline tersendiri.
            </p>
        </div>
    </div>
</div>
@endsection
