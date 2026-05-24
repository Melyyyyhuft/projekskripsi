<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Hasil Seleksi PPDB</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #000; padding-bottom: 20px; margin-bottom: 30px; }
        .school-name { font-size: 24px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .school-info { font-size: 12px; margin: 5px 0; }
        
        .title { text-align: center; text-decoration: underline; font-size: 18px; font-weight: bold; margin-bottom: 30px; }
        
        .content { font-size: 14px; margin-bottom: 20px; }
        .table-data { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .table-data td { padding: 8px 0; vertical-align: top; }
        .label { width: 180px; font-weight: bold; }
        
        .score-box { background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .score-table { width: 100%; border-collapse: collapse; }
        .score-table th, .score-table td { padding: 10px; border: 1px solid #e2e8f0; text-align: center; }
        .score-table th { background: #f1f5f9; font-size: 12px; text-transform: uppercase; }
        
        .result { text-align: center; margin: 30px 0; padding: 20px; border: 2px solid #059669; border-radius: 10px; }
        .status { font-size: 22px; font-weight: bold; color: #059669; }
        .placement { font-size: 18px; font-weight: bold; margin-top: 10px; }
        
        .footer { margin-top: 50px; }
        .signature { float: right; width: 250px; text-align: center; }
        .sign-space { height: 80px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="school-name">{{ $settings['nama_sekolah'] ?? 'PANITIA PPDB ONLINE' }}</h1>
        <p class="school-info">Tahun Pelajaran {{ $settings['tahun_ajaran'] ?? '2026/2027' }}</p>
        <p class="school-info">Alamat: {{ $settings['alamat_sekolah'] ?? 'Alamat Sekolah Belum Diatur' }}</p>
    </div>

    <h2 class="title">SURAT PENGUMUMAN HASIL SELEKSI</h2>

    <div class="content">
        <p>Berdasarkan hasil seleksi Penerimaan Peserta Didik Baru (PPDB) yang telah dilaksanakan, Panitia Penerimaan Peserta Didik Baru dengan ini menyatakan bahwa:</p>
    </div>

    <table class="table-data">
        <tr>
            <td class="label">Nama Lengkap</td>
            <td>: {{ $pendaftaran->user->name }}</td>
        </tr>
        <tr>
            <td class="label">Nomor Pendaftaran</td>
            <td>: {{ $pendaftaran->nomor_pendaftaran }}</td>
        </tr>
        <tr>
            <td class="label">NISN</td>
            <td>: {{ $pendaftaran->nisn }}</td>
        </tr>
        <tr>
            <td class="label">Jurusan</td>
            <td>: {{ $pendaftaran->jurusan->nama }}</td>
        </tr>
    </table>

    <p style="font-weight: bold; margin-bottom: 10px;">Ringkasan Penilaian:</p>
    <div class="score-box">
        <table class="score-table">
            <thead>
                <tr>
                    <th>Nilai Rapor</th>
                    <th>Nilai CBT</th>
                    <th>Bonus Sertifikat</th>
                    <th>Skor Akhir</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ number_format($pendaftaran->nilai_rapor, 1) }}</td>
                    <td>{{ number_format($pendaftaran->hasilUjian->skor ?? 0, 1) }}</td>
                    <td>+{{ number_format($hasil->bonus_sertifikat, 1) }}</td>
                    <td style="font-weight: bold;">{{ number_format($hasil->skor_akhir, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="result">
        <div class="status">DINYATAKAN: DITERIMA</div>
        <div class="placement">Penempatan Kelas: {{ $hasil->penempatan_kelas }}</div>
    </div>

    <div class="content">
        <p>Demikian surat pengumuman ini disampaikan. Calon siswa yang dinyatakan <strong>Diterima</strong> diwajibkan melakukan daftar ulang sesuai dengan jadwal dan ketentuan yang berlaku.</p>
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }}</p>
        <div class="signature">
            <p>Ketua Panitia PPDB,</p>
            <div class="sign-space"></div>
            <p><strong>( ____________________ )</strong></p>
            <p>NIP. .............................</p>
        </div>
    </div>
</body>
</html>
