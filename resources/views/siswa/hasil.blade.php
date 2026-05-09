@extends('layouts.siswa')
@section('title', 'Pengumuman Hasil Seleksi')

@section('content')
<style>
    /* Styling for the surat kelulusan - hidden by default */
    #surat-kelulusan-container {
        display: none;
    }
    
    /* When viewing the modal */
    .modal-surat {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.8);
        padding-top: 50px;
    }
    
    .modal-content-surat {
        background-color: #fff;
        margin: auto;
        padding: 40px;
        border: 1px solid #888;
        width: 80%;
        max-width: 800px;
        color: black;
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
    }

    .close-modal {
        color: #aaaaaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close-modal:hover {
        color: #000;
    }

    /* Print styling */
    @media print {
        body * {
            visibility: hidden;
        }
        #surat-kelulusan-template, #surat-kelulusan-template * {
            visibility: visible;
        }
        #surat-kelulusan-template {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 0;
            margin: 0;
        }
    }
</style>

<div style="max-width: 700px; margin: 0 auto; text-align: center;" id="main-content-hasil">

    @if(!$hasil || $pendaftaran->status !== 'siap_diumumkan')
        <div class="glass-card" style="padding: 4rem 2rem;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">⏳</div>
            <h2 style="color: var(--dark); margin-bottom: 1rem;">Belum Ada Pengumuman</h2>
            <p style="color: var(--gray-text);">Sistem sedang dalam proses seleksi. Pengumuman kelulusan akan ditampilkan pada halaman ini setelah Admin PPDB melakukan finalisasi hasil seleksi.</p>
            <p style="color: var(--gray-text); margin-top: 1rem;">Status Anda saat ini: <strong style="text-transform: uppercase;">{{ str_replace('_', ' ', $pendaftaran->status) }}</strong></p>
        </div>
    @else
        @php $kategori = $hasil->kategori_kelulusan; @endphp

        @if($kategori === 'Unggulan')
            <div class="glass-card" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 4rem 2rem;">
                <div style="font-size: 5rem; margin-bottom: 1rem;">⭐</div>
                <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">SELAMAT!</h1>
                <p style="font-size: 1.25rem; opacity: 0.95; margin-bottom: 1.5rem;">Anda dinyatakan <strong style="font-size: 1.6rem;">DITERIMA</strong> di jurusan <strong>{{ $pendaftaran->jurusan->nama }}</strong>.</p>
                <div style="background: rgba(255,255,255,0.2); padding: 1.25rem 2rem; border-radius: var(--radius-md); display: inline-block; text-align: left;">
                    <p style="margin-bottom: 0.4rem; font-size: 1.1rem;">Kategori: <strong>⭐ Kelas Unggulan</strong></p>
                    <p style="margin-bottom: 0.4rem;">Skor Akhir: <strong>{{ $hasil->skor_akhir }}</strong></p>
                    <p>Ranking: <strong>#{{ $hasil->ranking }}</strong></p>
                </div>
            </div>

        @elseif($kategori === 'Reguler')
            <div class="glass-card" style="background: linear-gradient(135deg, #059669, #10b981); color: white; padding: 4rem 2rem;">
                <div style="font-size: 5rem; margin-bottom: 1rem;">🎉</div>
                <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">SELAMAT!</h1>
                <p style="font-size: 1.25rem; opacity: 0.95; margin-bottom: 1.5rem;">Anda dinyatakan <strong style="font-size: 1.6rem;">DITERIMA</strong> di jurusan <strong>{{ $pendaftaran->jurusan->nama }}</strong>.</p>
                <div style="background: rgba(255,255,255,0.2); padding: 1.25rem 2rem; border-radius: var(--radius-md); display: inline-block; text-align: left;">
                    <p style="margin-bottom: 0.4rem; font-size: 1.1rem;">Kategori: <strong>✅ Kelas Reguler</strong></p>
                    <p style="margin-bottom: 0.4rem;">Skor Akhir: <strong>{{ $hasil->skor_akhir }}</strong></p>
                    <p>Ranking: <strong>#{{ $hasil->ranking }}</strong></p>
                </div>
            </div>

        @else
            <div class="glass-card" style="background: linear-gradient(135deg, #dc2626, #ef4444); color: white; padding: 4rem 2rem;">
                <div style="font-size: 5rem; margin-bottom: 1rem;">😔</div>
                <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">MOHON MAAF</h1>
                <p style="font-size: 1.25rem; opacity: 0.9; margin-bottom: 1.5rem;">Anda dinyatakan <strong style="font-size: 1.6rem;">TIDAK LULUS</strong> seleksi PPDB.</p>
                <div style="background: rgba(255,255,255,0.2); padding: 1.25rem 2rem; border-radius: var(--radius-md); display: inline-block; text-align: left;">
                    <p style="margin-bottom: 0.4rem;">Skor Akhir: <strong>{{ $hasil->skor_akhir }}</strong></p>
                    <p>Ranking: <strong>#{{ $hasil->ranking }}</strong></p>
                </div>
                <p style="margin-top: 2rem; opacity: 0.85; font-size: 1rem;">Tetap semangat dan terus berjuang! 💪</p>
            </div>
        @endif

        @if($kategori !== 'Tidak Lulus')
        <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: center;">
            <button class="btn-outline" onclick="openModal()" style="font-size: 1.125rem;">📄 Lihat Surat Kelulusan</button>
            <button class="btn-primary" onclick="downloadPDF()" style="font-size: 1.125rem;">⬇️ Download PDF</button>
        </div>

        {{-- TEMPLATE SURAT KELULUSAN --}}
        <div id="surat-kelulusan-container">
            <div id="surat-kelulusan-template" style="background: white; padding: 40px; font-family: 'Times New Roman', Times, serif; color: black; text-align: left; line-height: 1.6;">
                <div style="text-align: center; border-bottom: 3px solid black; padding-bottom: 20px; margin-bottom: 30px; display: flex; align-items: center; justify-content: center; gap: 20px;">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9e/Tut_Wuri_Handayani.svg/120px-Tut_Wuri_Handayani.svg.png" alt="Logo Sekolah" style="width: 80px; height: 80px; object-fit: contain;">
                    <div>
                        <h2 style="margin: 0; font-size: 24px; text-transform: uppercase; font-weight: bold;">PANITIA PENERIMAAN PESERTA DIDIK BARU</h2>
                        <h1 style="margin: 5px 0; font-size: 28px; text-transform: uppercase; font-weight: bold;">SEKOLAH MASA DEPAN GEMILANG</h1>
                        <p style="margin: 0; font-size: 14px;">Jl. Pendidikan No. 123, Jakarta | Telp: (021) 1234567</p>
                    </div>
                </div>
                <div style="text-align: center; margin-bottom: 30px;">
                    <h3 style="margin: 0; text-decoration: underline;">SURAT KEPUTUSAN KELULUSAN</h3>
                    <p style="margin: 5px 0;">Nomor: {{ sprintf('%03d', $pendaftaran->id) }}/PPDB/{{ date('Y') }}</p>
                </div>
                <p>Berdasarkan hasil seleksi administrasi dan ujian masuk PPDB Tahun Ajaran {{ date('Y') }}, maka:</p>
                <table style="width: 100%; margin: 20px 0; border-collapse: collapse; margin-left: 20px;">
                    <tr><td style="width: 25%; padding: 5px 0;">Nama Lengkap</td><td style="width: 5%;">:</td><td style="font-weight: bold;">{{ Auth::user()->name }}</td></tr>
                    <tr><td style="padding: 5px 0;">NISN</td><td>:</td><td style="font-weight: bold;">{{ $pendaftaran->nisn }}</td></tr>
                    <tr><td style="padding: 5px 0;">Dinyatakan</td><td>:</td><td style="font-weight: bold; font-size: 18px; color: #059669;">LULUS</td></tr>
                    <tr><td style="padding: 5px 0;">Pada Jurusan</td><td>:</td><td style="font-weight: bold;">{{ $pendaftaran->jurusan->nama }}</td></tr>
                    <tr><td style="padding: 5px 0;">Kategori Kelas</td><td>:</td><td style="font-weight: bold;">{{ $hasil->kategori_kelulusan }}</td></tr>
                    <tr><td style="padding: 5px 0;">Skor Akhir</td><td>:</td><td style="font-weight: bold;">{{ $hasil->skor_akhir }}</td></tr>
                </table>
                <p>Dimohon hadir untuk daftar ulang pada tanggal <strong>{{ date('d F Y', strtotime('+7 days')) }}</strong> dengan membawa berkas asli.</p>
                <div style="margin-top: 50px; display: flex; justify-content: flex-end;">
                    <div style="text-align: center; width: 300px;">
                        <p style="margin-bottom: 5px;">Jakarta, {{ date('d F Y') }}</p>
                        <p style="margin-bottom: 10px;">Kepala Sekolah</p>
                        <div style="font-family: 'Brush Script MT', cursive; font-size: 32px; color: #1a365d; transform: rotate(-5deg); margin: 15px 0;">Dr. H. Pendidikan</div>
                        <p style="font-weight: bold; text-decoration: underline; margin-bottom: 0;">Dr. H. Pendidikan, M.Pd.</p>
                        <p style="margin-top: 0;">NIP. 19800101 200501 1 001</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal --}}
        <div id="modalSurat" class="modal-surat">
            <div class="modal-content-surat">
                <span class="close-modal" onclick="closeModal()">&times;</span>
                <div id="modal-body-surat" style="margin-bottom: 20px; border: 1px solid #ccc;"></div>
                <div style="text-align: center; margin-top: 30px;">
                    <button class="btn-primary" onclick="downloadPDF()" style="padding: 10px 20px; margin-right: 10px;">Download PDF</button>
                    <button class="btn-outline" onclick="closeModal()" style="padding: 10px 20px;">Tutup</button>
                </div>
            </div>
        </div>
        @endif
    @endif


</div>

<!-- Include html2pdf.js for Client-Side PDF Generation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function openModal() {
        var modal = document.getElementById("modalSurat");
        var template = document.getElementById("surat-kelulusan-template").cloneNode(true);
        template.id = "cloned-template";
        
        var modalBody = document.getElementById("modal-body-surat");
        modalBody.innerHTML = '';
        modalBody.appendChild(template);
        
        modal.style.display = "block";
    }

    function closeModal() {
        document.getElementById("modalSurat").style.display = "none";
    }

    function downloadPDF() {
        var element = document.getElementById("surat-kelulusan-template");
        
        var opt = {
            margin:       [10, 10, 10, 10],
            filename:     'Surat_Kelulusan_{{ Auth::user()->name }}.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        var tempDiv = document.createElement('div');
        tempDiv.appendChild(element.cloneNode(true));
        tempDiv.style.display = 'block';
        tempDiv.style.width = '800px';
        tempDiv.style.padding = '40px';
        tempDiv.style.background = 'white';
        
        html2pdf().set(opt).from(tempDiv).save();
    }
</script>
@endsection
